<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Cash-on-delivery orders.
 *
 *   POST  /api/v1/ads/{id}/order        — anyone (guest included) places an
 *                                         order with name / phone / address.
 *                                         No payment: the shop confirms and
 *                                         delivers, tracking the state in
 *                                         /shop/orders.
 *   PATCH /api/v1/me/orders/{id}/status — seller moves their own order
 *                                         through pending → confirmed →
 *                                         delivered / cancelled.
 */
class OrderController extends Controller
{
    private const LIFECYCLE = ['pending', 'confirmed', 'delivered', 'cancelled'];

    public function store(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'phone' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $post = Post::query()->with('user')->active()->where('hide', '0')->find($id);
        if (!$post) {
            return $this->error('NOT_FOUND', 'This product is not available.', 404);
        }

        $seller = $post->user;
        if (!$seller || !$seller->isShop()) {
            return $this->error('NO_ORDERS', 'This seller does not accept orders.', 422);
        }

        $amount = (int) ($post->price ?? 0);
        if ($amount <= 0) {
            return $this->error('NOT_FOR_SALE', 'This product is not available for order.', 422);
        }

        $buyer = $request->user();
        if ($buyer && (int) $buyer->id === (int) $post->user_id) {
            return $this->error('OWN_PRODUCT', 'You cannot order your own product.', 422);
        }

        // One order per product per hour per IP — cheap spam brake for a
        // public, unauthenticated endpoint.
        $throttleKey = "cod-order:{$post->id}:".$request->ip();
        if (!Cache::add($throttleKey, 1, 3600)) {
            return $this->error('TOO_MANY_ORDERS', 'You already ordered this product recently. Please try again later.', 429);
        }

        $order = Order::create([
            'product_id' => $post->id,
            'buyer_id' => $buyer?->id,
            'buyer_name' => trim($data['name']),
            'buyer_phone' => trim($data['phone']),
            'buyer_address' => trim($data['address']),
            'seller_id' => $post->user_id,
            'amount' => $amount,
            'shipping_status' => 'pending',
        ]);

        return $this->created([
            'id' => (int) $order->id,
            'status' => $order->shipping_status,
            'message' => 'Order placed — the shop will contact you to confirm.',
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'shipping_status' => ['required', 'in:'.implode(',', self::LIFECYCLE)],
        ]);

        $order = Order::query()
            ->where('id', $id)
            ->where('seller_id', $request->user()->id)
            ->first();
        if (!$order) {
            return $this->error('NOT_FOUND', 'Order not found.', 404);
        }

        $order->forceFill(['shipping_status' => $data['shipping_status']])->save();

        return $this->ok([
            'id' => (int) $order->id,
            'status' => $order->shipping_status,
        ]);
    }
}
