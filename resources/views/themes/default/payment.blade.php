@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:640px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">Payment #{{ $transaction->id ?? '?' }}</h1>

    @if(($status ?? '') === 'success')
        <div style="background:#d1fae5;padding:1rem;border-radius:6px;">
            <h2 style="margin:0 0 .5rem;">✅ Payment successful</h2>
            <p>Thank you — your transaction of <strong>${{ number_format($transaction->amount, 2) }}</strong> via <strong>{{ $transaction->transaction_gatway }}</strong> has been recorded.</p>
            <p><a href="{{ route('invoice', ['id' => $transaction->id]) }}">Download invoice</a> · <a href="{{ route('dashboard') }}">Back to dashboard</a></p>
        </div>
    @elseif(($status ?? '') === 'cancel')
        <div style="background:#fee2e2;padding:1rem;border-radius:6px;">
            <h2 style="margin:0 0 .5rem;">❌ Payment cancelled</h2>
            <p><a href="{{ route('membership') }}">Try again</a></p>
        </div>
    @elseif(($status ?? '') === 'awaiting_transfer')
        <div style="background:#fef3c7;padding:1rem;border-radius:6px;">
            <h2 style="margin:0 0 .5rem;">⏳ Awaiting bank transfer</h2>
            <p>Please send <strong>${{ number_format($transaction->amount, 2) }}</strong> to:</p>
            <ul>
                <li><strong>Bank:</strong> {{ config('quickad.gateways.wire_transfer.bank_name') }}</li>
                <li><strong>Account name:</strong> {{ config('quickad.gateways.wire_transfer.account_name') }}</li>
                <li><strong>Account number:</strong> {{ config('quickad.gateways.wire_transfer.account_number') }}</li>
                <li><strong>Reference:</strong> TX-{{ $transaction->id }}</li>
            </ul>
            <p>Your ad will be activated once we confirm the transfer.</p>
        </div>
    @elseif(($status ?? '') === 'pending')
        <div style="background:#fef3c7;padding:1rem;border-radius:6px;">
            <h2 style="margin:0 0 .5rem;">⏳ Gateway not configured</h2>
            <p>Please contact the site administrator — the <strong>{{ $transaction->transaction_gatway }}</strong> gateway credentials are not set.</p>
        </div>
    @else
        <p>Processing payment…</p>
    @endif
</div>
@include('partials.footer')
