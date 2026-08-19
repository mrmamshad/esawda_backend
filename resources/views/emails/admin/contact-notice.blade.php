@extends('emails.layouts.email')

@section('content')
    <h1>New contact message</h1>
    <p>You received a new message from the contact form:</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">From</td>
                <td class="value">{{ $name }} &lt;{{ $email }}&gt;</td>
            </tr>
            @if(!empty($subject))
            <tr>
                <td class="label">Subject</td>
                <td class="value">{{ $subject }}</td>
            </tr>
            @endif
        </table>
    </div>

    <p style="white-space:pre-wrap; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px;">{{ $body }}</p>
@endsection