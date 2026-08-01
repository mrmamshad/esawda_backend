@include('partials.header')
<!-- Account-setting-page -->
<section id="main" class="clearfix  ad-profile-page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.account_setting') }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>{{ __('quickad.back_result') }}</a></div>
            </ol>
            <!-- breadcrumb -->
        </div>
        <!-- Main Content -->
        <div class="row">
            <!-- Page-Sidebar -->
            <aside class="col-sm-3 hidden-xs hidden-sm">
                <div class="section">
                    <div class="user-panel-sidebar">
                        <div class="collapse-box">
                            <h5 class="collapse-title no-border">{{ __('quickad.my_classified') }} <a class="pull-right" data-toggle="collapse" href="#MyClassified"><i class="fa fa-angle-down"></i></a></h5>
                            <div id="MyClassified" class="panel-collapse collapse in">
                                <ul class="acc-list">
                                    <li><a href="{{ $link['DASHBOARD'] ?? '#' }}" class="waves-effect"><i class="fa fa-home"></i>{{ __('quickad.dashboard') }} </a></li>
                                    <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}" class="waves-effect"><i class="fa fa-user"></i> {{ __('quickad.profile_public') }}</a></li>
                                    <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="waves-effect"><i class="fa fa-pencil"></i>{{ __('quickad.post_ad') }}</a></li>
                                    <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}" class="waves-effect"><i class="fa fa-shopping-bag"></i> {{ __('quickad.membership') }} </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="collapse-box">
                            <h5 class="collapse-title"> {{ __('quickad.my_ads') }} <a class="pull-right" data-toggle="collapse" href="#MyAds"><i class="fa fa-angle-down"></i></a></h5>
                            <div id="MyAds" class="panel-collapse collapse in">
                                <ul class="acc-list">
                                    <li><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-book"></i>{{ __('quickad.my_ads') }} <span class="badge">{{ $myads ?? '' }}</span> </a></li>
                                    <li><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-heart"></i>{{ __('quickad.favourite_ads') }} <span class="badge">{{ $favoriteads ?? '' }}</span> </a></li>
                                    <li><a href="{{ $link['PENDINGADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-info-circle"></i> {{ __('quickad.pending_ads') }}<span class="badge">{{ $pendingads ?? '' }}</span></a></li>
                                    <li><a href="{{ $link['HIDDENADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-eye-slash"></i> {{ __('quickad.hidden_ads') }} <span class="badge">{{ $hiddenads ?? '' }}</span></a></li>
                                    <li><a href="{{ $link['EXPIREADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-calendar-times-o"></i> {{ __('quickad.expire_ads') }} <span class="badge">{{ $expireads ?? '' }}</span></a>
                                    <li><a href="{{ $link['RESUBMITADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-briefcase"></i> {{ __('quickad.resubmited_ads') }} <span class="badge">{{ $resubmitads ?? '' }}</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="collapse-box">
                            <h5 class="collapse-title no-border"> {{ __('quickad.my_account') }} <a class="pull-right" data-toggle="collapse" href="#account"><i class="fa fa-angle-down"></i></a></h5>
                            <div id="account" class="panel-collapse collapse in">
                                <ul class="acc-list">
                                    <li><a href="{{ $link['TRANSACTION'] ?? '#' }}" class="waves-effect"><i class="fa fa-money"></i> {{ __('quickad.transaction') }}</a></li>
                                    <li class="active"><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}" class="waves-effect"><i class="fa fa-cog"></i> {{ __('quickad.account_setting') }} </a></li>
                                    <li><a href="{{ $link['LOGOUT'] ?? '#' }}" class="waves-effect"><i class="fa fa-unlock"></i>{{ __('quickad.logout') }} </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <!-- # End Page-Sidebar -->
            <!-- Page-Content -->
            <div class="col-sm-9 page-content">
                <div class="panel-user-details">
                    <!-- profile-details -->
                    <div class="user-details section">
                        <div class="user-img"><img src="{{ $site_url ?? '' }}storage/profile/small_{{ $authorimg ?? '' }}" alt="{{ $username ?? '' }}" class="img-responsive"></div>
                        <div class="user-admin">
                            <h3><a href="#">{{ __('quickad.hello') }} {{ $username ?? '' }}</a></h3>
                            <small>{{ __('quickad.you_login') }}: {{ $lastactive ?? '' }}</small>
                        </div>
                        <div class="user-ads-details">
                            <div class="my-quickad">
                                <h3><a href="{{ $link['MYADS'] ?? '#' }}">{{ $myads ?? '' }}</a></h3>
                                <small>{{ __('quickad.my_ads') }}</small>
                            </div>
                            <div class="favourites">
                                <h3><a href="{{ $link['FAVADS'] ?? '#' }}">{{ $favoriteads ?? '' }}</a></h3>
                                <small>{{ __('quickad.favourites') }}</small>
                            </div>
                        </div>
                    </div>
                    <!-- profile-details -->
                    <!-- My Details -->
                    <div class="section my-details">
                        <div class="section-title">
                            <h2>{{ __('quickad.change_pass') }}</h2>
                        </div>
                        <form class="row" method="post">
                            <div class="section-body">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ __('quickad.email') }}<span class="required">*</span></label>
                                    <div class="col-sm-9">
                                        <input class="form-control border-form" type="text" name="email" id="email" value="{{ $email_field ?? '' }}" onBlur="checkAvailabilityEmail()">
                                        <span id="email-availability-status">@if(($email_error ?? "")!="") {{ $email_error ?? '' }} @endif</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ __('quickad.username') }}<span class="required">*</span></label>

                                    <div class="col-sm-9">
                                        <input class="form-control border-form" type="text" name="username" id="username" value="{{ $username_field ?? '' }}" onBlur="checkAvailabilityUsername()">
                                        <span id="user-availability-status">@if(($username_error ?? "")!="") {{ $username_error ?? '' }} @endif</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ __('quickad.password') }}<span class="required">*</span></label>
                                    <div class="col-sm-9">
                                        <input class="form-control border-form" type="text" name="password" id="password" onkeyup="checkAvailabilityPassword()" autocomplete="off">
                                        <span id="password-availability-status">@if(($password_error ?? "")!="") {{ $password_error ?? '' }} @endif</span>
                                    </div>
                                </div>
                                <section class="hidden">
                                    <div class="section-title">
                                        <h2>{{ __('quickad.preferences_setting') }}</h2>
                                    </div>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="logged">{{ __('quickad.comments_enabled') }} </label>
                                        <label><input type="checkbox" name="receive">{{ __('quickad.receive_newsletter') }}.</label>
                                    </div>
                                    <!--end row-->
                                </section>
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <button type="submit" class="btn btn-outline" name="submit">
                                            {{ __('quickad.update_account') }}
                                        </button>
                                        <button type="reset" class="btn btn-outline cancel"> {{ __('quickad.cancel') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- My Details -->
                </div>
                <!-- user-pro-edit -->
            </div>
            <!-- # End Page-Content -->
        </div>
        <!-- row -->
    </div>
    <!-- container -->
</section>
<!-- Account-setting-page -->
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