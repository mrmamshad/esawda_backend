{{ $subject ?? '' }}

Hi {{ $user->name ?: $user->username }},

Your {{ $planName }} plan is now active. You can start listing ads and selling right away.

Plan: {{ $planName }}
Active until: {{ $expiresAt }}

Dashboard: {{ $dashboardUrl }}

© {{ date('Y') }} {{ config('app.name', 'eSawda') }}
