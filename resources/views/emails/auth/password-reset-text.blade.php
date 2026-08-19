{{ $subject ?? '' }}

Hi {{ $user->name ?: $user->username }},

Use the link below to reset your eSawda password. It expires in 60 minutes.

{{ $resetUrl }}

If you did not request this, you can safely ignore this email.

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
