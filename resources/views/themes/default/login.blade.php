@include('partials.header')
<div class="container mx-auto max-w-md p-6" style="font-family:system-ui;padding:2rem;max-width:420px;margin:2rem auto;">
    <h1 style="font-size:1.5rem;margin-bottom:1rem;">Login</h1>

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

    <form method="post" action="{{ route('auth.login') }}">
        @csrf
        <label style="display:block;margin-bottom:.5rem;">Email or Username</label>
        <input name="username" type="text" required autofocus
               value="{{ old('username') }}"
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin-bottom:1rem;">

        <label style="display:block;margin-bottom:.5rem;">Password</label>
        <input name="password" type="password" required
               style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:4px;margin-bottom:1rem;">

        <label style="display:block;margin-bottom:1rem;">
            <input name="remember" type="checkbox"> Remember me
        </label>

        <button name="submit" value="1" type="submit"
                style="background:#2563eb;color:#fff;padding:.6rem 1rem;border:0;border-radius:4px;cursor:pointer;width:100%;">
            Login
        </button>
    </form>

    <p style="margin-top:1.5rem;text-align:center;font-size:.9rem;">
        Don't have an account? <a href="{{ route('auth.signup') }}">Sign up</a>
    </p>
</div>
@include('partials.footer')
