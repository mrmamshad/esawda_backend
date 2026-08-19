{{ $subject ?? '' }}

Hi {{ $buyer->name ?: $buyer->username }},

Your order for {{ $product->product_name }} has a new status: {{ ucfirst($status) }}.
@if($order->courier_name)
Courier: {{ $order->courier_name }}
@endif
@if($order->tracking_no)
Tracking number: {{ $order->tracking_no }}
@endif

View order: {{ $orderUrl }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
