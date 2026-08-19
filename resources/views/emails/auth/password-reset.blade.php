@extends('emails.layouts.email')

@section('content')
    <h1>Reset your password</h1>
    <p>Hi <strong>{{ $user->name ?: $user->username }}</strong>,</p>
    <p>We received a request to reset your eSawda password. Use the button below to set a new one. This link expires in 60 minutes.</p>

    <a class="btn" href="{{ $resetUrl }}">Reset password</a>

    <p style="color:#64748b; font-size:13px;">If the button does not work, copy and paste this link into your browser:<br>
    <a href="{{ $resetUrl }}" style="color:#2563eb; word-break:break-all;">{{ $resetUrl }}</a></p>

    <p style="color:#94a3b8; font-size:12px;">If you didn't request this, you can safely ignore this email — your password will not change.</p>
@endsection