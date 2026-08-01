@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.register') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.register') }}</li>
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
                    <h3 style="font-size: 26px;">{{ __('quickad.lets_create_acc') }}</h3>
                    <span>{{ __('quickad.already_have_acc') }} <a href="{{ $link['LOGIN'] ?? '#' }}">{{ __('quickad.login') }}</a></span>
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
                <form method="post" name="register-form" id="register-form" action="#" accept-charset="UTF-8">
                    <div class="form-group">
                        <div class="input-with-icon-left">
                            <i class="la la-user"></i>
                            <input type="text" class="input-text with-border" placeholder="{{ __('quickad.full_name') }}" value="{{ $name_field ?? '' }}" id="name" name="name" onBlur="checkAvailabilityName()" required/>
                        </div>
                        <span id="name-availability-status">@if(($name_error ?? "")!="") {{ $name_error ?? '' }} @endif</span>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon-left">
                            <i class="la la-user"></i>
                            <input type="text" class="input-text with-border" placeholder="{{ __('quickad.username') }}" value="{{ $username_field ?? '' }}" id="Rusername" name="username" onBlur="checkAvailabilityUsername()" required/>
                        </div>
                        <span id="user-availability-status">@if(($username_error ?? "")!="") {{ $username_error ?? '' }} @endif</span>
                    </div>
                    <div class="form-group">
                        <div class="input-with-icon-left">
                            <i class="la la-envelope"></i>
                            <input type="text" class="input-text with-border" placeholder="{{ __('quickad.email') }}" value="{{ $email_field ?? '' }}" name="email" id="email" onBlur="checkAvailabilityEmail()" required/>
                        </div>
                        <span id="email-availability-status">@if(($email_error ?? "")!="") {{ $email_error ?? '' }} @endif</span>
                    </div>
                    @if(($sms_verify_mode ?? "")=="1")
                    <div class="form-group">
                        <div>
                            <input type="phone" class="input-text with-border" placeholder="{{ __('quickad.phone_no') }}" value="{{ $phone_field ?? '' }}" id="verify-mobile" name="phone" onBlur="checkAvailabilityPhone()" required/>
                        </div>
                        <span id="phone-availability-status">@if(($phone_error ?? "")!="") {{ $phone_error ?? '' }} @endif</span>
                    </div>
                    @endif
                    <div class="form-group">
                        <div class="input-with-icon-left">
                            <i class="la la-unlock"></i>
                            <input type="password" class="input-text with-border" placeholder="{{ __('quickad.password') }}" id="Rpassword" name="password" onBlur="checkAvailabilityPassword()" required/>
                        </div>
                        <span id="password-availability-status">@if(($password_error ?? "")!="") {{ $password_error ?? '' }} @endif</span>
                    </div>
                    <div class="form-group">
                        <div class="text-center">
                            @if(($recaptcha_mode ?? "")=="1")
                            <div style="display: inline-block;" class="g-recaptcha" data-sitekey="{{ $recaptcha_public_key ?? '' }}"></div>
                            @endif
                        </div>
                        <span>@if(($recaptch_error ?? "")!="") {{ $recaptch_error ?? '' }} @endif</span>
                    </div>
                    <span class="text-center">{{ __('quickad.by_click_register') }} <a href="{{ $termcondition_link ?? '' }}" target="_blank">{{ __('quickad.term_con') }}</a> </span>
                    <button class="button full-width button-sliding-icon ripple-effect margin-top-10" name="submit" type="submit">{{ __('quickad.register') }} <i class="icon-feather-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="margin-top-70"></div>
<script src='https://www.google.com/recaptcha/api.js'></script>
<!-- Verify Mobile Number popup / End -->
<link href="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/css/intlTelInput.css" media="all" rel="stylesheet" type="text/css"/>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/js/intlTelInput.min.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/js/intlTelInput.utils.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/js/custom.js"></script>
<script>

    var error = "";

    function checkAvailabilityName() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: ajaxurl,
            data : {
                action: 'check_availability',
                name: $("#name").val()
            },
            type: "POST",
            success: function (data) {
                if (data != "success") {
                    error = 1;
                    $("#name").removeClass('has-success');
                    $("#name-availability-status").html(data);
                    $("#name").addClass('has-error mar-zero');
                }
                else {
                    error = 0;
                    $("#name").removeClass('has-error mar-zero');
                    $("#name-availability-status").html("");
                    $("#name").addClass('has-success');
                }
                $("#loaderIcon").hide();
            },
            error: function () {
            }
        });
    }
    function checkAvailabilityUsername() {
        var $item = $("#Rusername").closest('.form-group');
        $("#loaderIcon").show();
        jQuery.ajax({
            url: ajaxurl,
            data : {
                action: 'check_availability',
                username: $("#Rusername").val()
            },
            type: "POST",
            success: function (data) {
                if (data != "success") {
                    error = 1;
                    $item.removeClass('has-success');
                    $("#user-availability-status").html(data);
                    $item.addClass('has-error');
                }
                else {
                    error = 0;
                    $item.removeClass('has-error');
                    $("#user-availability-status").html("");
                    $item.addClass('has-success');
                }
                $("#loaderIcon").hide();
            },
            error: function () {
            }
        });
    }
    function checkAvailabilityEmail() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: ajaxurl,
            data : {
                action: 'check_availability',
                email: $("#email").val()
            },
            type: "POST",
            success: function (data) {
                if (data != "success") {
                    error = 1;
                    $("#email").removeClass('has-success');
                    $("#email-availability-status").html(data);
                    $("#email").addClass('has-error mar-zero');
                }
                else {
                    error = 0;
                    $("#email").removeClass('has-error mar-zero');
                    $("#email-availability-status").html("");
                    $("#email").addClass('has-success');
                }
                $("#loaderIcon").hide();
            },
            error: function () {
            }
        });
    }
    function checkAvailabilityPhone() {
        $("#loaderIcon").show();
        var getNumber = $('#verify-mobile').intlTelInput("getNumber");
        $('#verify-mobile').val(getNumber);
        jQuery.ajax({
            url: ajaxurl,
            data : {
                action: 'check_availability',
                phone: $('#verify-mobile').val()
            },
            type: "POST",
            success: function (data) {
                if (data != "success") {
                    error = 1;
                    $("#verify-mobile").removeClass('has-success');
                    $("#phone-availability-status").html(data);
                    $("#verify-mobile").addClass('has-error mar-zero');
                }
                else {
                    error = 0;
                    $("#verify-mobile").removeClass('has-error mar-zero');
                    $("#phone-availability-status").html("");
                    $("#verify-mobile").addClass('has-success');
                }
                $("#loaderIcon").hide();
            },
            error: function () {
            }
        });
    }
    function checkAvailabilityPassword() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: ajaxurl,
            data : {
                action: 'check_availability',
                password: $("#Rpassword").val()
            },
            type: "POST",
            success: function (data) {
                if (data != "success") {
                    error = 1;
                    $("#Rpassword").removeClass('has-success');
                    $("#password-availability-status").html(data);
                    $("#Rpassword").addClass('has-error mar-zero');
                }
                else {
                    error = 0;
                    $("#Rpassword").removeClass('has-error mar-zero');
                    $("#password-availability-status").html("");
                    $("#Rpassword").addClass('has-success');
                }
                $("#loaderIcon").hide();
            },
            error: function () {
            }
        });
    }

</script>

@include('partials.footer')
