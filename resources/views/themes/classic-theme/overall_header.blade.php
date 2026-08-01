<!DOCTYPE html>
<html lang="{{ __('quickad.code') }}" dir="{{ $language_direction ?? '' }}">
<head>
    <title>@if(($page_title ?? "")!="") {{ $page_title ?? '' }} - @endif{{ $site_title ?? '' }}</title>
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

    <script async>
        var themecolor = '{{ $theme_color ?? '' }}';
        var mapcolor = '{{ $map_color ?? '' }}';
        var siteurl = '{{ $site_url ?? '' }}';
        var template_name = '{{ $tpl_name ?? '' }}';
    </script>
    <!-- CSS -->
    <style>
        :root{@foreach($colors ?? [] as $colors)--theme-color-{{ data_get($colors ?? [], 'id', '') }}: {{ data_get($colors ?? [], 'value', '') }};@endforeach}
        /*=====Pre-Load-Wrap=====*/
        .load-wrapp{background:#fff;color:#fff;position:fixed;left:0;top:0;width:100%;height:100%;z-index:99999;text-align:center;display:flex;flex-direction:column;justify-content:center}.load-wrapp .wrap{position:absolute;left:50%;top:50%;transform:translateX(-50%) translateY(-50%)}.load-wrapp .wrap ul.dots-box{position:relative;width:80px;height:80px;list-style:none}.load-wrapp .wrap ul.dots-box li.dot{width:100%;height:100%;border-radius:52px;top:0;left:0;z-index:99;text-indent:-9999px;display:block;position:absolute;border:none;animation-iteration-count:infinite;animation-timing-function:linear;animation-name:orbit;animation-duration:4s}.load-wrapp .wrap ul.dots-box li.dot span{background:#1c90f3;background: var(--theme-color);bottom:0;left:50%;margin-left:-2px;display:block;position:absolute;width:10px;height:10px;border-radius:10px}.load-wrapp .wrap ul.dots-box li:nth-child(2){animation-delay:.2s}.load-wrapp .wrap ul.dots-box li:nth-child(3){animation-delay:.4s}.load-wrapp .wrap ul.dots-box li:nth-child(4){animation-delay:.6s}.load-wrapp .wrap ul.dots-box li:nth-child(5){animation-delay:.8s}@keyframes orbit{0%{transform:rotate(0);opacity:1}5%{transform:rotate(90deg);opacity:1}45%{transform:rotate(270deg);opacity:1}55%{transform:rotate(540deg);opacity:1}75%{transform:rotate(630deg);opacity:1}100%,80%{transform:rotate(720deg);opacity:0}}
        /*=====End-Pre-Load-Wrap=====*/
    </style>
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/bootstrap.min.css">
    <noscript id="deferred-styles">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/css/icons.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/flags/flags.min.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/styleswitcher/styleswitcher.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/map/google/map-marker.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/owl.carousel.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/slidr.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/main.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/ajax-search.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/membership.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/responsive.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/sweetalert/sweetalert.css" type="text/css">
        @if(($language_direction ?? "")=="rtl")
        <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/rtl.css">
        <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/bootstrap-rtl.min.css">
        @endif

        <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/color.css">
    </noscript>


    <!-- icons -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script async src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script async src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Template Developed By Bylancer -->
    <script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/jquery-2.2.1.min.js'></script>
    <script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/styleswitcher/jquery.style-switcher.js'></script>
    <script async type="text/javascript" src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/mmenu.min.js"></script>

    <script async>var ajaxurl = "{{ $app_url ?? '' }}user-ajax.php";</script>

    <script async type="text/javascript">
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

    <script>
        var loadDeferredStyles = function() {
            var addStylesNode = document.getElementById("deferred-styles");
            var replacement = document.createElement("div");
            replacement.innerHTML = addStylesNode.textContent;
            document.body.appendChild(replacement)
            addStylesNode.parentElement.removeChild(addStylesNode);
        };
        var raf = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
                window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
        if (raf) raf(function() { window.setTimeout(loadDeferredStyles, 0); });
        else window.addEventListener('load', loadDeferredStyles);
    </script>
    <!-- ==================================
    ===============External Code===========
    ======================================= -->
    {{ $external_code ?? '' }}
    <!-- ==================================
    ===============External Code===========
    ======================================= -->
</head>
<body class="{{ $language_direction ?? '' }}" id="page" data-ipapi="{{ $live_location_api ?? '' }}" data-showlocationicon="{{ $location_track_icon ?? '' }}">
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<div class="load-wrapp">
    <div class="wrap">
        <ul class="dots-box">
            <li class="dot"><span></span></li>
            <li class="dot"><span></span></li>
            <li class="dot"><span></span></li>
            <li class="dot"><span></span></li>
            <li class="dot"><span></span></li>
        </ul>
    </div>
</div>
<!-- Wrapper -->
<div id="wrapper">

    <!-- Header Container
     ================================================== -->
    <header id="header-container">

        <!-- Header -->
        <div id="header">
            <div class="container">

                <!-- Left Side Content -->
                <div class="left-side">
                    <!-- Logo -->
                    <div id="logo">
                        <a href="{{ $link['INDEX'] ?? '#' }}"><img src="{{ $site_url ?? '' }}storage/logo/{{ $site_logo ?? '' }}" alt=""></a>
                    </div>
                    <!-- Mobile Navigation -->
                    <button class="btn btn-primary hidden" id="change-city" data-toggle="modal" data-target="#countryModal">{{ __('quickad.select_city') }}</span></button>
                    @if(($country_type ?? "")=="multi")
                    <div class="mmenu-trigger" id="#selectCountry" data-toggle="modal" data-target="#selectCountry">
                        <button class="hamburger hamburger--collapse country-flag">
                            <img src="{{ $site_url ?? '' }}includes/assets/plugins/flags/images/{{ $user_country ?? '' }}.png">
                        </button>
                    </div>
                    @endif
                    @if(($country_type ?? "")=="multi")
                    <!-- Main Navigation -->
                    <nav id="navigation" class="style-1">
                        <ul id="responsive">
                            <li>
                                <div class="flag-menu">

                                    <button class="hamburger hamburger--collapse country-flag" id="#selectCountry" data-toggle="modal" data-target="#selectCountry">
                                        <img src="{{ $site_url ?? '' }}includes/assets/plugins/flags/images/{{ $user_country ?? '' }}.png">
                                    </button>

                                </div>
                            </li>
                        </ul>
                    </nav>
                    @endif

                    <div class="clearfix"></div>
                    <!-- Main Navigation / End -->

                </div>
                <!-- Left Side Content / End -->


                <!-- Right Side Content / End -->
                <div class="right-side">
                    <div class="header-widget">
                        @if((($logged_in ?? "")) && (($wchat_on_off ?? "")=='on' || ($quickchat_ajax_on_off ?? "")=='on' || ($quickchat_socket_on_off ?? "")=='on'))
                        <a href="{{ $link['MESSAGE'] ?? '#' }}" class="sign-in popup-with-zoom-anim hidden-xs"><i class="fa fa-envelope"></i> <span class="hidden-xs">{{ __('quickad.message') }}</span></a>
                        @endif
                        @if(($logged_in ?? "")=="1")
                        <!-- User Menu -->
                        <div class="user-menu">
                            <div class="user-name"><span><img src="{{ $site_url ?? '' }}storage/profile/{{ $userpic ?? '' }}" alt="{{ $username ?? '' }}"></span>{{ $username ?? '' }}</div>
                            <ul>
							    <li><a href="{{ $link['POST-AD'] ?? '#' }}" ><i class="fa fa-plus-circle"></i> {{ __('quickad.post_free_ad') }} </a></li>
                                <li><a href="{{ $link['DASHBOARD'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.dashboard') }}</a></li>
                                <li><a href="{{ $link['MYADS'] ?? '#' }}"><i class="fa fa-book"></i> {{ __('quickad.my_ads') }}</a></li>
                                <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}"><i class="fa fa-user"></i> {{ __('quickad.my_profile') }}</a></li>
                                <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}"><i class="fa fa-shopping-bag"></i> {{ __('quickad.membership') }}</a></li>
                                <li><a href="{{ $link['TRANSACTION'] ?? '#' }}"><i class="fa fa-money"></i> {{ __('quickad.transaction') }}</a></li>
                                <li><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}"><i class="fa fa-cog"></i> {{ __('quickad.account_setting') }}</a></li>
                                <li><a href="{{ $link['LOGOUT'] ?? '#' }}"><i class="fa fa-unlock"></i> {{ __('quickad.logout') }}</a></li>
                            </ul>
                        </div>
                        @endif
                        @if(($logged_in ?? "")=="0")
                        <a href="{{ $link['LOGIN'] ?? '#' }}" class="sign-in popup-with-zoom-anim"><i class="fa fa-sign-in"></i> {{ __('quickad.login') }}</a>

                        <a href="{{ $link['SIGNUP'] ?? '#' }}" class="sign-in popup-with-zoom-anim"> {{ __('quickad.register') }}</a>

                        @endif
                        <a href="{{ $link['POST-AD'] ?? '#' }}" class="hidden-xs button border with-icon">{{ __('quickad.post_free_ad') }} <i class="fa fa-plus-circle"></i></a>
                        <!-- lang-dropdown -->
                        @if(($lang_sel ?? "")=="1")
                        <div class="dropdown lang-dropdown" id="lang-dropdown">
                            <button class="btn dropdown-toggle btn-default-lite" type="button" id="dropdownMenu1"
                                    data-toggle="dropdown" aria-expanded="false"><span id="selected_lang">{{ __('quickad.code') }}</span><span
                                        class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu1">
                                @foreach($langs ?? [] as $langs)
                                    <li><a role="menuitem" tabindex="-1" rel="alternate" href="{{ $link['HOME'] ?? '#' }}/{{ data_get($langs ?? [], 'code', '') }}">{{ data_get($langs ?? [], 'name', '') }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <!-- lang-dropdown -->
                    </div>
                </div>
                <!-- Right Side Content / End -->
            </div>
        </div>
        <!-- Header / End -->

    </header>
    <div class="clearfix"></div>
    <!-- Header Container / End -->


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
                        <div class="row" style="padding: 0 20px">
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
    <div class="modal" id="countryModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="top:23px">
                <div class="quick-states" id="country-popup" data-country-id="{{ $default_country_id ?? '' }}" style="display: block;">
                    <div id="regionSearchBox" class="title clr">
                        <a class="closeMe icon close fa fa-close" data-dismiss="modal" title="Close"></a>

                        <div class="clr row">
                            @if(($country_type ?? "")=="multi")
                            <span style="line-height: 30px;">
                                <span class="flag flag-{{ $user_country ?? '' }}"></span> <a href="#"  id="#selectCountry" data-toggle="modal" data-target="#selectCountry">{{ __('quickad.change_country') }}</a>
                            </span>
                            @endif
                            <div class="locationrequest smallBox br5 col-sm-4">
                                <div class="rel input-container"><span class="watermark_container" style="display: block;">
                                <input class="light cityfield ca2" type="text" id="inputStateCity" placeholder="{{ __('quickad.type_your_city') }}">
                                </span>
                                    <label for="inputStateCity" class="icon locmarker2 abs"><i class="fa fa-map-marker"></i></label>

                                    <div id="searchDisplay"></div>
                                    <div class="suggest bottom abs small br3 error hidden"><span
                                                class="target abs icon"></span>

                                        <p></p>
                                    </div>
                                </div>
                                <div id="lastUsedCities" class="last-used binded" style="display: none;">{{ __('quickad.last_visited') }}:
                                    <ul id="last-locations-ul">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="popular-cities clr">
                        <p>{{ __('quickad.popular_cities') }}:</p>

                        <div class="list row">

                            <ul class="col-lg-12 col-md-12 popularcity">
                                @foreach($popularcity ?? [] as $popularcity)
                                {{ data_get($popularcity ?? [], 'tpl', '') }}
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="viewport">

                        <div class="row full" id="getCities">
                            <div class="col-sm-12 col-md-12 loader" style="display: none"></div>
                            <div id="results" class="animate-bottom">
                                <ul class="column col-md-12 col-sm-12 cities">
                                    @foreach($statelist ?? [] as $statelist)
                                    {{ data_get($statelist ?? [], 'tpl', '') }}
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="table full subregionslinks hidden" id="subregionslinks"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loginPopUp" class="modal-container"><a href="#" class="modal-overlay"> {{ __('quickad.close_modal') }}</a>
        <div class="inner">
            <button class="close_modal"><i class="fa fa-remove"></i></button>

            <div class='socialLoginDiv @if(($facebook_app_id ?? "")($google_app_id ?? "")=="") hidden @endif'>
                <div class="socialLoginHere">
                    <div class="row text-center">
                        @if(($facebook_app_id ?? "")!="")
                        <div class="col-xs-6"><a class="loginBtn loginBtn--facebook" onclick="fblogin()"><i
                                        class="fa fa-facebook"></i> <span>Facebook</span></a></div>
                        @endif
                        @if(($google_app_id ?? "")!="")
                        <div class="col-xs-6"><a class="loginBtn loginBtn--google" onclick="gmlogin()"><i
                                        class="fa fa-google"></i> <span>Google</span></a></div>
                        @endif
                    </div>
                    <div class="clear"></div>
                </div>
                <span class="split-opt">or</span>
            </div>
            <div class="modal-content signin text-center">
                <div id="login-status" class="info-notice" style="display: none;margin-bottom: 20px">
                    <div class="content-wrapper">
                        <div id="login-detail">
                            <div id="login-status-icon-container"><span class="login-status-icon"></span></div>
                            <div id="login-status-message">{{ __('quickad.authenticating') }}...</div>
                        </div>
                    </div>
                </div>
                <form action="ajaxlogin" id="lg-form">
                    <header>
                        <h4>{{ __('quickad.welcome_back') }}!</h4>

                        <p>{{ __('quickad.enter_details') }}</p>
                    </header>
                    <div class="field-block">
                        <div class="labeled-input">
                            <input type="text" id="username" placeholder="{{ __('quickad.username') }} / {{ __('quickad.email') }}">
                        </div>
                    </div>
                    <div class="field-block">
                        <div class="labeled-input">
                            <input id="password" type="password" placeholder="{{ __('quickad.password') }}">
                        </div>
                    </div>
                    <div class="text-center"><a href="{{ $link['LOGIN'] ?? '#' }}?fstart=1">{{ __('quickad.forgotpass') }}?</a></div>
                    <button id="login" href="#" class="btn field-block">{{ __('quickad.login') }}</button>
                    <div class="login-cta text-center"><a href="{{ $link['SIGNUP'] ?? '#' }}">{{ __('quickad.create_new_account') }}</a></div>
                </form>
            </div>
        </div>
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
                            <span class="pts5 fsl fwb fs13 fbold">{{ __('quickad.welcome') }} <span class="coffel">{{ $username ?? '' }}</span>, {{ __('quickad.goto_ur_email') }} <span class="coffel">{{ $useremail ?? '' }}</span> {{ __('quickad.to') }} {{ __('quickad.verify_email_address') }}</span>
                        </span>
                    </td>
                    <td class="_51m- phm _51mw">
                        <table class="uiGrid _51mz _5ud-" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr class="_51mx">
                                <td class="_51m- phm"><a class="uiButton uiButtonLarge" style="box-sizing:content-box;" rel="nofollow" target="_blank" role="button" href="http://www.{{ $emaildomain ?? '' }}/"><span class="uiButtonText">{{ __('quickad.goto_ur_email') }}</span></a>
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

