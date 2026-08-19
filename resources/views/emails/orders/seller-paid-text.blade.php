{{ $subject ?? '' }}

Hi {{ $seller->name ?: $seller->username }},

The payment for your order has been released.

Product: {{ $product->product_name }}
Amount released: {{ $amount }}
Order ID: #{{ $order->id }}

View order: {{ $orderUrl }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
