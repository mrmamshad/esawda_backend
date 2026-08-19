@extends('emails.layouts.email')

@section('content')
    <h1>New order received 🎉</h1>
    <p>Hi <strong>{{ $seller->name ?: $seller->username }}</strong>,</p>
    <p>You have a new order from <strong>{{ $buyer->name ?: $buyer->username }}</strong>.</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Product</td>
                <td class="value">{{ $product->product_name }}</td>
            </tr>
            <tr>
                <td class="label">Order total</td>
                <td class="value">{{ $amount }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td class="value">{{ ucfirst($order->shipping_status) }}</td>
            </tr>
            <tr>
                <td class="label">Order ID</td>
                <td class="value">#{{ $order->id }}</td>
            </tr>
        </table>
    </div>

    <p>Keep the item ready and ship it once the buyer's address is confirmed.</p>

    <a class="btn" href="{{ $orderUrl }}">View order</a>
    <p style="color:#64748b; font-size:13px;">This payment is held securely until the order is delivered.</p>
@endsection