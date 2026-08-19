@extends('emails.layouts.email')

@section('content')
    <h1>Transaction #{{ $tx->id }} — {{ strtoupper((string) $tx->purpose) }}</h1>
    <p>A payment transaction was completed on the platform:</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Amount</td>
                <td class="value">{{ $amount }}</td>
            </tr>
            <tr>
                <td class="label">Purpose</td>
                <td class="value">{{ $tx->purpose }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td class="value">{{ $tx->status?->value ?? 'success' }}</td>
            </tr>
            <tr>
                <td class="label">Customer</td>
                <td class="value">{{ $customer->name ?: $customer->username }} &lt;{{ $customer->email }}&gt;</td>
            </tr>
            <tr>
                <td class="label">Gateway</td>
                <td class="value">{{ $tx->transaction_gatway }}</td>
            </tr>
        </table>
    </div>
@endsection