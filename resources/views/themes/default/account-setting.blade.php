@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:640px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">Account settings</h1>

    @if(session('flash_success'))
        <div style="background:#d1fae5;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_success') }}</div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;padding:.75rem;border-radius:6px;margin-bottom:1rem;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="post" action="{{ route('account.setting') }}">
        @csrf
        <input type="hidden" name="_action" value="profile">

        <fieldset style="border:1px solid #e5e7eb;padding:1rem;border-radius:6px;margin-bottom:1.5rem;">
            <legend style="padding:0 .5rem;font-weight:600;">Profile</legend>

            <label>Name</label>
            <input name="name" type="text" value="{{ old('name', $user->name) }}"
                   style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

            <label>Email</label>
            <input name="email" type="email" value="{{ old('email', $user->email) }}"
                   style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

            <label>Phone</label>
            <input name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                   style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

            <label>Description</label>
            <textarea name="description" rows="3"
                      style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">{{ old('description', $user->description) }}</textarea>

            <label>Country</label>
            <input name="country" type="text" value="{{ old('country', $user->country) }}"
                   style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

            <label>City</label>
            <input name="city" type="text" value="{{ old('city', $user->city) }}"
                   style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

            <button type="submit" style="background:#2563eb;color:#fff;padding:.5rem 1rem;border:0;border-radius:4px;cursor:pointer;">
                Save profile
            </button>
        </fieldset>
    </form>

    <form method="post" action="{{ route('account.setting') }}">
        @csrf
        <input type="hidden" name="_action" value="password">

        <fieldset style="border:1px solid #e5e7eb;padding:1rem;border-radius:6px;">
            <legend style="padding:0 .5rem;font-weight:600;">Change password</legend>

            <label>Current password</label>
            <input name="current_password" type="password" required
                   style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

            <label>New password</label>
            <input name="password" type="password" required minlength="6"
                   style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

            <label>Confirm new password</label>
            <input name="password_confirmation" type="password" required minlength="6"
                   style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

            <button type="submit" style="background:#dc2626;color:#fff;padding:.5rem 1rem;border:0;border-radius:4px;cursor:pointer;">
                Change password
            </button>
        </fieldset>
    </form>
</div>
@include('partials.footer')
