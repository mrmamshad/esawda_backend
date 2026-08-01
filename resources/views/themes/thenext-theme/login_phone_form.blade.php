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


                <!-- Form -->
                <div id="mobile-form">
                    <div class="reset-form d-block">
                        <div class="welcome-text">
                            <h3>{{ __('quickad.login_with_phone') }}</h3>
                            <span id="mobile-status">{{ __('quickad.otp_notify_1') }}</span>
                        </div>
                        <form action="mobile_verify" name="pv-form" id="pv-form" method="post">
                            <input required class="input-text with-border" id="verify-mobile" name="mobile_no" type="phone" placeholder="{{ __('quickad.phone_no') }}" >
                            <input type="hidden" value="1" name="submit_mobile"/>
                            <button class="button full-width button-sliding-icon ripple-effect margin-top-20" id="mobile-submit" type="submit" name="submit">{{ __('quickad.send_otp') }} <i class="icon-feather-arrow-right"></i></button>
                        </form>
                    </div>
                    <div class="reset-confirmation d-none">
                        <div class="welcome-text">
                            <h3>{{ __('quickad.otp_verification') }}</h3>
                            <span id="otp-status">{{ __('quickad.otp_notify_2') }}: <span class="otp_mobile"></span></span>
                        </div>
                        <form action="otp_verify" name="otp-form" id="otp-form" method="post">
                            <div class="otp-form-content">
                                <div class="input-with-icon-left">
                                    <i class="la la-user"></i>
                                    <input required class="input-text with-border" name="otp_code" type="phone" placeholder="{{ __('quickad.otp_code') }}">
                                </div>
                                <input type="hidden" value="" name="mobile_no" class="otp_mobile_no"/>
                                <input type="hidden" value="1" name="submit_otp"/>
                                <button class="button full-width button-sliding-icon ripple-effect margin-top-20" id="otp-submit" type="submit" name="submit">{{ __('quickad.verify_otp') }} <i class="icon-feather-arrow-right"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="margin-top-70"></div>
<!-- Verify Mobile Number popup / End -->
<link href="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/css/intlTelInput.css" media="all" rel="stylesheet" type="text/css"/>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/js/intlTelInput.min.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/js/intlTelInput.utils.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/js/custom.js"></script>
@include('partials.footer')