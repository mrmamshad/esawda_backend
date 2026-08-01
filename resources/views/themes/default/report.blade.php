@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:520px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">🚩 Report an ad</h1>

    @if(session('flash_success'))
        <div style="background:#d1fae5;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_success') }}</div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;padding:.75rem;border-radius:6px;margin-bottom:1rem;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="post" action="{{ route('report') }}">
        @csrf
        <label>Ad ID</label>
        <input name="post_id" type="number" required value="{{ old('post_id', request('post_id')) }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Your email</label>
        <input name="email" type="email" required value="{{ old('email') }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Reason</label>
        <select name="reason" required style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">
            <option value="spam">Spam / scam</option>
            <option value="inappropriate">Inappropriate content</option>
            <option value="duplicate">Duplicate listing</option>
            <option value="miscategorized">Wrong category</option>
            <option value="other">Other</option>
        </select>

        <label>Details</label>
        <textarea name="details" rows="4" required maxlength="2000"
                  style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">{{ old('details') }}</textarea>

        <button type="submit" style="background:#dc2626;color:#fff;padding:.5rem 1rem;border:0;border-radius:4px;cursor:pointer;">
            Submit report
        </button>
    </form>
</div>
@include('partials.footer')
