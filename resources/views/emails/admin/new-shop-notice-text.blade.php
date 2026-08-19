{{ $subject ?? '' }}

A new shop opened on eSawda:

Name: {{ $user->name }}
Shop: {{ $user->shop_name ?: '—' }}
Email: {{ $user->email }}
Phone: {{ $user->phone ?: '—' }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
