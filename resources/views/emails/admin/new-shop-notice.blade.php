@extends('emails.layouts.email')

@section('content')
    <h1>New shop opened</h1>
    <p>A new seller has opened a shop on eSawda:</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Name</td>
                <td class="value">{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="label">Shop name</td>
                <td class="value">{{ $user->shop_name ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td class="value">{{ $user->email }}</td>
            </tr>
            <tr>
                <td class="label">Phone</td>
                <td class="value">{{ $user->phone ?: '—' }}</td>
            </tr>
        </table>
    </div>
@endsection