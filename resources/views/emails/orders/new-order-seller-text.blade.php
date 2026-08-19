{{ $subject ?? '' }}

Hi {{ $seller->name ?: $seller->username }},

You have a new order from {{ $buyer->name ?: $buyer->username }}.

Product: {{ $product->product_name }}
Order total: {{ $amount }}
Status: {{ ucfirst($order->shipping_status) }}
Order ID: #{{ $order->id }}

View order: {{ $orderUrl }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
