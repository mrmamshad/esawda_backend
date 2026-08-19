@extends('emails.layouts.email')

@section('content')
    <h1>Payment released 💸</h1>
    <p>Hi <strong>{{ $seller->name ?: $seller->username }}</strong>,</p>
    <p>The payment for your order has been released to you.</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Product</td>
                <td class="value">{{ $product->product_name }}</td>
            </tr>
            <tr>
                <td class="label">Amount released</td>
                <td class="value">{{ $amount }}</td>
            </tr>
            <tr>
                <td class="label">Order ID</td>
                <td class="value">#{{ $order->id }}</td>
            </tr>
        </table>
    </div>

    <a class="btn" href="{{ $orderUrl }}">View order</a>
@endsection