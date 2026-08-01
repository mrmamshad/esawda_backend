@include('partials.header')
<div id="titlebar" class="margin-bottom-0">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.account_setting') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.account_setting') }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="section gray padding-bottom-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <div class="dashboard-sidebar">
                    <div class="dashboard-sidebar-inner">
                        <div class="dashboard-nav-container">
                            <!-- Responsive Navigation Trigger -->
                            <a href="#" class="dashboard-responsive-nav-trigger">
                                <span class="hamburger hamburger--collapse" >
                                    <span class="hamburger-box">
                                        <span class="hamburger-inner"></span>
                                    </span>
                                </span>
                                <span class="trigger-title">{{ __('quickad.dash_navigation') }}</span>
                            </a>

                            <div class="dashboard-nav">
                                <div class="dashboard-nav-inner">
                                    <ul data-submenu-title="{{ __('quickad.my_classified') }}">
                                        <li><a href="{{ $link['DASHBOARD'] ?? '#' }}"><i class="icon-feather-grid"></i> {{ __('quickad.dashboard') }}</a></li>
                                        <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}"><i class="icon-feather-user"></i> {{ __('quickad.profile_public') }}</a></li>
                                        <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}"><i class="icon-feather-gift"></i> {{ __('quickad.membership') }}</a></li>
                                    </ul>

                                    <ul data-submenu-title="{{ __('quickad.my_ads') }}">
                                        <li><a href="{{ $link['MYADS'] ?? '#' }}"><i class="icon-feather-briefcase"></i> {{ __('quickad.my_ads') }} <span class="nav-tag">{{ $myads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['FAVADS'] ?? '#' }}"><i class="icon-feather-heart"></i> {{ __('quickad.favourite_ads') }} <span class="nav-tag">{{ $favoriteads ?? '' }}</span></a></li>

                                        <li><a href="{{ $link['PENDINGADS'] ?? '#' }}"><i class="icon-feather-clock"></i> {{ __('quickad.pending_ads') }} <span class="nav-tag">{{ $pendingads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['HIDDENADS'] ?? '#' }}"><i class="icon-feather-eye-off"></i> {{ __('quickad.hidden_ads') }} <span class="nav-tag">{{ $hiddenads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['EXPIREADS'] ?? '#' }}"><i class="icon-feather-alert-octagon"></i> {{ __('quickad.expire_ads') }} <span class="nav-tag">{{ $expireads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['RESUBMITADS'] ?? '#' }}"><i class="icon-feather-rotate-cw"></i> {{ __('quickad.resubmited_ads') }} <span class="nav-tag">{{ $resubmitads ?? '' }}</span></a></li>
                                    </ul>

                                    <ul data-submenu-title="{{ __('quickad.my_account') }}">
                                        @if(($wchat_on_off ?? "")=='on' || ($quickchat_ajax_on_off ?? "")=='on' || ($quickchat_socket_on_off ?? "")=='on')
                                        <li><a href="{{ $link['MESSAGE'] ?? '#' }}"><i class="icon-feather-message-circle"></i> {{ __('quickad.message') }}</a></li>
                                        @endif
                                        <li><a href="{{ $link['TRANSACTION'] ?? '#' }}"><i class="icon-feather-file-text"></i> {{ __('quickad.transaction') }}</a></li>
                                        <li class="active"><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}"><i class="icon-feather-settings"></i> {{ __('quickad.account_setting') }}</a></li>
                                        <li><a href="{{ $link['LOGOUT'] ?? '#' }}"><i class="icon-feather-log-out"></i> {{ __('quickad.logout') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{ $ad_inner_page_sidebar ?? '' }}
            </div>
            <div class="col-lg-9 col-md-12">
                <div class="dashboard-box margin-top-0">
                    <!-- Headline -->
                    <div class="headline">
                        <h3><i class="icon-feather-settings"></i> {{ __('quickad.account_setting') }}</h3>
                    </div>
                    <div class="content with-padding">
                        <form method="post" accept-charset="UTF-8">
                            <div class="row">
                                <div class="col-xl-6 col-md-12">
                                    <div class="submit-field">
                                        <h5>{{ __('quickad.username') }} *</h5>
                                        <div class="input-with-icon-left">
                                            <i class="la la-user"></i>
                                            <input type="text" class="with-border" id="username" name="username" value="{{ $username ?? '' }}" onBlur="checkAvailabilityUsername()">
                                        </div>
                                        <span id="user-availability-status">@if(($username_error ?? "")!="") {{ $username_error ?? '' }} @endif</span>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-12">
                                    <div class="submit-field">
                                        <h5>{{ __('quickad.email') }} *</h5>
                                        <div class="input-with-icon-left">
                                            <i class="la la-envelope"></i>
                                            <input type="text" class="with-border" id="email" name="email" value="{{ $email_field ?? '' }}" onBlur="checkAvailabilityEmail()">
                                        </div>
                                        <span id="email-availability-status">@if(($email_error ?? "")!="") {{ $email_error ?? '' }} @endif</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>{{ __('quickad.newpass') }}</h5>
                                        <input type="password" id="password" name="password" class="with-border" onkeyup="checkAvailabilityPassword()">
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>{{ __('quickad.conpass') }}</h5>
                                        <input type="password" id="re_password" name="re_password" class="with-border" onkeyup="checkRePassword()">
                                    </div>
                                </div>
                            </div>
                            <span id="password-availability-status">@if(($password_error ?? "")!="") {{ $password_error ?? '' }} @endif</span>
                            <button type="submit" name="submit" class="button ripple-effect">{{ __('quickad.save_changes') }}</button>
                        </form>
                    </div>
                </div>
                <div class="dashboard-box">
                    <div class="headline">
                        <h3><i class="icon-material-outline-description"></i> {{ __('quickad.billing_details') }}</h3>
                    </div>
                    <div class="content">
                        <div class="content with-padding">
                            <div class="notification notice">{{ __('quickad.billing_details_notes') }}</div>
                            @if(($billing_error ?? "")=="1")
                            <div class="notification error">{{ __('quickad.all_fields_req') }}</div>
                            @endif
                            <form method="post" accept-charset="UTF-8">
                                <div class="submit-field">
                                    <h5>{{ __('quickad.type') }}</h5>
                                    <select name="billing_details_type" id="billing_details_type"  class="with-border selectpicker" required>
                                        <option value="personal" @if(($billing_details_type ?? "")=="personal") selected @endif>{{ __('quickad.personal') }}</option>
                                        <option value="business" @if(($billing_details_type ?? "")=="business") selected @endif>{{ __('quickad.business') }}</option>
                                    </select>
                                </div>
                                <div class="submit-field billing-tax-id">
                                    <h5>@if(($invoice_admin_tax_type ?? "")!="") {{ $invoice_admin_tax_type ?? '' }} {{ $else ?? '' }} {{ __('quickad.tax_id') }}@endif</h5>
                                    <input type="text" id="billing_tax_id" name="billing_tax_id" class="with-border" value="{{ $billing_tax_id ?? '' }}">
                                </div>
                                <div class="submit-field">
                                    <h5>{{ __('quickad.name') }} *</h5>
                                    <input type="text" id="billing_name" name="billing_name" class="with-border" value="{{ $billing_name ?? '' }}" required>
                                </div>
                                <div class="submit-field">
                                    <h5>{{ __('quickad.address') }} *</h5>
                                    <input type="text" id="billing_address" name="billing_address" class="with-border" value="{{ $billing_address ?? '' }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="submit-field">
                                            <h5>{{ __('quickad.city') }} *</h5>
                                            <input type="text" id="billing_city" name="billing_city" class="with-border" value="{{ $billing_city ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="submit-field">
                                            <h5>{{ __('quickad.state') }} *</h5>
                                            <input type="text" id="billing_state" name="billing_state" class="with-border" value="{{ $billing_state ?? '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="submit-field">
                                            <h5>{{ __('quickad.zipcode') }} *</h5>
                                            <input type="text" id="billing_zipcode" name="billing_zipcode" class="with-border" value="{{ $billing_zipcode ?? '' }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-field">
                                    <h5>{{ __('quickad.country') }} *</h5>
                                    <select name="billing_country" id="billing_country" class="with-border selectpicker" data-live-search="true" required>
                                        @foreach($countries ?? [] as $countries)
                                            <option value="{{ data_get($countries ?? [], 'code', '') }}" {{ data_get($countries ?? [], 'selected', '') }}>{{ data_get($countries ?? [], 'asciiname', '') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" name="billing-submit" class="button ripple-effect">{{ __('quickad.save_changes') }}</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var error = "";
    function checkAvailabilityUsername() {
        jQuery.ajax({
            url: ajaxurl,
            data : {
                action: 'check_availability',
                username: $("#username").val()
            },
            type: "POST",
            success: function (data) {
                if (data != "success") {
                    error = 1;
                    $("#user-availability-status").html(data);
                }
                else {
                    error = 0;
                    $("#user-availability-status").html("");
                }
            },
            error: function () {
            }
        });
    }
    function checkAvailabilityEmail() {
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
                    $("#email-availability-status").html(data);
                }
                else {
                    error = 0;
                    $("#email-availability-status").html("");
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
                $("#password-availability-status").html("<span class='status-not-available'>" + PASSLENG + "</span>");
            }
            else {
                $("#password-availability-status").html("");
            }
        }

    }

    function checkRePassword(){
        if($('#password').val() != $('#re_password').val()){
            var PASS = "{{ __('quickad.passnomatch') }}";
            $("#password-availability-status").html("<span class='status-not-available'>" + PASS + "</span>");
        }else{
            $("#password-availability-status").html("");
        }
    }

    jQuery(window).load(function (e) {
        jQuery('#password').val("");
    });
</script>
@include('partials.footer')
