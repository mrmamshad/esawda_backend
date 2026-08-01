@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.login') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.login') }}</li>
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
                    <h3>{{ __('quickad.welcome_back') }}</h3>
                    <span>{{ __('quickad.dont_have_account') }} <a href="{{ $link['SIGNUP'] ?? '#' }}">{{ __('quickad.signup_now') }}</a></span>
                </div>
                @if(($facebook_app_id ?? "")!='' || ($google_app_id ?? "")!='')
                <div class="social-login-buttons">
                     @if(($facebook_app_id ?? "")!='')
                    <button class="facebook-login ripple-effect" onclick="fblogin()"><i class="fa fa-facebook"></i> {{ __('quickad.login_via_facebook') }}
                    </button>
                    @endif

                     @if(($google_app_id ?? "")!='')
                    <button class="google-login ripple-effect" onclick="gmlogin()"><i class="fa fa-google"></i> {{ __('quickad.login_via_google') }}
                    </button>
                    @endif
                </div>
                <div class="social-login-separator"><span>{{ __('quickad.or') }}</span></div>
                @endif
                <!-- Form -->
                @if(($error ?? "")!="")
                <span class='status-not-available'>{{ $error ?? '' }}</span>
                @endif
                <form method="post">
                    <div class="input-with-icon-left">
                        <i class="la la-user"></i>
                        <input type="text" class="input-text with-border" name="username"
                        placeholder="{{ __('quickad.username') }} / {{ __('quickad.email') }}" required/>
                    </div>

                    <div class="input-with-icon-left">
                        <i class="la la-unlock"></i>
                        <input type="password" class="input-text with-border" name="password"
                        placeholder="{{ __('quickad.password') }}" required/>
                    </div>
                    <div class="row mt-6 mb-6">
                        <div class="col-6 align-items-center d-flex">
                            <div class="checkbox">
                                <input type="checkbox" id="remember1" name="remember" value="1">
                                <label for="remember1"><span class="checkbox-icon"></span> {{ __('quickad.remember_me') }}</label>
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            <a href="{{ $link['LOGIN'] ?? '#' }}?fstart=1" class="forgot-password">{{ __('quickad.forgotpass') }}</a>
                        </div>
                    </div>
                    <input type="hidden" name="ref" value="{{ $ref ?? '' }}"/>
                    <button class="button full-width button-sliding-icon ripple-effect margin-top-10" name="submit" type="submit">{{ __('quickad.login') }} <i class="icon-feather-arrow-right"></i></button>
                    </form>
                @if(($sms_verify_mode ?? "")=="1")
                <a href="{{ $link['LOGIN'] ?? '#' }}?loginphone=1" class="button full-width button-sliding-icon ripple-effect margin-top-10">{{ __('quickad.login_with_phone') }} <i class="icon-feather-arrow-right"></i></a>
                @endif
            </div>
            </div>
        </div>
    </div>
    <div class="margin-top-70"></div>
    @include('partials.footer')