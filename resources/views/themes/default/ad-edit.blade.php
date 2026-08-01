@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:720px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">Edit ad #{{ $post->id }}</h1>

    @if(session('flash_success'))
        <div style="background:#d1fae5;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_success') }}</div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;padding:.75rem;border-radius:6px;margin-bottom:1rem;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="post" action="{{ route('ad.edit', ['id' => $post->id]) }}">
        @csrf

        <label>Product name *</label>
        <input name="product_name" type="text" required value="{{ old('product_name', $post->product_name) }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Description *</label>
        <textarea name="description" required rows="5"
                  style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">{{ old('description', $post->description) }}</textarea>

        <label>Price</label>
        <input name="price" type="number" min="0" value="{{ old('price', $post->price) }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>City</label>
        <input name="city" type="text" value="{{ old('city', $post->city) }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Country</label>
        <input name="country" type="text" value="{{ old('country', $post->country) }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Phone</label>
        <input name="phone" type="text" value="{{ old('phone', $post->phone) }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <button name="submit" value="1" type="submit"
                style="background:#2563eb;color:#fff;padding:.6rem 1.25rem;border:0;border-radius:4px;cursor:pointer;">
            Save changes
        </button>
        <a href="{{ route('ad.mine') }}" style="margin-left:1rem;">Cancel</a>
    </form>
</div>
@include('partials.footer')
