@include('partials.header')
<div id="titlebar" class="margin-bottom-0">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.dashboard') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.dashboard') }}</li>
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
                            <a href="#" class="dashboard-responsive-nav-trigger">
                                <span class="hamburger hamburger--collapse" >
                                    <span class="hamburger-box"><span class="hamburger-inner"></span></span>
                                </span>
                                <span class="trigger-title">{{ __('quickad.dash_navigation') }}</span>
                            </a>
                            <div class="dashboard-nav">
                                <div class="dashboard-nav-inner">
                                    <ul data-submenu-title="{{ __('quickad.my_classified') }}">
                                        <li class="active"><a href="{{ $link['DASHBOARD'] ?? '#' }}"><i class="icon-feather-grid"></i> {{ __('quickad.dashboard') }}</a></li>
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
                                        <li><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}"><i class="icon-feather-settings"></i> {{ __('quickad.account_setting') }}</a></li>
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
                    <div class="content with-padding">
                        <div class="row dashboard-profile">
                            <div class="col-xl-6 col-md-6 col-sm-12">
                                <div class="user-img"><img src="{{ $site_url ?? '' }}storage/profile/{{ $authorimg ?? '' }}" alt="{{ $authorname ?? '' }}" class="img-responsive"></div>
                                <div>
                                    <h2>{{ $authorname ?? '' }}
                                        @if(($sub_image ?? "")!="")
                                        <img src="{{ $sub_image ?? '' }}" alt="{{ $sub_title ?? '' }}" title="{{ $sub_title ?? '' }}" width="28px"/>
                                        @endif
                                    </h2>
                                    <span><i class="icon-feather-gift"></i> {{ __('quickad.membership') }}  :
                                        @if(($sub_title ?? "")!="")
                                            {{ $sub_title ?? '' }}
                                        {{ $else ?? '' }}
                                            {{ __('quickad.free') }}
                                        @endif
                                    </span><br>
                                    <small>{{ __('quickad.username') }}: {{ $username ?? '' }}</small>
                                    <style>
                                        .text-success{ color:#28a745 !important}
                                        .text-danger{ color:#dc3545 !important}
                                    </style>
                                    @if(($sms_verify_mode ?? "")=="1")
                                    <div class="agent-page">
                                        <ul class="agent-contact-details">
                                            @if(($phone_verify ?? "")=="1")
                                            <li><i class="icon-feather-phone-call"></i><a href="tel:{{ $phone ?? '' }}">{{ $phone ?? '' }}</a><img src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/images/verified-badge.png" data-tippy-placement="top" title="{{ __('quickad.verified') }}" width="16px"/>
                                                <a href="#verify-mobile-dialog" class="popup-with-zoom-anim" data-tippy-placement="top" title="{{ __('quickad.change_pno') }}"><i class="icon-feather-edit" style="position: relative;left: 6px;top: 3px;"></i></a></li>
                                            {{ $else ?? '' }}
                                            <li class="text-success"><i class="icon-feather-phone-call"></i><a href="#verify-mobile-dialog" class="text-success popup-with-zoom-anim">{{ __('quickad.verify_mobile_no') }}</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6 col-sm-12 text-right">
                                <span class="dashboard-badge"><strong>{{ $favoriteads ?? '' }}</strong><i class="icon-feather-heart"></i> {{ __('quickad.favourites') }}</span>
                                <span class="dashboard-badge"><strong>{{ $myads ?? '' }}</strong><i class="icon-feather-briefcase"></i> {{ __('quickad.my_ads') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{ $ad_inner_page_horizontal_banner ?? '' }}
                <form method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                    <div class="dashboard-box">
                        <!-- Headline -->
                        <div class="headline">
                            <h3><i class="icon-feather-user"></i> {{ __('quickad.my_details') }}</h3>
                        </div>
                        <div class="content with-padding">
                            <div class="row">
                                <div class="col-xl-6 col-md-12">
                                    <div class="submit-field">
                                        <h5>{{ __('quickad.name') }} *</h5>
                                        <div class="input-with-icon-left">
                                            <i class="la la-user"></i>
                                            <input type="text" class="with-border" name="name" value="{{ $authorname ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-12">
                                    <div class="submit-field">
                                        <h5>{{ __('quickad.address') }}</h5>
                                        <div class="input-with-icon-left">
                                            <i class="la la-location-arrow"></i>
                                            <input type="text" class="with-border" name="address" value="{{ $address ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-12">
                                    <div class="submit-field">
                                        <h5>{{ __('quickad.country') }}</h5>
                                        <select name="country" class="multiselect2">
                                            @foreach($country ?? [] as $country)
                                                <option value="{{ data_get($country ?? [], 'asciiname', '') }}" @if(($country ?? "")==(data_get($country ?? [], 'asciiname', ''))) selected @endif>{{ data_get($country ?? [], 'asciiname', '') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-12">
                                    <div class="submit-field">
                                        <h5>{{ __('quickad.website') }}</h5>
                                        <div class="input-with-icon-left">
                                            <i class="la la-globe"></i>
                                            <input type="text" class="with-border" name="website" value="{{ $website ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="submit-field">
                                        <h5>{{ __('quickad.about_me') }}</h5>
                                        <textarea class="with-border" id="pageContent" rows="2" name="content">{{ $authorabout ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="submit-field">
                                        <h5>{{ __('quickad.avatar') }}</h5>
                                        <div class="BrowseButton">
                                            <input class="BrowseButton-input" type="file" accept="image/*" id="upload" name="avatar"/>
                                            <label class="BrowseButton-button ripple-effect" for="upload">{{ __('quickad.choose_file') }}</label>
                                            <span class="BrowseButton-file-name">{{ __('quickad.logo_hint') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="dashboard-box">
                        <!-- Headline -->
                        <div class="headline">
                            <h3><i class="icon-feather-lock"></i> {{ __('quickad.social_networks') }}</h3>
                        </div>

                        <div class="content with-padding">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>Facebook</h5>
                                        <input type="text" name="facebook" class="with-border" value="{{ $facebook ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>Twitter</h5>
                                        <input type="text" name="twitter" class="with-border" value="{{ $twitter ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>Pinterest</h5>
                                        <input type="text" name="googleplus" class="with-border" value="{{ $googleplus ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>Instagram</h5>
                                        <input type="text" name="instagram" class="with-border" value="{{ $instagram ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>Linked In</h5>
                                        <input type="text" name="linkedin" class="with-border" value="{{ $linkedin ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="submit-field">
                                        <h5>Youtube</h5>
                                        <input type="text" name="youtube" class="with-border" value="{{ $youtube ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-box">
                        <!-- Headline -->
                        <div class="headline">
                            <h3><i class="icon-feather-lock"></i> {{ __('quickad.newsletter') }}</h3>
                        </div>

                        <div class="content with-padding">
                            <div class="row">
                                <div class="col-xl-6 subscribe-category">
                                    <div class="checkbox">
                                        <input type="checkbox" name="notify" id="notify" value="1" onchange="NotifyValueChanged()" @if(($notify ?? "")=="1") checked @endif>
                                        <label for="notify"><span class="checkbox-icon"></span> {{ __('quickad.notifyemail') }}</label>
                                    </div>
                                    <div class="skills" style="margin: 0 25px">
                                        @foreach($category ?? [] as $category)
                                            <div class="checkbox d-block">
                                                <input type="checkbox" name="choice[{{ data_get($category ?? [], 'id', '') }}]" id="{{ data_get($category ?? [], 'id', '') }}" value="{{ data_get($category ?? [], 'id', '') }}" {{ data_get($category ?? [], 'selected', '') }}>
                                                <label for="{{ data_get($category ?? [], 'id', '') }}"><span class="checkbox-icon"></span> {{ data_get($category ?? [], 'name', '') }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="padding-30">
                        <button type="submit" name="submit" class="button ripple-effect">{{ __('quickad.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Verify Mobile Number popup /
================================================== -->
<div id="verify-mobile-dialog" class="zoom-anim-dialog mfp-hide dialog-with-tabs popup-dialog">
    <ul class="popup-tabs-nav">
        <li><a href="#login">{{ __('quickad.verify_mobile_no') }}</a></li>
    </ul>
    <div class="popup-tabs-container">
        <div class="popup-tab-content" id="mobile-form">
            <div class="reset-form d-block">
                <div class="welcome-text">
                    <h3>{{ __('quickad.verify_mobile_no') }}</h3>
                    <span id="mobile-status">{{ __('quickad.otp_notify_1') }}</span>
                </div>
                <form action="dashboard_mobile_verify" name="pv-form" id="pv-form" method="post">
                    <input required class="input-text with-border" id="verify-mobile" name="mobile_no" type="phone" placeholder="{{ __('quickad.phone_no') }}" value="{{ $phone ?? '' }}">
                    <input type="hidden" value="1" name="dashboard_verify"/>
                    <input type="hidden" value="1" name="submit_mobile"/>
                    <button class="button full-width button-sliding-icon ripple-effect" id="mobile-submit" type="submit" name="submit">{{ __('quickad.send_otp') }} <i class="icon-feather-arrow-right"></i></button>
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
                        <button class="button full-width button-sliding-icon ripple-effect" id="otp-submit" type="submit" name="submit">{{ __('quickad.verify_otp') }} <i class="icon-feather-arrow-right"></i></button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
<!-- Verify Mobile Number popup / End -->
<link href="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/css/intlTelInput.css" media="all" rel="stylesheet" type="text/css"/>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/js/intlTelInput.min.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/js/intlTelInput.utils.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/intlTelInput/js/custom.js"></script>

<!-- CRUD FORM CONTENT - crud_fields_scripts stack -->
<link media="all" rel="stylesheet" type="text/css" href="{{ $site_url ?? '' }}includes/assets/plugins/simditor/styles/simditor.css" />
<script src="{{ $site_url ?? '' }}includes/assets/plugins/simditor/scripts/mobilecheck.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/simditor/scripts/module.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/simditor/scripts/uploader.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/simditor/scripts/hotkeys.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/simditor/scripts/simditor.js"></script>
<script>
    (function() {
        $(function() {
            var $preview, editor, mobileToolbar, toolbar, allowedTags;
            Simditor.locale = 'en-US';
            toolbar = ['bold','italic','underline','fontScale','color','|','ol','ul','blockquote','table','link'];
            mobileToolbar = ["bold", "italic", "underline", "ul", "ol"];
            if (mobilecheck()) {
                toolbar = mobileToolbar;
            }
            allowedTags = ['br','span','a','img','b','strong','i','strike','u','p','ul','ol','li','blockquote','pre','h1','h2','h3','h4','hr','table'];
            editor = new Simditor({
                textarea: $('#pageContent'),
                placeholder: '',
                toolbar: toolbar,
                pasteImage: false,
                defaultImage: '{{ $site_url ?? '' }}includes/assets/plugins/simditor/images/image.png',
                upload: false,
                allowedTags: allowedTags
            });
            $preview = $('#preview');
            if ($preview.length > 0) {
                return editor.on('valuechanged', function(e) {
                    return $preview.html(editor.getValue());
                });
            }
        });
    }).call(this);
</script>

<script type="text/javascript">
    function NotifyValueChanged()
    {
        if($('#notify').is(":checked"))
            $(".skills").show();
        else
            $(".skills").hide();
    }
    NotifyValueChanged();
</script>
@include('partials.footer')
