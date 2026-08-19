{{ $subject ?? '' }}

Hi {{ $user->name ?: $user->username }},

Your shop is now open on eSawda. Start listing your products and reaching buyers today.

Dashboard: {{ $shopUrl }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
