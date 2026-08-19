@extends('emails.layouts.email')

@section('content')
    <h1>Welcome to the eSawda shop 🛍️</h1>
    <p>Hi <strong>{{ $user->name ?: $user->username }}</strong>,</p>
    <p>Your shop is now open on eSawda. Start listing your products and reaching buyers across the platform.</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Shop name</td>
                <td class="value">{{ $user->shop_name ?: $user->name }}</td>
            </tr>
        </table>
    </div>

    <a class="btn" href="{{ $shopUrl }}">Go to your dashboard</a>
    <p style="color:#64748b; font-size:13px;">Tip: add clear photos and detailed descriptions to sell faster.</p>
@endsection