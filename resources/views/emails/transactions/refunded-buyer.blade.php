@extends('emails.layouts.email')

@section('content')
    <h1>Refund issued</h1>
    <p>Hi <strong>{{ $buyer->name ?: $buyer->username }}</strong>,</p>
    <p>Your payment for transaction <strong>#{{ $tx->id }}</strong> has been refunded.</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Amount refunded</td>
                <td class="value">{{ $amount }}</td>
            </tr>
            <tr>
                <td class="label">Transaction</td>
                <td class="value">#{{ $tx->id }}</td>
            </tr>
        </table>
    </div>

    <p style="color:#64748b; font-size:13px;">Refunds typically appear in 5–10 business days depending on your bank or payment method.</p>
@endsection