<!DOCTYPE html>
<html lang="{{ __('quickad.code') }}" dir="{{ $language_direction ?? '' }}">
<head>
    <title>
        @if(($page_title ?? "")!="")
        {{ $page_title ?? '' }} -
        @endif
        {{ $site_title ?? '' }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="{{ $site_title ?? '' }}">
    <meta name="keywords" content="{{ $page_meta_keywords ?? '' }}">
    <meta name="description" content="{{ $page_meta_description ?? '' }}">

    <meta property="fb:app_id" content="{{ $facebook_app_id ?? '' }}" />
    <meta property="og:site_name" content="{{ $site_title ?? '' }}" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:url" content="{{ $page_link ?? '' }}" />
    <meta property="og:title" content="@if(($page_title ?? "")!="") {{ $page_title ?? '' }} - @endif{{ $site_title ?? '' }}" />
    <meta property="og:description" content="{{ $page_meta_description ?? '' }}" />
    <meta property="og:type" content="{{ $meta_content ?? '' }}" />
    @if(($meta_content ?? "")=="article")
    <meta property="article:author" content="#" />
    <meta property="article:publisher" content="#" />
    <meta property="og:image" content="{{ $meta_image ?? '' }}" />
    <meta property="og:image:width" content="800" />
    <meta property="og:image:height" content="800" />
    @endif
    @if(($meta_content ?? "")=="website")
    <meta property="og:image" content="{{ $meta_image ?? '' }}" />
    @endif

    <meta property="twitter:card" content="summary">
    <meta property="twitter:title" content="{{ $page_title ?? '' }} - {{ $site_title ?? '' }}">
    <meta property="twitter:description" content="{{ $page_meta_description ?? '' }}">
    <meta property="twitter:domain" content="{{ $site_url ?? '' }}">
    <meta name="twitter:image:src" content="{{ $meta_image ?? '' }}" />

    <link rel="shortcut icon" href="{{ $site_url ?? '' }}storage/logo/{{ $site_favicon ?? '' }}">
    <style>
        :root {
            --theme-color: transparent;
        }
        .highlight-premium-ad{ background: #ffedc0 !important;}
    </style>
    <script>
        var themecolor = '{{ $theme_color ?? '' }}';
        var mapcolor = '{{ $map_color ?? '' }}';
        var siteurl = '{{ $site_url ?? '' }}';
        var template_name = '{{ $tpl_name ?? '' }}';
    </script>
    <link rel='stylesheet' href='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/fonts/elegant-fonts.css' type='text/css' />
    <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/css/icons.css">
    <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/flags/flags.min.css">
    <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/styleswitcher/styleswitcher.css">

    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Lato%3A400%2C300%2C700%2C900%2C400italic%7COpen+Sans%3A700italic%2C400%2C800%2C600&#038;subset=latin%2Clatin-ext&#038;ver=4.7.3' type='text/css' />
    <link rel='stylesheet'  href='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/bootstrap/css/bootstrap.css' type='text/css' />
    <link rel='stylesheet' href='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/css/bootstrap-select.min.css' type='text/css' />

    <link rel='stylesheet'  href='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/css/owl.carousel.css' type='text/css' />
    <link rel='stylesheet' href='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/css/trackpad-scroll-emulator.css' type='text/css' />
    <link rel='stylesheet' href='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/css/jquery.nouislider.min.css' type='text/css' />
    <link rel='stylesheet'  href='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/css/style.css' type='text/css' />
    <link rel='stylesheet' href='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/css/main-theme.css' type='text/css' />
    <link rel='stylesheet' href='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/materialize/css/materialize.css' type='text/css' />
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/css/membership.css" >
    <!--Sweet Alert CSS -->
    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/sweetalert/sweetalert.css" rel="stylesheet" type="text/css">

    <script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/js/jquery-2.2.1.min.js'></script>
    <script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/styleswitcher/jquery.style-switcher.js'></script>

    @if(($language_direction ?? "")=="rtl")
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/css/rtl.css">
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/css/bootstrap-rtl.min.css">
    @endif

    <script>var ajaxurl = "{{ $app_url ?? '' }}user-ajax.php";</script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.resend').click(function(e) { 						// Button which will activate our modal

                the_id = $(this).attr('id');						//get the id

                // show the spinner
                $(this).parent().html("<img src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/images/spinner.gif'/>");

                $.ajax({											//the main ajax request
                    type: "POST",
                    data: "action=email_verify&id="+$(this).attr("id"),
                    url: ajaxurl,
                    success: function(data)
                    {
                        $("span#resend_count"+the_id).html(data);
                        //fadein the vote count
                        $("span#resend_count"+the_id).fadeIn();
                        //remove the spinner
                        $("span#resend_buttons"+the_id).remove();

                    }
                });

                return false;
            });
        });
    </script>
    <!-- ==================================
    ===============External Code===========
    ======================================= -->
    {{ $external_code ?? '' }}
    <!-- ==================================
    ===============External Code===========
    ======================================= -->
