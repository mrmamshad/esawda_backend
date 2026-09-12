<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\Mail\MailService;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function __construct(private readonly MailService $mail) {}

    public function index(Request $request)
    {
        $q = Order::query()
            ->with(['product:id,product_name,slug,price', 'buyer:id,username,name,email', 'seller:id,username,name,email', 'transaction:id,status']);

        if ($s = $request->query('status')) {
            $q->where('shipping_status', $s);
        }
        if ($b = $request->query('buyer_id')) {
            $q->where('buyer_id', $b);
        }
        if ($p = $request->query('paid')) {
            $q->where('seller_paid', filter_var($p, FILTER_VALIDATE_BOOL));
        }

        $q->orderByDesc('id');

        return $this->ok($q->paginate((int) min(100, max(1, (int) $request->query('per_page', 20)))));
    }

    public function show(int $id)
    {
        return $this->ok(Order::with(['product', 'buyer', 'seller', 'transaction'])->findOrFail($id));
    }

    /**
     * GET /admin/orders/summary
     *
     * Per-shop order rollup: how many orders each shop has received and
     * the total value, broken down by the COD lifecycle statuses.
     */
    public function summary(Request $request)
    {
        $rows = Order::query()
            ->selectRaw('seller_id, COUNT(*) as orders, COALESCE(SUM(amount),0) as total_amount')
            ->selectRaw("SUM(CASE WHEN shipping_status = 'pending'   THEN 1 ELSE 0 END) as pending_count")
            ->selectRaw("SUM(CASE WHEN shipping_status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_count")
            ->selectRaw("SUM(CASE WHEN shipping_status = 'delivered' THEN 1 ELSE 0 END) as delivered_count")
            ->selectRaw("SUM(CASE WHEN shipping_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count")
            ->groupBy('seller_id')
            ->orderByDesc('orders')
            ->paginate((int) min(100, max(1, (int) $request->query('per_page', 20))));

        $ids = collect($rows->items())->pluck('seller_id')->all();
        $shops = User::query()
            ->whereIn('id', $ids)
            ->get(['id', 'username', 'name', 'shop_name'])
            ->keyBy('id');

        $rows->getCollection()->transform(function ($row) use ($shops) {
            $shop = $shops->get($row->seller_id);

            return [
                'seller_id' => (int) $row->seller_id,
                'shop_name' => $shop?->shop_name ?: $shop?->name,
                'username' => $shop?->username,
                'orders' => (int) $row->orders,
                'total_amount' => (float) $row->total_amount,
                'pending_count' => (int) $row->pending_count,
                'confirmed_count' => (int) $row->confirmed_count,
                'delivered_count' => (int) $row->delivered_count,
                'cancelled_count' => (int) $row->cancelled_count,
            ];
        });

        return $this->ok($rows);
    }

    /** PATCH /admin/orders/{id}  body: { shipping_status?, courier_name?, tracking_no?, seller_paid? } */
    public function update(int $id, Request $request)
    {
        $data = $request->validate([
            'shipping_status' => ['sometimes', 'in:pending,confirmed,processing,shipped,delivered,cancelled'],
            'courier_name' => ['nullable', 'string', 'max:100'],
            'tracking_no' => ['nullable', 'string', 'max:100'],
            'seller_paid' => ['sometimes', 'boolean'],
        ]);

        $order = Order::findOrFail($id);
        $oldShipping = $order->shipping_status;
        $oldPaid = (bool) $order->seller_paid;

        $order->forceFill([
            'shipping_status' => $data['shipping_status'] ?? $order->shipping_status,
            'courier_name' => array_key_exists('courier_name', $data) ? ($data['courier_name'] ?? null) : $order->courier_name,
            'tracking_no' => array_key_exists('tracking_no', $data) ? ($data['tracking_no'] ?? null) : $order->tracking_no,
            'seller_paid' => array_key_exists('seller_paid', $data) ? (bool) $data['seller_paid'] : $order->seller_paid,
            'updated_at' => now(),
        ])->save();

        $order->load(['buyer', 'seller', 'product']);

        if (array_key_exists('shipping_status', $data) && $data['shipping_status'] !== $oldShipping) {
            $this->mail->shippingUpdateToBuyer($order);
        }

        if (array_key_exists('seller_paid', $data) && (bool) $data['seller_paid'] && !$oldPaid) {
            $this->mail->sellerPaidToSeller($order);
        }

        return $this->ok(Order::with(['product', 'buyer', 'seller', 'transaction'])->findOrFail($id));
    }
}
