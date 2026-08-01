@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:960px;margin:2rem auto;">
    <div style="display:flex;gap:1rem;align-items:center;margin-bottom:1.5rem;">
        <img src="/storage/profile/{{ $user->image ?? 'default_user.png' }}" alt="" style="width:80px;height:80px;border-radius:50%;background:#eee;">
        <div>
            <h1 style="font-size:1.5rem;margin:0;">{{ $user->name ?: $user->username }}</h1>
            <div style="color:#6b7280;">@{{ $user->username }} · {{ $user->city }} · {{ $user->country }}</div>
        </div>
    </div>

    @if(!empty($user->description))
        <p style="background:#f9fafb;padding:1rem;border-radius:6px;">{{ $user->description }}</p>
    @endif

    <h2 style="font-size:1.15rem;margin:1rem 0 .5rem;">Ads by {{ $user->username }} ({{ $posts->total() ?? $posts->count() }})</h2>
    @if($posts->count())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;">
            @foreach($posts as $p)
                <a href="{{ route('ad.detail', ['id' => $p->id, 'slug' => $p->slug]) }}"
                   style="border:1px solid #e5e7eb;border-radius:6px;padding:.75rem;text-decoration:none;color:inherit;">
                    <div style="font-weight:600;">{{ $p->product_name }}</div>
                    <div style="color:#6b7280;font-size:.85rem;">{{ $p->city }} • ${{ $p->price }}</div>
                </a>
            @endforeach
        </div>
        <div style="margin-top:1rem;">{{ $posts->links() }}</div>
    @else
        <p style="color:#6b7280;">No active ads.</p>
    @endif
</div>
@include('partials.footer')
