{{ $subject ?? '' }}

Hi {{ $buyer->name ?: $buyer->username }},

Your payment was successful and your order is confirmed.

Product: {{ $product->product_name }}
Seller: {{ $seller->name ?: $seller->username }}
Paid: {{ $amount }}
Order ID: #{{ $order->id }}

Track your order: {{ $orderUrl }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
