@include('partials.header')
<!-- signup-page -->
<section id="main" class="clearfix user-page">
    <div class="container">
        <div class="row text-center">
            <!-- user-login -->
            <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <div class="user-account">
                    <h2>{{ __('quickad.create_an_account') }}</h2>

                    <div class="social-signup socialLoginDivHide" style="padding-bottom: 20px;">
                        <div class="row">
                            @if(($facebook_app_id ?? "")!="")
                            <div class="col-xs-6">
                                <a class="loginBtn loginBtn--facebook" onclick="fblogin()"><i class="fa fa-facebook"></i> <span>Facebook</span></a>
                            </div>
                            @endif
                            @if(($google_app_id ?? "")!="")
                            <div class="col-xs-6">
                                <a class="loginBtn loginBtn--google" onclick="gmlogin()"><i class="fa fa-google"></i> <span>Google</span></a>
                            </div>
                            @endif
                        </div>
                        <div class="clear"></div>
                    </div>
                    <form action="#" method="post" accept-charset="UTF-8">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="{{ __('quickad.full_name') }}" value="{{ $name_field ?? '' }}" id="name" name="name" onBlur="checkAvailabilityName()">
                            <span id="name-availability-status">@if(($name_error ?? "")!="") {{ $name_error ?? '' }} @endif</span>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="{{ __('quickad.username') }}" value="{{ $username_field ?? '' }}" id="Rusername" name="username" onBlur="checkAvailabilityUsername()">
                            <span id="user-availability-status">@if(($username_error ?? "")!="") {{ $username_error ?? '' }} @endif</span>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="{{ __('quickad.email') }}" value="{{ $email_field ?? '' }}" name="email" id="email" onBlur="checkAvailabilityEmail()">
                            <span id="email-availability-status">@if(($email_error ?? "")!="") {{ $email_error ?? '' }} @endif</span>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="{{ __('quickad.conpass') }}" id="Rpassword" name="password" onBlur="checkAvailabilityPassword()">
                            <span id="password-availability-status">@if(($password_error ?? "")!="") {{ $password_error ?? '' }} @endif</span>
                        </div>
                        <div class="form-group text-center">
                            <div class="text-xs-center">
                                @if(($recaptcha_mode ?? "")=="1")
                                <div style="display: inline-block;" class="g-recaptcha" data-sitekey="{{ $recaptcha_public_key ?? '' }}"></div>
                                @endif
                            </div>
                            <span>@if(($recaptch_error ?? "")!="") {{ $recaptch_error ?? '' }} @endif</span>
                        </div>
                        <div class="checkbox">
                            <label class="pull-left checked" for="signing">
                                <input type="checkbox" name="signing" id="signing">
                                {{ __('quickad.by_click_register') }} {{ __('quickad.term_con') }}
                            </label>
                        </div>

                        <!-- checkbox -->
                        <button type="submit" name="submit" id="submit" class="btn">{{ __('quickad.register_now') }}</button>
                    </form>
                    <!-- checkbox -->
                </div>
            </div>
            <!-- user-login -->
        </div>
        <!-- row -->
    </div>
    <!-- container -->
</section>
<!-- signup-page -->
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
    /* 5 */
    /* Account Setting
    /* ========================================================================== */
    var error = "";
    function checkAvailabilityName() {
        $("#loaderIcon").show();
        var action = 'check_availability';
        var name = $("#name").val();
        var data = {action: action, name: name};
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
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
        var $item = $("#username").closest('.form-group');
        $("#loaderIcon").show();
        var action = 'check_availability';
        var username = $("#Rusername").val();
        var data = {action: action, username: username};
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
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
        var action = 'check_availability';
        var email = $("#email").val();
        var data = {action: action, email: email};
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
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
    function checkAvailabilityPassword() {
        var length = $('#Rpassword').val().length;
        if (length != 0) {
            var PASSLENG = "{{ __('quickad.passleng') }}";
            if (length < 5 || length > 21) {
                $("#password").removeClass('has-success');
                $("#password-availability-status").html("<span class='status-not-available'>" + PASSLENG + "</span>");
                $("#password").addClass('has-error mar-zero');
            }
            else {
                $("#password").removeClass('has-error');
                $("#password-availability-status").html("<span class='status-available'>Leave blank if don't want to change password.</span>");
                $("#password").addClass('has-success mar-zero');
            }
        }

    }

</script>
@include('partials.footer')