@extends('emails.layouts.email')

@section('content')
    <h1>Your plan is active 🚀</h1>
    <p>Hi <strong>{{ $user->name ?: $user->username }}</strong>,</p>
    <p>Your <strong>{{ $planName }}</strong> plan is now active. You can start listing ads and selling right away.</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Plan</td>
                <td class="value">{{ $planName }}</td>
            </tr>
            <tr>
                <td class="label">Active until</td>
                <td class="value">{{ $expiresAt }}</td>
            </tr>
        </table>
    </div>

    <a class="btn" href="{{ $dashboardUrl }}">Go to dashboard</a>
@endsection
