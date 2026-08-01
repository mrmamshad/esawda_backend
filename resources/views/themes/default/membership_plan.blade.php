@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:960px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">Membership plans</h1>

    @if(session('flash_success'))
        <div style="background:#d1fae5;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_success') }}</div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;padding:.75rem;border-radius:6px;margin-bottom:1rem;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    @if(isset($plans) && $plans->count())
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem;">
            @foreach($plans as $plan)
                <div style="border:1px solid #e5e7eb;border-radius:6px;padding:1rem;">
                    <h3 style="margin:0 0 .5rem;">{{ $plan->name }}
                        @if($plan->recommended === 'yes')<span style="background:#fde68a;color:#78350f;padding:.15rem .5rem;font-size:.7rem;border-radius:4px;">RECOMMENDED</span>@endif
                    </h3>
                    <p><strong>${{ $plan->monthly_price ?? 0 }}</strong>/month
                       · ${{ $plan->annual_price ?? 0 }}/year
                       · ${{ $plan->lifetime_price ?? 0 }} lifetime</p>
                    <form method="post" action="{{ route('membership') }}">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                        <label>Billing</label>
                        <select name="frequency" style="width:100%;padding:.35rem;margin:.25rem 0;">
                            <option value="MONTHLY">Monthly (${{ $plan->monthly_price ?? 0 }})</option>
                            <option value="YEARLY">Yearly (${{ $plan->annual_price ?? 0 }})</option>
                            <option value="LIFETIME">Lifetime (${{ $plan->lifetime_price ?? 0 }})</option>
                        </select>

                        <label>Payment method</label>
                        <select name="gateway" style="width:100%;padding:.35rem;margin:.25rem 0;">
                            @foreach($gateways ?? [] as $slug => $label)
                                <option value="{{ $slug }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        <button type="submit"
                                style="background:#2563eb;color:#fff;padding:.5rem 1rem;border:0;border-radius:4px;cursor:pointer;margin-top:.5rem;width:100%;">
                            Subscribe
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <p style="color:#6b7280;">No membership plans are set up yet.</p>
        <p><a href="{{ route('dashboard') }}">← Back to dashboard</a></p>
    @endif
</div>
@include('partials.footer')
