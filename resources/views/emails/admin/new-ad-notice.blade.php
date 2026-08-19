@extends('emails.layouts.email')

@section('content')
    <h1>New ad awaiting review</h1>
    <p>A new ad has been submitted and is awaiting moderation:</p>

    <div class="panel">
        <table role="presentation" cellpadding="0" cellspacing="0">
            <tr>
                <td class="label">Ad</td>
                <td class="value">{{ $post->product_name }}</td>
            </tr>
            <tr>
                <td class="label">Seller</td>
                <td class="value">{{ $post->user?->name ?: $post->user?->username ?: ('#' . $post->user_id) }}</td>
            </tr>
            <tr>
                <td class="label">Price</td>
                <td class="value">{{ $post->price ? '৳' . number_format((float) $post->price, 2) : '—' }}</td>
            </tr>
        </table>
    </div>

    <a class="btn" href="{{ $adUrl }}">Review ads</a>
@endsection