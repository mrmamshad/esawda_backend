@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:960px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">❤️ My Favourites</h1>

    @if(session('flash_success'))
        <div style="background:#d1fae5;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_success') }}</div>
    @endif

    @if(isset($favourites) && $favourites->count())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;">
            @foreach($favourites as $fav)
                @if($fav->post)
                    <div style="border:1px solid #e5e7eb;border-radius:6px;padding:.75rem;">
                        <a href="{{ route('ad.detail', ['id' => $fav->post->id, 'slug' => $fav->post->slug]) }}">
                            <div style="font-weight:600;">{{ $fav->post->product_name }}</div>
                            <div style="color:#6b7280;font-size:.85rem;">{{ $fav->post->city }} • ${{ $fav->post->price }}</div>
                        </a>
                        <form method="post" action="{{ route('ad.favourite') }}" style="margin-top:.5rem;">
                            @csrf
                            <input type="hidden" name="_action" value="remove">
                            <input type="hidden" name="favourite_id" value="{{ $fav->id }}">
                            <button style="background:none;color:#dc2626;border:0;cursor:pointer;padding:0;font-size:.85rem;">
                                Remove
                            </button>
                        </form>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <p style="color:#6b7280;">No favourites yet. Browse ads and add some!</p>
    @endif
</div>
@include('partials.footer')
