{{ $subject ?? '' }}

Transaction #{{ $tx->id }} — {{ strtoupper((string) $tx->purpose) }}

Amount: {{ $amount }}
Purpose: {{ $tx->purpose }}
Status: {{ $tx->status?->value ?? 'success' }}
Customer: {{ $customer->name ?: $customer->username }} <{{ $customer->email }}>
Gateway: {{ $tx->transaction_gatway }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
