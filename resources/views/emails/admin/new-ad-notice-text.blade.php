{{ $subject ?? '' }}

A new ad is awaiting moderation:

Ad: {{ $post->product_name }}
Seller: {{ $post->user?->name ?: $post->user?->username }}
Price: {{ $post->price ? '৳' . number_format((float) $post->price, 2) : '—' }}

Review: {{ $adUrl }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
