@include('partials.header')
<div id="page-content">
    <div class="container">
        <ul class="breadcrumb bcstyle2">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li class="active"><a>{{ __('quickad.account_setting') }}</a></li>
        </ul>
        <a href="{{ $link['POST-AD'] ?? '#' }}" class="postadinner"><span> <i class="fa fa-plus-circle"></i> {{ __('quickad.post_ad') }}</span></a>
        <!--end breadcrumb-->
        <section class="page-title center"><h1>{{ __('quickad.setting') }}</h1></section>
        <!--end page-title-->
        <section>
            <div class="row">
                <aside class="col-md-3 col-sm-12">
                    <div class="inner-box">
                        <div class="user-panel-sidebar">
                            <div class="collapse-box">
                                <h5 class="collapse-title no-border"> {{ __('quickad.my_classified') }} <a class="pull-right" data-toggle="collapse" href="#MyClassified"><i class="fa fa-angle-down"></i></a></h5>
                                <div id="MyClassified" class="panel-collapse collapse in">
                                    <ul class="acc-list">
                                        <li><a href="{{ $link['DASHBOARD'] ?? '#' }}" class="waves-effect"><i class="fa fa-home"></i> {{ __('quickad.dashboard') }}</a></li>
                                        <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}" class="waves-effect"><i class="fa fa-user"></i> {{ __('quickad.profile_public') }}</a></li>
                                        <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="waves-effect"><i class="fa fa-pencil"></i> {{ __('quickad.post_ad') }}</a></li>
                                        <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}" class="waves-effect"><i class="fa fa-shopping-bag"></i> {{ __('quickad.membership') }} </a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="collapse-box"><h5 class="collapse-title"> {{ __('quickad.my_ads') }} <a class="pull-right" data-toggle="collapse" href="#MyAds"><i class="fa fa-angle-down"></i></a></h5>

                                <div id="MyAds" class="panel-collapse collapse in">
                                    <ul class="acc-list">
                                        <li><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-book"></i> {{ __('quickad.my_ads') }}<span class="badge">{{ $myads ?? '' }}</span> </a></li>
                                        <li><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-heart"></i> {{ __('quickad.favourite_ads') }} <span class="badge">{{ $favoriteads ?? '' }}</span> </a></li>
                                        <li><a href="{{ $link['PENDINGADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.pending_approval') }}<span class="badge">{{ $pendingads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['HIDDENADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.hidden_ads') }} <span class="badge">{{ $hiddenads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['EXPIREADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-calendar-times-o"></i> {{ __('quickad.expire_ads') }} <span class="badge">{{ $expireads ?? '' }}</span></a>
                                        <li><a href="{{ $link['RESUBMITADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.resubmited_ads') }} <span class="badge">{{ $resubmitads ?? '' }}</span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="collapse-box">
                                <h5 class="collapse-title no-border"> {{ __('quickad.my_account') }} <a class="pull-right" data-toggle="collapse" href="#account"><i class="fa fa-angle-down"></i></a></h5>
                                <div id="account" class="panel-collapse collapse in">
                                    <ul class="acc-list">
                                        <li><a href="{{ $link['TRANSACTION'] ?? '#' }}" class="waves-effect"><i class="fa fa-money"></i> {{ __('quickad.transaction') }}</a></li>
                                        <li class="active"><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}" class="waves-effect"><i class="fa fa-cog"></i> {{ __('quickad.account_setting') }}</a></li>
                                        <li><a href="{{ $link['LOGOUT'] ?? '#' }}" class="waves-effect"><i class="fa fa-unlock"></i> {{ __('quickad.logout') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
                <div class="col-md-9 col-sm-12">
                    <section>
                        <h3>{{ __('quickad.account_setting') }}</h3>
                        <div class="row">
                            <form method="post" action="#">
                                <div class="input-field">
                                    <label for="email">{{ __('quickad.email') }}</label>
                                    <input type="email" name="email" id="email" value="{{ $email_field ?? '' }}"  onBlur="checkAvailabilityEmail()">
                                </div>
                                <span id="email-availability-status">@if(($email_error ?? "")!="") {{ $email_error ?? '' }} @endif</span>
                                <!--end input-field-->
                                <div class="input-field">
                                    <label for="username">{{ __('quickad.username') }}</label>
                                    <input type="text" name="username" id="username" value="{{ $username_field ?? '' }}" onBlur="checkAvailabilityUsername()">
                                </div>
                                <span id="user-availability-status">@if(($username_error ?? "")!="") {{ $username_error ?? '' }} @endif</span>
                                <!--end input-field-->
                                <input type="password" class="hide">
                                <div class="input-field">
                                    <label for="password">{{ __('quickad.newpass') }}</label>
                                    <input type="password" name="password" id="password" onkeyup="checkAvailabilityPassword()" autocomplete="off">
                                </div>
                                <span id="password-availability-status">@if(($password_error ?? "")!="") {{ $password_error ?? '' }} @endif</span>
                                <div class="input-field center">
                                    <button type="submit" class="btn btn-primary btn-framed btn-rounded btn-light-frame" name="submit">{{ __('quickad.save') }}</button>
                                </div>
                                <!--end input-field-->
                            </form>
                        </div>
                    </section>


                    <hr>
                </div>
                <!--end col-md-6-->
            </div>
            <!--end row-->
        </section>

    </div>
    <!--end container-->
</div> <!--end page-content-->
<script>
    /* 5 */
    /* Account Setting
    /* ========================================================================== */
    var error = "";
    function checkAvailabilityUsername() {
        var $item = $("#username").closest('.form-group');
        $("#loaderIcon").show();
        var action = 'check_availability';
        var username = $("#username").val();
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
        var length = $('#password').val().length;
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
    $(window).load(function () {
        $('#password').val("");
    });
</script>
@include('partials.footer')