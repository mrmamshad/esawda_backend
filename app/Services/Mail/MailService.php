<?php

namespace App\Services\Mail;

use App\Mail\Transactional;
use App\Models\Order;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

/**
 * Central dispatch for every transactional email the platform sends.
 *
 * Each method knows the exact recipients, subject line, and Blade view
 * (under resources/views/emails/*). Emails are queued so the request
 * handler never blocks on SMTP; the queue worker picks them up.
 *
 * Pure read-only service — no state, no static caching.
 */
class MailService
{
    public function send(string $to, string $toName, string $subject, string $view, array $data = []): void
    {
        $data += [
            'subject'      => $subject,
            'frontendUrl'  => $this->frontendUrl(),
        ];

        Mail::to($to, $toName)->queue(new Transactional($subject, $view, $data));
    }

    /**
     * Send to the configured site admin (config('quickad.admin_email')).
     */
    public function sendToAdmin(string $subject, string $view, array $data = []): void
    {
        $this->send(
            (string) config('quickad.admin_email'),
            'eSawda Admin',
            $subject,
            $view,
            $data,
        );
    }

    public function frontendUrl(string $path = ''): string
    {
        $base = rtrim((string) explode(',', (string) env('FRONTEND_URLS', 'http://localhost:3000'))[0], '/');
        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }

    public function money(float|int $amount): string
    {
        return '৳' . number_format((float) $amount, 2);
    }

    /* --------------------------------------------------------------- */
    /* Order + payment events                                          */
    /* --------------------------------------------------------------- */

    /** S1 — Seller: a buyer placed a new order. */
    public function newOrderToSeller(Order $order): void
    {
        $seller = $order->seller;
        if (! $seller || ! $seller->email) {
            return;
        }

        $this->send(
            $seller->email,
            $seller->name ?: $seller->username,
            'New order received',
            'emails.orders.new-order-seller',
            [
                'seller'    => $seller,
                'order'     => $order,
                'product'   => $order->product,
                'buyer'     => $order->buyer,
                'amount'    => $this->money($order->amount),
                'orderUrl'  => $this->frontendUrl('dashboard/orders'),
            ],
        );
    }

    /** B1+B2 — Buyer: payment succeeded + receipt / invoice. */
    public function paymentSuccessToBuyer(Order $order): void
    {
        $buyer = $order->buyer;
        if (! $buyer || ! $buyer->email) {
            return;
        }

        $this->send(
            $buyer->email,
            $buyer->name ?: $buyer->username,
            'Payment received — order confirmed',
            'emails.orders.payment-success-buyer',
            [
                'buyer'     => $buyer,
                'order'     => $order,
                'product'   => $order->product,
                'seller'    => $order->seller,
                'amount'    => $this->money($order->amount),
                'orderUrl'  => $this->frontendUrl('dashboard/purchases'),
            ],
        );
    }

    /** A1 — Admin: a payment transaction completed. */
    public function transactionToAdmin(Transaction $tx, ?Order $order = null): void
    {
        $this->sendToAdmin(
            'Transaction #' . $tx->id . ' — ' . strtoupper((string) $tx->purpose),
            'emails.admin.transaction-notice',
            [
                'tx'        => $tx,
                'order'     => $order,
                'amount'    => $this->money($tx->amount),
                'customer'  => $order?->buyer ?? $tx->seller,
            ],
        );
    }

    /* --------------------------------------------------------------- */
    /* Ad moderation events                                            */
    /* --------------------------------------------------------------- */

    /** S3 — Seller: ad approved and published. */
    public function adApprovedToSeller(Post $post): void
    {
        $this->toPostOwner($post, 'Your ad is now live', 'emails.ads.ad-approved-seller', [
            'post'  => $post,
            'adUrl' => $this->frontendUrl('ads/' . $post->id . '-' . ($post->slug ?: 'ad')),
        ]);
    }

    /** S4 — Seller: ad rejected with reason. */
    public function adRejectedToSeller(Post $post, string $reason): void
    {
        $this->toPostOwner($post, 'Your ad was not approved', 'emails.ads.ad-rejected-seller', [
            'post'   => $post,
            'reason' => $reason ?: 'It did not meet our listing guidelines.',
            'adUrl'  => $this->frontendUrl('dashboard/ads'),
        ]);
    }

    /** A3 — Admin: a new ad is awaiting moderation. */
    public function pendingAdToAdmin(Post $post): void
    {
        $this->sendToAdmin('New ad pending review', 'emails.admin.new-ad-notice', [
            'post' => $post,
            'adUrl' => $this->frontendUrl('admin/ads'),
        ]);
    }

