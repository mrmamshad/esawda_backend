@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:960px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">Messages</h1>

    @if(session('flash_success'))
        <div style="background:#d1fae5;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_success') }}</div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;padding:.75rem;border-radius:6px;margin-bottom:1rem;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
        <section>
            <h2 style="font-size:1.15rem;margin-bottom:.5rem;">📥 Inbox ({{ isset($inbox) ? $inbox->count() : 0 }})</h2>
            @if(isset($inbox) && $inbox->count())
                @foreach($inbox as $m)
                    <div style="border:1px solid #e5e7eb;border-radius:6px;padding:.75rem;margin-bottom:.5rem;background:{{ $m->seen == '0' ? '#fef3c7' : '#fff' }};">
                        <div style="font-size:.85rem;color:#6b7280;">From: {{ $m->from_uname ?? 'user #' . $m->from_id }} • {{ $m->message_date }}</div>
                        <div>{{ $m->message_content }}</div>
                    </div>
                @endforeach
            @else
                <p style="color:#6b7280;">Inbox empty.</p>
            @endif
        </section>

        <section>
            <h2 style="font-size:1.15rem;margin-bottom:.5rem;">📤 Sent ({{ isset($sent) ? $sent->count() : 0 }})</h2>
            @if(isset($sent) && $sent->count())
                @foreach($sent as $m)
                    <div style="border:1px solid #e5e7eb;border-radius:6px;padding:.75rem;margin-bottom:.5rem;">
                        <div style="font-size:.85rem;color:#6b7280;">To: user #{{ $m->to_id }} • {{ $m->message_date }}</div>
                        <div>{{ $m->message_content }}</div>
                    </div>
                @endforeach
            @else
                <p style="color:#6b7280;">No sent messages.</p>
            @endif
        </section>
    </div>

    <hr style="margin:2rem 0;">

    <h2 style="font-size:1.15rem;margin-bottom:.5rem;">Send new message</h2>
    <form method="post" action="{{ route('message') }}" style="max-width:500px;">
        @csrf
        <label>Recipient user ID</label>
        <input name="to_id" type="number" required
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Message</label>
        <textarea name="message_content" required rows="4" maxlength="5000"
                  style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;"></textarea>

        <button type="submit" style="background:#2563eb;color:#fff;padding:.5rem 1rem;border:0;border-radius:4px;cursor:pointer;">
            Send
        </button>
    </form>
</div>
@include('partials.footer')
