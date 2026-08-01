@include('partials.header')
<div style="font-family:system-ui;padding:2rem;max-width:480px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">Sign up</h1>

    @if(session('flash_error'))
        <div style="background:#fee2e2;padding:.75rem;border-radius:6px;margin-bottom:1rem;">{{ session('flash_error') }}</div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;padding:.75rem;border-radius:6px;margin-bottom:1rem;">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="post" action="{{ route('auth.signup') }}">
        @csrf
        <label>Name</label>
        <input name="name" type="text" value="{{ old('name') }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Username</label>
        <input name="username" type="text" required value="{{ old('username') }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Email</label>
        <input name="email" type="email" required value="{{ old('email') }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Password</label>
        <input name="password" type="password" required minlength="6"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Confirm password</label>
        <input name="password_confirmation" type="password" required minlength="6"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">

        <label>Account type</label>
        <select name="user_type" style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin:.25rem 0 1rem;">
            <option value="user">User</option>
            <option value="seller">Seller</option>
        </select>

        <button name="submit" value="1" type="submit"
                style="background:#16a34a;color:#fff;padding:.6rem 1rem;border:0;border-radius:4px;cursor:pointer;width:100%;">
            Create account
        </button>
    </form>

    <p style="margin-top:1.5rem;text-align:center;font-size:.9rem;">
        Already have an account? <a href="{{ route('auth.login') }}">Log in</a>
    </p>
</div>
@include('partials.footer')