    /* --------------------------------------------------------------- */
    /* Shop events                                                      */
    /* --------------------------------------------------------------- */

    /** S6 — Seller: shop opened welcome. */
    public function shopOpenedToSeller(User $user): void
    {
        if (! $user->email) {
            return;
        }

        $this->send(
            $user->email,
            $user->name ?: $user->username,
            'Welcome to the eSawda shop',
            'emails.shop.shop-opened-seller',
            [
                'user'  => $user,
                'shopUrl' => $this->frontendUrl('dashboard'),
            ],
        );
    }

    /** A2 — Admin: a new shop/seller registered. */
    public function newShopToAdmin(User $user): void
    {
        $this->sendToAdmin('New shop opened', 'emails.admin.new-shop-notice', [
            'user' => $user,
        ]);
    }

    /* --------------------------------------------------------------- */
    /* Order lifecycle events                                          */
    /* --------------------------------------------------------------- */

    /** B5 — Buyer: shipping status changed. */
    public function shippingUpdateToBuyer(Order $order): void
    {
        $buyer = $order->buyer;
        if (! $buyer || ! $buyer->email) {
            return;
        }

        $this->send(
            $buyer->email,
            $buyer->name ?: $buyer->username,
            'Order status updated — ' . strtoupper((string) $order->shipping_status),
            'emails.orders.shipping-update-buyer',
            [
                'buyer'  => $buyer,
                'order'  => $order,
                'product'=> $order->product,
                'seller' => $order->seller,
                'status' => (string) $order->shipping_status,
                'orderUrl' => $this->frontendUrl('dashboard/purchases'),
            ],
        );
    }

    /** S7 — Seller: admin marked the order paid. */
    public function sellerPaidToSeller(Order $order): void
    {
        $seller = $order->seller;
        if (! $seller || ! $seller->email) {
            return;
        }

        $this->send(
            $seller->email,
            $seller->name ?: $seller->username,
            'Payment released for order #' . $order->id,
            'emails.orders.seller-paid',
            [
                'seller' => $seller,
                'order'  => $order,
                'product'=> $order->product,
                'amount' => $this->money($order->amount),
                'orderUrl' => $this->frontendUrl('dashboard/orders'),
            ],
        );
    }

    /** Refund — Buyer: transaction refunded. */
    public function refundedToBuyer(Transaction $tx, ?Order $order = null): void
    {
        $buyer = $order?->buyer ?? $tx->seller;
        if (! $buyer || ! $buyer->email) {
            return;
        }

        $this->send(
            $buyer->email,
            $buyer->name ?: $buyer->username,
            'Refund issued for transaction #' . $tx->id,
            'emails.transactions.refunded-buyer',
            [
                'buyer'  => $buyer,
                'tx'     => $tx,
                'amount' => $this->money($tx->amount),
            ],
        );
    }

    /** B3 — User: subscription plan activated/renewed. */
    public function planActivatedToUser(?User $user): void
    {
        if (! $user || ! $user->email) {
            return;
        }

        $this->send(
            $user->email,
            $user->name ?: $user->username,
            'Your eSawda plan is active',
            'emails.plans.plan-activated',
            [
                'user'     => $user,
                'planName' => $user->group_id ?: 'Premium',
                'expiresAt'=> optional($user->plan_expires_at)->format('d M Y'),
                'dashboardUrl' => $this->frontendUrl('dashboard'),
            ],
        );
    }

    /* --------------------------------------------------------------- */
    /* Auth / content events                                           */
    /* --------------------------------------------------------------- */

    /** B6 — User: password reset link. */
    public function passwordReset(User $user, string $resetUrl): void
    {
        if (! $user->email) {
            return;
        }

        $this->send(
            $user->email,
            $user->name ?: $user->username,
            'Reset your eSawda password',
            'emails.auth.password-reset',
            [
                'user'     => $user,
                'resetUrl' => $resetUrl,
            ],
        );
    }

    /** A4 — Admin: contact form submission. */
    public function contactMessageToAdmin(array $data): void
    {
        $data['body'] = $data['message'] ?? '';

        $this->sendToAdmin(
            'Contact form: ' . ($data['subject'] ?? '(no subject)'),
            'emails.admin.contact-notice',
            $data,
        );
    }

    /* --------------------------------------------------------------- */

    private function toPostOwner(Post $post, string $subject, string $view, array $data): void
    {
        $owner = $post->user;
        if (! $owner || ! $owner->email) {
            return;
        }

        $this->send(
            $owner->email,
            $owner->name ?: $owner->username,
            $subject,
            $view,
            $data + ['owner' => $owner],
        );
    }
}