</head>
<body class="{{ $language_direction ?? '' }}">

<!--*********************************Modals*************************************-->
<div class="modal fade modalHasList" id="selectCountry" tabindex="-1" role="dialog" aria-labelledby="selectCountryLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">{{ __('quickad.close') }}</span>
                </button>
                <h4 class="modal-title uppercase font-weight-bold" id="selectCountryLabel">
                    <i class="icon-map"></i> {{ __('quickad.select_your_country') }}
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div style="padding: 20px">
                        <ul class="column col-md-12 col-sm-12 cities">
                            @foreach($countrylist ?? [] as $countrylist)
                                <li><span class="flag flag-{{ data_get($countrylist ?? [], 'lowercase_code', '') }}"></span> <a href="{{ $link['HOME'] ?? '#' }}/{{ data_get($countrylist ?? [], 'lang', '') }}/{{ data_get($countrylist ?? [], 'lowercase_code', '') }}" data-id="{{ data_get($countrylist ?? [], 'id', '') }}" data-name="{{ data_get($countrylist ?? [], 'name', '') }}"> {{ data_get($countrylist ?? [], 'name', '') }}</a></li>
                            @endforeach

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="loginPopUp">
    <i class="loading-icon fa fa-circle-o-notch fa-spin"></i>
    <div class="modal-dialog width-400px" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <div class="section-title">
                    <h2>{{ __('quickad.login') }}</h2>
                </div>
            </div>
            <div class="socialLoginHere">
                <div class="row text-center">
                    <div class="col-xs-6"><a class="loginBtn loginBtn--facebook" onclick="fblogin()"><i class="fa fa-facebook"></i> <span>Facebook</span></a></div>
                    <div class="col-xs-6"><a class="loginBtn loginBtn--google" onclick="gmlogin()"><i class="fa fa-google-plus"></i> <span>Google+</span></a></div>
                </div>
                <div class="clear"></div>
            </div>
            <div id="login-status" class="info-notice" style="display: none;margin-bottom: 20px">
                <div class="content-wrapper">
                    <div id="login-detail">
                        <div id="login-status-icon-container"><span class="login-status-icon"></span></div>
                        <div id="login-status-message">{{ __('quickad.authenticating') }}...</div>
                    </div>
                </div>
            </div>
            <form action="ajaxlogin" id="lg-form">
                <div class="modal-body">
                <form class="form inputs-underline">
                    <div class="form-group">
                        <label for="username">{{ __('quickad.username') }} / {{ __('quickad.email') }}</label>
                        <input type="email" class="form-control" id="username" placeholder="{{ __('quickad.username') }} / {{ __('quickad.email') }}">
                    </div>
                    <!--end form-group-->
                    <div class="form-group">
                        <label for="password">{{ __('quickad.password') }}</label>
                        <input type="password" class="form-control" id="password" placeholder="{{ __('quickad.password') }}">
                    </div>
                    <div class="form-group center">
                        <button id="login" type="button" class="btn btn-primary width-100">{{ __('quickad.login') }}</button>
                    </div>
                    <!--end form-group-->
                </form>

                <hr>

                <a href="{{ $link['LOGIN'] ?? '#' }}?fstart=1">{{ __('quickad.forgotpass') }}?</a>
                <!--end form-->
            </div>
                <!--end modal-body-->
            </form>
        </div>
        <!--end modal-content-->
    </div>
    <!--end modal-dialog-->
