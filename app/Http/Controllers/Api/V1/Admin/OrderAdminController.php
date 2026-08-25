<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

    /** PATCH /admin/orders/{id}  body: { shipping_status?, courier_name?, tracking_no?, seller_paid? } */
    public function update(int $id, Request $request)
    {
        $data = $request->validate([
            'shipping_status' => ['sometimes', 'in:pending,processing,shipped,delivered,cancelled'],
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
