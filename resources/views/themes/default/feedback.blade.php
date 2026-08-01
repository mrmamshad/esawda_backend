@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:520px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">💬 Feedback</h1>

    @if(session('flash_success'))
        <div style="background:#d1fae5;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_success') }}</div>
    @endif

    <form method="post" action="{{ route('feedback') }}">
        @csrf
        <label>Name</label>
        <input name="name" type="text" required
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">
        <label>Email</label>
        <input name="email" type="email" required
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">
        <label>Message</label>
        <textarea name="message" rows="5" required maxlength="4000"
                  style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;"></textarea>
        <button type="submit" style="background:#2563eb;color:#fff;padding:.5rem 1rem;border:0;border-radius:4px;cursor:pointer;">
            Send feedback
        </button>
    </form>
</div>
@include('partials.footer')
