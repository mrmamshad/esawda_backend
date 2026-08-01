@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:720px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">Post a new ad</h1>

    @if(session('flash_success'))
        <div style="background:#d1fae5;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_success') }}</div>
    @endif
    @if(session('flash_error'))
        <div style="background:#fee2e2;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_error') }}</div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;padding:.75rem;border-radius:6px;margin-bottom:1rem;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="post" action="{{ route('ad.post') }}" enctype="multipart/form-data">
        @csrf

        <label>Product name *</label>
        <input name="product_name" type="text" required maxlength="150" value="{{ old('product_name') }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Description *</label>
        <textarea name="description" required rows="5"
                  style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">{{ old('description') }}</textarea>

        <label>Category *</label>
        <select name="category" required style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">
            <option value="">-- Choose a category --</option>
            @foreach($categories ?? [] as $c)
                <option value="{{ $c->cat_id }}" @selected(old('category') == $c->cat_id)>{{ $c->cat_name }}</option>
            @endforeach
        </select>

        <label>Price</label>
        <input name="price" type="number" min="0" value="{{ old('price', 0) }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Phone</label>
        <input name="phone" type="text" maxlength="50" value="{{ old('phone') }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>City</label>
        <input name="city" type="text" maxlength="50" value="{{ old('city') }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Country</label>
        <input name="country" type="text" maxlength="50" value="{{ old('country', 'Bangladesh') }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Tags</label>
        <input name="tag" type="text" maxlength="225" value="{{ old('tag') }}"
               placeholder="comma,separated,keywords"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Product image (optional)</label>
        <input name="image" type="file" accept="image/*"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <button name="submit" value="1" type="submit"
                style="background:#2563eb;color:#fff;padding:.6rem 1.25rem;border:0;border-radius:4px;cursor:pointer;">
            Submit ad
        </button>
    </form>
</div>
@include('partials.footer')
