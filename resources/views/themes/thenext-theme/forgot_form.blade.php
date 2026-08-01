@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.forgotpass') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.forgotpass') }}</li>
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
                    <h3>{{ __('quickad.forgotpass') }}</h3>
                </div>
                @if(($success ?? "")!="")
                    <span class="status-available">
                        <big>{{ __('quickad.confirmation_mail_sent') }}</big><br>
                        {{ $success ?? '' }}
                    </span>
                @endif
                @if(($login_error ?? "")!="")
                <span class='status-not-available'>{{ $login_error ?? '' }}</span>
                @endif
                <form method="post">
                    <div class="input-with-icon-left">
                        <i class="la la-envelope"></i>
                        <input type="email" class="input-text with-border" name="email" id="email"
                        placeholder="{{ __('quickad.email') }}" required/>
                    </div>
                    <button class="button full-width button-sliding-icon ripple-effect margin-top-10" name="submit" type="submit">{{ __('quickad.req_pass') }} <i class="icon-feather-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="margin-top-70"></div>
    @include('partials.footer')
