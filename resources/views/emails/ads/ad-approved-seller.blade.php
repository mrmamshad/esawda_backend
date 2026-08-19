@extends('emails.layouts.email')

@section('content')
    <h1>Your ad is live 🎊</h1>
    <p>Hi <strong>{{ $owner->name ?: $owner->username }}</strong>,</p>
    <p>Your ad <strong>{{ $post->product_name }}</strong> has been approved and is now live on eSawda.</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Ad</td>
                <td class="value">{{ $post->product_name }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td class="value">Live</td>
            </tr>
        </table>
    </div>

    <a class="btn" href="{{ $adUrl }}">View your ad</a>
    <p style="color:#64748b; font-size:13px;">Buyers can now find and contact you about it.</p>
@endsection