{{ $subject ?? '' }}

Hi {{ $buyer->name ?: $buyer->username }},

Your payment for transaction #{{ $tx->id }} has been refunded.

Amount refunded: {{ $amount }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
