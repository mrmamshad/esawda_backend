@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:960px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">My Ads</h1>

    @if(session('flash_success'))
        <div style="background:#d1fae5;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_success') }}</div>
    @endif

    <p style="margin-bottom:1rem;">
        <a href="{{ route('ad.post') }}"
           style="background:#2563eb;color:#fff;padding:.5rem 1rem;text-decoration:none;border-radius:4px;">+ Post new ad</a>
    </p>

    @if(isset($posts) && $posts->count())
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th style="padding:.5rem;text-align:left;border:1px solid #e5e7eb;">ID</th>
                    <th style="padding:.5rem;text-align:left;border:1px solid #e5e7eb;">Product</th>
                    <th style="padding:.5rem;text-align:right;border:1px solid #e5e7eb;">Price</th>
                    <th style="padding:.5rem;text-align:left;border:1px solid #e5e7eb;">Location</th>
                    <th style="padding:.5rem;text-align:left;border:1px solid #e5e7eb;">Status</th>
                    <th style="padding:.5rem;text-align:right;border:1px solid #e5e7eb;">Views</th>
                    <th style="padding:.5rem;text-align:center;border:1px solid #e5e7eb;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($posts as $p)
                <tr>
                    <td style="padding:.5rem;border:1px solid #e5e7eb;">#{{ $p->id }}</td>
                    <td style="padding:.5rem;border:1px solid #e5e7eb;">
                        <a href="{{ route('ad.detail', ['id' => $p->id, 'slug' => $p->slug]) }}">{{ $p->product_name }}</a>
                    </td>
                    <td style="padding:.5rem;text-align:right;border:1px solid #e5e7eb;">${{ $p->price }}</td>
                    <td style="padding:.5rem;border:1px solid #e5e7eb;">{{ $p->city }}, {{ $p->country }}</td>
                    <td style="padding:.5rem;border:1px solid #e5e7eb;">
                        <span style="background:{{ $p->status == 'active' ? '#d1fae5' : '#fef3c7' }};padding:.15rem .5rem;border-radius:4px;font-size:.8rem;">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td style="padding:.5rem;text-align:right;border:1px solid #e5e7eb;">{{ $p->view }}</td>
                    <td style="padding:.5rem;text-align:center;border:1px solid #e5e7eb;">
                        <a href="{{ route('ad.edit', ['id' => $p->id]) }}" style="margin-right:.5rem;">Edit</a>
                        <form action="{{ route('ad.mine') }}" method="post" style="display:inline;"
                              onsubmit="return confirm('Delete this ad?');">
                            @csrf
                            <input type="hidden" name="_action" value="delete">
                            <input type="hidden" name="post_id" value="{{ $p->id }}">
                            <button type="submit" style="background:none;color:#dc2626;border:0;cursor:pointer;padding:0;">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="margin-top:1rem;">{{ $posts->links() }}</div>
    @else
        <p style="color:#6b7280;">You haven't posted any active ads yet.</p>
    @endif
</div>
@include('partials.footer')
