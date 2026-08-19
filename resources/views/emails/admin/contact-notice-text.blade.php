{{ $subject ?? '' }}

New contact message:

From: {{ $name }} <{{ $email }}>
Subject: {{ $subject ?? '(no subject)' }}

{{ $body }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
