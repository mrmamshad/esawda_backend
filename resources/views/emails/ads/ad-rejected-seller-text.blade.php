{{ $subject ?? '' }}

Hi {{ $owner->name ?: $owner->username }},

Your ad "{{ $post->product_name }}" could not be approved.

Reason: {{ $reason }}

You can edit the ad and resubmit it: {{ $adUrl }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
