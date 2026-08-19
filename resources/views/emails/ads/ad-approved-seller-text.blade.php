{{ $subject ?? '' }}

Hi {{ $owner->name ?: $owner->username }},

Your ad "{{ $post->product_name }}" has been approved and is now live on eSawda.

View your ad: {{ $adUrl }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
