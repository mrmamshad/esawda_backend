@extends('emails.layouts.email')

@section('content')
    <h1>Shipping update — {{ ucfirst($status) }}</h1>
    <p>Hi <strong>{{ $buyer->name ?: $buyer->username }}</strong>,</p>
    <p>Your order for <strong>{{ $product->product_name }}</strong> has a new status: <strong>{{ ucfirst($status) }}</strong>.</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Status</td>
                <td class="value">{{ ucfirst($status) }}</td>
            </tr>
            @if($order->courier_name)
            <tr>
                <td class="label">Courier</td>
                <td class="value">{{ $order->courier_name }}</td>
            </tr>
            @endif
            @if($order->tracking_no)
            <tr>
                <td class="label">Tracking number</td>
                <td class="value">{{ $order->tracking_no }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Order ID</td>
                <td class="value">#{{ $order->id }}</td>
            </tr>
        </table>
    </div>

    <a class="btn" href="{{ $orderUrl }}">View order</a>
    <p style="color:#64748b; font-size:13px;">You will be notified again when the status changes.</p>
@endsection