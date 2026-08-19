@extends('emails.layouts.email')

@section('content')
    <h1>Ad not approved</h1>
    <p>Hi <strong>{{ $owner->name ?: $owner->username }}</strong>,</p>
    <p>We're sorry — your ad <strong>{{ $post->product_name }}</strong> could not be approved.</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Ad</td>
                <td class="value">{{ $post->product_name }}</td>
            </tr>
            <tr>
                <td class="label">Reason</td>
                <td class="value">{{ $reason }}</td>
            </tr>
        </table>
    </div>

    <p>You can edit the ad to fix the issue and resubmit it for review.</p>

    <a class="btn" href="{{ $adUrl }}">Manage your ads</a>
@endsection