@extends('emails.layouts.email')

@section('content')
    <h1>Payment received ✅</h1>
    <p>Hi <strong>{{ $buyer->name ?: $buyer->username }}</strong>,</p>
    <p>Your payment was successful and your order is confirmed. Here is your receipt:</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Product</td>
                <td class="value">{{ $product->product_name }}</td>
            </tr>
            <tr>
                <td class="label">Seller</td>
                <td class="value">{{ $seller->name ?: $seller->username }}</td>
            </tr>
            <tr>
                <td class="label">Paid</td>
                <td class="value">{{ $amount }}</td>
            </tr>
            <tr>
                <td class="label">Order ID</td>
                <td class="value">#{{ $order->id }}</td>
            </tr>
        </table>
    </div>

    <p>The seller has been notified and will prepare your item for shipping.</p>

    <a class="btn" href="{{ $orderUrl }}">Track your order</a>
    <p style="color:#64748b; font-size:13px;">Keep this email as your payment receipt.</p>
@endsection