</div>
<!--*********************************Modals*************************************-->
@if(($userstatus ?? "")=="0")
<div class="pam fbPageBanner uiBoxYellow noborder">
    <div class="fbPageBannerInner">
        <table class="uiGrid _51mz _5ud_" cellspacing="0" cellpadding="0">
            <tbody>
            <tr class="_51mx">
                <td class="_51m- phm" style="width:78%">
                    <span class="uiIconText">
                        <i class="icon-lock text-18"></i>
                        <span class="pts5 fsl fwb fs13 fbold">{{ __('quickad.welcome') }} <span class="coffel">{{ $username ?? '' }}</span>, go to <span class="coffel">{{ $useremail ?? '' }}</span> {{ __('quickad.to') }} {{ __('quickad.verify_email_address') }}</span>
                    </span>
                </td>
                <td class="_51m- phm _51mw">
                    <table class="uiGrid _51mz _5ud-" cellspacing="0" cellpadding="0">
                        <tbody>
                        <tr class="_51mx">
                            <td class="_51m- phm"><a class="uiButton uiButtonLarge" style="box-sizing:content-box;" onMouseOver="LinkshimAsyncLink.swap(this, "http:\/\/www.{{ $emaildomain ?? '' }}\/");" rel="nofollow" target="_blank" role="button" href="http://www.{{ $emaildomain ?? '' }}/"><span class="uiButtonText">{{ __('quickad.goto_ur_email') }}</span></a>
                            </td>
                            <td class="_51m- phm _51mw">
                                <span class='resend_buttons' id='resend_buttons{{ $user_id ?? '' }}'><a class="uiButton uiButtonLarge resend" style="box-sizing:content-box;" href='javascript:;' id="{{ $user_id ?? '' }}"><span class="uiButtonText">{{ __('quickad.resend_email') }}</span></a></span>
                                <span class='resend_count' id='resend_count{{ $user_id ?? '' }}' style="box-sizing:content-box;"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
