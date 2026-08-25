<?php

namespace Tests\Feature;

use App\Jobs\FulfilTransactionJob;
use App\Mail\Transactional;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TransactionalEmailsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('catagory_main')->insert([
            'cat_id' => 1,
            'cat_order' => 1,
            'cat_name' => 'Test Category',
            'slug' => 'test-category',
            'icon' => 'test-icon',
            'picture' => 'test.jpg',
        ]);

        // The legacy transaction.status column only allowed
        // pending|success|failed|cancel; relax it so admin refunds work.
        \DB::statement('ALTER TABLE `transaction` RENAME TO transaction_tmp');
        \DB::statement('CREATE TABLE `transaction` (id INTEGER PRIMARY KEY AUTOINCREMENT, product_name TEXT, product_id INTEGER, seller_id INTEGER, amount REAL, base_amount REAL, featured TEXT DEFAULT "0", urgent TEXT DEFAULT "0", highlight TEXT DEFAULT "0", transaction_time INTEGER, status TEXT, payment_id TEXT, transaction_gatway TEXT, transaction_ip TEXT, transaction_description TEXT, transaction_method TEXT, frequency TEXT, billing TEXT, taxes_ids TEXT, plan_id INTEGER, purpose TEXT, meta TEXT, created_at DATETIME, updated_at DATETIME, fulfilled_at DATETIME)');
        \DB::statement('INSERT INTO `transaction` SELECT id, product_name, product_id, seller_id, amount, base_amount, featured, urgent, highlight, transaction_time, status, payment_id, transaction_gatway, transaction_ip, transaction_description, transaction_method, frequency, billing, taxes_ids, plan_id, purpose, meta, created_at, updated_at, fulfilled_at FROM transaction_tmp');
        \DB::statement('DROP TABLE transaction_tmp');
    }

    private function user(array $overrides = []): User
    {
        return User::factory()->create($overrides);
    }

    private function activePost(int $sellerId, array $overrides = []): Post
    {
        return Post::create(array_merge([
            'user_id' => $sellerId,
            'product_name' => 'Test Product',
            'description' => 'desc',
            'price' => 1500,
            'category' => 1,
            'condition' => 'new',
            'status' => 'active',
            'hide' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    private function makeOrder(Post $post, User $buyer, User $seller): Order
    {
        return Order::create([
            'product_id' => $post->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'transaction_id' => null,
            'amount' => $post->price,
            'shipping_status' => 'processing',
            'seller_paid' => false,
        ]);
    }

    public function test_seller_gets_new_order_email_on_purchase_fulfilment(): void
    {
        Mail::fake();

        $seller = $this->user(['email' => 'seller@example.com', 'name' => 'Seller One']);
        $buyer = $this->user(['email' => 'buyer@example.com', 'name' => 'Buyer One']);
        $post = $this->activePost($seller->id);

        $tx = Transaction::create([
            'seller_id' => $buyer->id,
            'product_id' => $post->id,
            'amount' => $post->price,
            'status' => 'success',
            'purpose' => 'product_purchase',
            'transaction_gatway' => 'sslcommerz',
            'meta' => json_encode(['buyer_id' => $buyer->id, 'seller_id' => $seller->id]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FulfilTransactionJob::dispatchSync($tx->id);

        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('seller@example.com')
            && str_contains($mail->envelope()->subject, 'New order')
        );
        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('buyer@example.com')
            && str_contains($mail->envelope()->subject, 'Payment received')
        );
    }

    public function test_admin_gets_transaction_notice_on_purchase(): void
    {
        Mail::fake();
        config(['quickad.admin_email' => 'admin@esawda.com']);

        $seller = $this->user(['email' => 'seller@example.com']);
        $buyer = $this->user(['email' => 'buyer@example.com']);
        $post = $this->activePost($seller->id);

        $tx = Transaction::create([
            'seller_id' => $buyer->id,
            'product_id' => $post->id,
            'amount' => $post->price,
            'status' => 'success',
            'purpose' => 'product_purchase',
            'transaction_gatway' => 'sslcommerz',
            'meta' => json_encode(['buyer_id' => $buyer->id, 'seller_id' => $seller->id]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FulfilTransactionJob::dispatchSync($tx->id);

        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('admin@esawda.com')
            && str_contains($mail->envelope()->subject, 'Transaction #'.$tx->id)
        );
    }

    public function test_ad_approval_notifies_owner(): void
    {
        Mail::fake();

        $seller = $this->user(['email' => 'seller@example.com', 'name' => 'Seller']);
        $post = $this->activePost($seller->id, ['status' => 'pending']);

        $this->actingAs($this->user(['user_type' => 'admin']))
            ->postJson("/api/v1/admin/ads/{$post->id}/approve")
            ->assertOk();

        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('seller@example.com')
            && str_contains($mail->envelope()->subject, 'Your ad is now live')
        );
    }

    public function test_ad_rejection_notifies_owner_with_reason(): void
    {
        Mail::fake();

        $seller = $this->user(['email' => 'seller@example.com']);
        $post = $this->activePost($seller->id, ['status' => 'pending']);

        $this->actingAs($this->user(['user_type' => 'admin']))
            ->postJson("/api/v1/admin/ads/{$post->id}/reject", ['reason' => 'Inappropriate content'])
            ->assertOk();

        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('seller@example.com')
            && str_contains($mail->envelope()->subject, 'not approved')
        );
    }

    public function test_new_ad_pending_notifies_admin(): void
    {
        Mail::fake();
        config(['quickad.admin_email' => 'admin@esawda.com']);

        $seller = $this->user(['email' => 'seller@example.com', 'plan_expires_at' => now()->addMonth(), 'ads_remaining' => 5]);
        $post = $this->activePost($seller->id, ['status' => 'pending']);

        $this->actingAs($seller)
            ->postJson('/api/v1/ads', [
                'title' => 'New Pending Ad',
                'description' => 'A brand new ad awaiting review',
                'price' => 500,
                'category' => 1,
                'condition' => 'new',
            ])
            ->assertCreated();

        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('admin@esawda.com')
            && str_contains($mail->envelope()->subject, 'New ad pending review')
        );
    }

    public function test_shop_apply_notifies_seller_and_admin(): void
    {
        Mail::fake();
        config(['quickad.admin_email' => 'admin@esawda.com']);

        $seller = $this->user(['email' => 'seller@example.com', 'name' => 'New Seller']);

        $this->actingAs($seller)
            ->post('/api/v1/me/shop/apply', [
                'owner_name' => 'New Seller',
                'owner_phone' => '01700000000',
                'shop_name' => 'My Shop',
                'shop_address' => 'Dhaka',
                'documents' => [
                    UploadedFile::fake()->create('doc.pdf', 10),
                ],
            ])
            ->assertOk();

        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('seller@example.com')
            && str_contains($mail->envelope()->subject, 'Welcome to the eSawda shop')
        );
        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('admin@esawda.com')
            && str_contains($mail->envelope()->subject, 'New shop opened')
        );
    }

    public function test_shipping_update_notifies_buyer(): void
    {
        Mail::fake();

        $seller = $this->user(['email' => 'seller@example.com']);
        $buyer = $this->user(['email' => 'buyer@example.com']);
        $post = $this->activePost($seller->id);
        $order = $this->makeOrder($post, $buyer, $seller);

        $this->actingAs($this->user(['user_type' => 'admin']))
            ->patchJson("/api/v1/admin/orders/{$order->id}", [
                'shipping_status' => 'shipped',
                'courier_name' => 'Steadfast',
                'tracking_no' => 'ST123',
            ])
            ->assertOk();

        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('buyer@example.com')
            && str_contains($mail->envelope()->subject, 'SHIPPED')
        );
    }

    public function test_seller_paid_notifies_seller(): void
    {
        Mail::fake();

        $seller = $this->user(['email' => 'seller@example.com']);
        $buyer = $this->user(['email' => 'buyer@example.com']);
        $post = $this->activePost($seller->id);
        $order = $this->makeOrder($post, $buyer, $seller);

        $this->actingAs($this->user(['user_type' => 'admin']))
            ->patchJson("/api/v1/admin/orders/{$order->id}", ['seller_paid' => true])
            ->assertOk();

        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('seller@example.com')
            && str_contains($mail->envelope()->subject, 'Payment released')
        );
    }

    public function test_refund_notifies_buyer(): void
    {
        Mail::fake();

        $seller = $this->user(['email' => 'seller@example.com']);
        $buyer = $this->user(['email' => 'buyer@example.com']);
        $post = $this->activePost($seller->id);

        $tx = Transaction::create([
            'seller_id' => $buyer->id,
            'product_id' => $post->id,
            'amount' => $post->price,
            'status' => 'success',
            'purpose' => 'product_purchase',
            'transaction_gatway' => 'sslcommerz',
            'meta' => json_encode(['buyer_id' => $buyer->id, 'seller_id' => $seller->id]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $order = $this->makeOrder($post, $buyer, $seller);
        $order->forceFill(['transaction_id' => $tx->id])->save();

        $this->actingAs($this->user(['user_type' => 'admin']))
            ->postJson("/api/v1/admin/transactions/{$tx->id}/refund")
            ->assertOk();

        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('buyer@example.com')
            && str_contains($mail->envelope()->subject, 'Refund issued')
        );
    }

    public function test_password_reset_sends_html_email(): void
    {
        Mail::fake();

        $user = $this->user(['email' => 'user@example.com', 'name' => 'Test User']);

        $this->postJson('/api/v1/auth/forgot', ['email' => $user->email])
            ->assertOk();

        Mail::assertQueued(Transactional::class, function (Mailable $mail) {
            if (!$mail->hasTo('user@example.com') || !str_contains($mail->envelope()->subject, 'Reset your eSawda password')) {
                return false;
            }
            $html = $mail->render();

            return str_contains($html, 'Reset your password')
                && str_contains($html, '/auth/reset?token=');
        });
    }

    public function test_contact_form_notifies_admin(): void
    {
        Mail::fake();
        config(['quickad.admin_email' => 'admin@esawda.com']);

        $this->postJson('/api/v1/contact', [
            'name' => 'Visitor',
            'email' => 'visitor@example.com',
            'subject' => 'Need help',
            'message' => 'Hello, I need assistance.',
        ])->assertOk();

        Mail::assertQueued(Transactional::class, function (Mailable $mail) {
            if (!$mail->hasTo('admin@esawda.com') || !str_contains($mail->envelope()->subject, 'Need help')) {
                return false;
            }

            return str_contains($mail->render(), 'Hello, I need assistance.');
        });
    }

    public function test_plan_activation_notifies_user(): void
    {
        Mail::fake();

        $user = $this->user(['email' => 'user@example.com', 'group_id' => 'gold']);

        $plan = Plan::create([
            'name' => 'Gold',
            'monthly_price' => 500,
            'annual_price' => 5000,
            'settings' => json_encode(['ads_limit' => 10]),
            'status' => 1,
            'date' => now(),
        ]);

        $tx = Transaction::create([
            'seller_id' => $user->id,
            'product_id' => 0,
            'plan_id' => $plan->id,
            'amount' => 500,
            'status' => 'success',
            'purpose' => 'plan',
            'transaction_gatway' => 'sslcommerz',
            'meta' => json_encode(['cadence' => 'monthly']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        FulfilTransactionJob::dispatchSync($tx->id);

        Mail::assertQueued(Transactional::class, fn (Mailable $mail) => $mail->hasTo('user@example.com')
            && str_contains($mail->envelope()->subject, 'plan is active')
        );
    }
}
