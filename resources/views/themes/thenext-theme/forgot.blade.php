@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.change_pass') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.change_pass') }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-5 margin-0-auto">
            <div class="login-register-page">
                <!-- Welcome Text -->
                <div class="welcome-text">
                    <h3>{{ __('quickad.change_pass') }}</h3>
                </div>

                @if(($forgot_error ?? "")!="")
                <span class='status-not-available'>{{ $forgot_error ?? '' }}</span>
                @endif
                <form method="post">
                    <span class="status-available">
                        <strong>{{ __('quickad.username') }} : </strong> {{ $username ?? '' }}
                    </span>
                    <div class="input-with-icon-left">
                        <i class="la la-unlock"></i>
                        <input type="password" class="input-text with-border" name="password" id="password"
                        placeholder="{{ __('quickad.password') }}" required/>
                    </div>
                    <div class="input-with-icon-left">
                        <i class="la la-unlock"></i>
                        <input type="password" class="input-text with-border" name="password2" id="password2"
                        placeholder="{{ __('quickad.conpass') }}" required/>
                    </div>
                    <input type="hidden" name="forgot" id="forgot" value="{{ $field_forgot ?? '' }}">
                    <input type="hidden" name="r" id="r" value="{{ $field_r ?? '' }}">
                    <input type="hidden" name="e" id="e" value="{{ $field_e ?? '' }}">
                    <input type="hidden" name="t" id="t" value="{{ $field_t ?? '' }}">
                    <input type="hidden" name="type" id="type" value="{{ $field_type ?? '' }}">
                    <button class="button full-width button-sliding-icon ripple-effect margin-top-10" name="submit" type="submit">{{ __('quickad.change_pass') }} <i class="icon-feather-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="margin-top-70"></div>
@include('partials.footer')