@endif
<div class="page-wrapper" id="page-header">
    <!--start header section header v1-->
    <header class="header-section-default header-main nav-left hidden-sm hidden-xs">
        <div class="container">
            <div class="header-left">
                <div class="logo">
                    <a href="{{ $link['INDEX'] ?? '#' }}">
                        <img src="{{ $site_url ?? '' }}storage/logo/material-theme_logo.png" alt="logo">
                    </a>
                </div>
                @if(($country_type ?? "")=="multi")
                <div class="pull-left">
                    <button class="flag-menu country-flag btn btn-default" id="#selectCountry" data-toggle="modal" data-target="#selectCountry" style="margin-left: 20px;">
                        <img src="{{ $site_url ?? '' }}includes/assets/plugins/flags/images/{{ $user_country ?? '' }}.png" style="float: left;">
                    </button>
                </div>
                @endif
                <nav class="navi main-nav">
                    <ul>
                        <li><a href="{{ $link['HOME'] ?? '#' }}">{{ __('quickad.home') }}</a>
                            <ul class="sub-menu">
                                <li> <a class="" href="{{ $link['INDEX1'] ?? '#' }}">{{ __('quickad.home_image') }} </a> </li>
                                <li> <a class="" href="{{ $link['INDEX2'] ?? '#' }}">{{ __('quickad.home_map') }} </a> </li>
                            </ul>
                        </li>
                        <li><a href="{{ $link['LISTING'] ?? '#' }}">{{ __('quickad.listing') }}</a></li>
                        <li><a href="{{ $link['CONTACT'] ?? '#' }}">{{ __('quickad.contact') }}</a></li>
                        @if(($lang_sel ?? "")=="1")
                        <li>
                            <a href="#"><button class="btn btn-default" type="button">{{ __('quickad.code') }}<span
                                            class="caret"></span></button></a>
                            <ul class="sub-menu">
                                @foreach($langs ?? [] as $langs)
                                    <li><a href="{{ $link['HOME'] ?? '#' }}/{{ data_get($langs ?? [], 'code', '') }}">{{ data_get($langs ?? [], 'name', '') }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @endif

                    </ul>
                </nav>

            </div>
            <div class="header-right">
                @if(($logged_in ?? "")=="0")
                <div class="user">
                    <a href="#" data-toggle="modal" data-target="#loginPopUp" class="waves-effect pad-lr-10 modal-trigger">{{ __('quickad.sign_in') }}</a>
                    <a href="{{ $link['SIGNUP'] ?? '#' }}" class="waves-effect pad-lr-10">{{ __('quickad.register') }}</a>
                    <a href="{{ $link['POST-AD'] ?? '#' }}" class="btn btn-rounded btn-default waves-effect">{{ __('quickad.post_free_ad') }}</a>
                </div>
                @endif
                @if((($logged_in ?? "")) && (($wchat_on_off ?? "")=='on' || ($quickchat_ajax_on_off ?? "")=='on' || ($quickchat_socket_on_off ?? "")=='on'))
                <div class="user">
                    <a href="{{ $link['MESSAGE'] ?? '#' }}" class="waves-effect message-link"><i class="fa fa-envelope"></i> </a>
                </div>
                @endif
                @if(($logged_in ?? "")=="1")
                <ul class="account-action">

                    <li class="">
                        <span class="hidden-sm hidden-xs">{{ $username ?? '' }} <i class="fa fa-angle-down"></i></span>
                        <img src="{{ $site_url ?? '' }}storage/profile/{{ $userpic ?? '' }}" alt="{{ $username ?? '' }}" class="user-image" height="22" width="22">

                        <div class="account-dropdown">
                            <ul>
                                <li><a href="{{ $link['DASHBOARD'] ?? '#' }}" class="waves-effect"> <i class="fa fa-file"></i>{{ __('quickad.my_classified') }}</a></li>
                                <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}" class="waves-effect"> <i class="fa fa-user"></i>{{ __('quickad.profile_public') }}</a></li>
                                <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="waves-effect"> <i class="fa fa-plus-circle"></i>{{ __('quickad.post_free_ad') }}</a></li>
                                <li><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"> <i class="fa fa-building"></i>{{ __('quickad.my_ads_listings') }}</a></li>
                                <li><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"> <i class="fa fa-heart"></i>{{ __('quickad.favourite_ads') }}</a></li>
                                <li><a href="{{ $link['PENDINGADS'] ?? '#' }}" class="waves-effect"> <i class="fa fa-clock-o"></i>{{ __('quickad.my_pending_ads') }}</a></li>
                                <li><a href="{{ $link['LOGOUT'] ?? '#' }}" class="waves-effect"> <i class="fa fa-unlock"></i>{{ __('quickad.logout') }}</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>

                @endif
            </div>
        </div>
    </header>
    <div class="header-mobile visible-sm visible-xs">
        <div class="container">
            <!--start mobile nav-->
            <div class="mobile-nav">
                <span class="nav-trigger"><i class="fa fa-navicon"></i></span>
                <div class="nav-dropdown main-nav-dropdown"></div>
            </div>
            <!--end mobile nav-->
            <div class="header-logo">
                <a href="{{ $link['INDEX'] ?? '#' }}"><img src="{{ $site_url ?? '' }}storage/logo/{{ $site_logo ?? '' }}" alt="logo"></a>
            </div>
            <div class="header-user">

                <ul class="account-action">
                    <li>
                        @if(($logged_in ?? "")=="0")
                        <span class="user-image"><i class="fa fa-ellipsis-v"></i></span>
                        @endif
                        @if(($logged_in ?? "")=="1")
                        <img src="{{ $site_url ?? '' }}storage/profile/{{ $userpic ?? '' }}" alt="{{ $username ?? '' }}" class="user-image" width="36" height="36">
                        @endif
                        <div class="account-dropdown">
                            <ul>
                                @if(($logged_in ?? "")=="0")
                                <li><a href="{{ $link['LOGIN'] ?? '#' }}" class="waves-effect"><i class="fa fa-user"></i> {{ __('quickad.sign_in') }}</a></li>
                                <li><a href="{{ $link['SIGNUP'] ?? '#' }}" class="waves-effect"><i class="fa fa-lock"></i> {{ __('quickad.register') }}</a></li>
                                <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="waves-effect"><i class="fa fa-plus-circle"></i> {{ __('quickad.post_free_ad') }}</a></li>
                                @endif
                                @if(($logged_in ?? "")=="1")
                                <li><a href="{{ $link['DASHBOARD'] ?? '#' }}" class="waves-effect"> <i class="fa fa-file"></i>{{ __('quickad.my_classified') }}</a></li>
                                <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}" class="waves-effect"> <i class="fa fa-user"></i>{{ __('quickad.profile_public') }}</a></li>
                                <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="waves-effect"> <i class="fa fa-plus-circle"></i>{{ __('quickad.post_free_ad') }}</a></li>
                                <li><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"> <i class="fa fa-building"></i>{{ __('quickad.my_ads_listings') }}</a></li>
                                <li><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"> <i class="fa fa-heart"></i>{{ __('quickad.favourite_ads') }}</a></li>
                                <li><a href="{{ $link['PENDINGADS'] ?? '#' }}" class="waves-effect"> <i class="fa fa-clock-o"></i>{{ __('quickad.my_pending_ads') }}</a></li>
                                <li><a href="{{ $link['LOGOUT'] ?? '#' }}" class="waves-effect"> <i class="fa fa-unlock"></i>{{ __('quickad.logout') }}</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--end header section header v1-->


