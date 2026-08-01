<!DOCTYPE html>
<html lang="{{ __('quickad.code') }}" dir="{{ $language_direction ?? '' }}">
<head>
    <title>@if(($page_title ?? "")!="") {{ $page_title ?? '' }} - @endif{{ $site_title ?? '' }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="author" content="{{ $site_title ?? '' }}">
    <meta name="keywords" content="{{ $page_meta_keywords ?? '' }}">
    <meta name="description" content="{{ $page_meta_description ?? '' }}">
    <meta property="fb:app_id" content="{{ $facebook_app_id ?? '' }}"/>
    <meta property="og:site_name" content="{{ $site_title ?? '' }}"/>
    <meta property="og:locale" content="en_US"/>
    <meta property="og:url" content="{{ $page_link ?? '' }}"/>
    <meta property="og:title" content="@if(($page_title ?? "")!="") {{ $page_title ?? '' }} - @endif{{ $site_title ?? '' }}" />
    <meta property="og:description" content="{{ $page_meta_description ?? '' }}"/>
    <meta property="og:type" content="{{ $meta_content ?? '' }}"/>
    @if(($meta_content ?? "")=="article")
    <meta property="article:author" content="#"/>
    <meta property="article:publisher" content="#"/>
    <meta property="og:image" content="{{ $meta_image ?? '' }}"/>
    @endif
    @if(($meta_content ?? "")=="website")
    <meta property="og:image" content="{{ $meta_image ?? '' }}"/>
    @endif

    <meta property="twitter:card" content="summary">
    <meta property="twitter:title" content="{{ $page_title ?? '' }} - {{ $site_title ?? '' }}">
    <meta property="twitter:description" content="{{ $page_meta_description ?? '' }}">
    <meta property="twitter:domain" content="{{ $site_url ?? '' }}">
    <meta name="twitter:image:src" content="{{ $meta_image ?? '' }}"/>

    <link rel="shortcut icon" href="{{ $site_url ?? '' }}storage/logo/{{ $site_favicon ?? '' }}">

    <script async>
        var themecolor = '{{ $theme_color ?? '' }}';
        var mapcolor = '{{ $map_color ?? '' }}';
        var siteurl = '{{ $site_url ?? '' }}';
        var template_name = '{{ $tpl_name ?? '' }}';
        var country_code = "{{ $user_country ?? '' }}";
    </script>
    <style>
        :root{@foreach($colors ?? [] as $colors)--theme-color-{{ data_get($colors ?? [], 'id', '') }}: {{ data_get($colors ?? [], 'value', '') }};@endforeach}
    </style>
    <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/css/icons.css">
    <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/flags/flags.min.css">
    <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/styleswitcher/styleswitcher.css">
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/style.css?ver={{ $version ?? '' }}">
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/slick.css">
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/color.css">
    <script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/jquery-3.4.1.min.js"></script>
    <script src='{{ $site_url ?? '' }}includes/assets/plugins/styleswitcher/jquery.style-switcher.js'></script>
    @if(($language_direction ?? "")=="rtl")
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/rtl.css">
    @endif
    <script async>var ajaxurl = "{{ $app_url ?? '' }}user-ajax.php";</script>
    <script async type="text/javascript">
        $(document).ready(function() {
            $('.resend').click(function(e) { 						// Button which will activate our modal
                var the_id = $(this).attr('id');						//get the id
                // show the spinner
                $(this).html("<i class='fa fa-spinner fa-pulse'></i>");
                $.ajax({											//the main ajax request
                    type: "POST",
                    data: "action=email_verify&id="+$(this).attr("id"),
                    url: ajaxurl,
                    success: function(data)
                    {
                        var tpl = '<a class="button ripple-effect gray" href="javascript:void(0);">'+data+'</a>';
                        $("span#resend_count"+the_id).html(tpl);
                        //fadein the vote count
                        $("span#resend_count"+the_id).fadeIn();
                        //remove the spinner
                        $("a.resend_buttons"+the_id).remove();

                    }
                });
                return false;
            });
        });
    </script>
    <!-- ===External Code=== -->
    {{ $external_code ?? '' }}
    <!-- ===/External Code=== -->
</head>
<body data-role="page" class="{{ $language_direction ?? '' }}" id="page" data-ipapi="{{ $live_location_api ?? '' }}" data-showlocationicon="{{ $location_track_icon ?? '' }}">
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->
<!--Country-Cities-changes-Model-->
<a class="popup-with-zoom-anim hidden" href="#citiesModal" id="change-city">city</a>
<div class="zoom-anim-dialog mfp-hide popup-dialog big-dialog" id="citiesModal">
    <div class="popup-tab-content padding-0">
        <div class="quick-states" id="country-popup" data-country-id="{{ $default_country_id ?? '' }}" style="display: block;">
            <div id="regionSearchBox" class="title clr">
                <div class="clr">
                    <div class="locationrequest smallBox br5 col-sm-4">
                        <div class="rel input-container">
                            <div class="input-with-icon">
                                <input id="inputStateCity" class="with-border" type="text" placeholder="{{ __('quickad.type_your_city') }}">
                                <i class="la la-map-marker"></i>
                            </div>
                            <div id="searchDisplay"></div>
                            <div class="suggest bottom abs small br3 error hidden"><span
                                        class="target abs icon"></span>

                                <p></p>
                            </div>
                        </div>
                        <div id="lastUsedCities" class="last-used binded" style="display: none;">{{ __('quickad.last_visited') }}
                            <ul id="last-locations-ul">
                            </ul>
                        </div>
                    </div>
                    @if(($country_type ?? "")=="multi")
                    <span style="line-height: 30px;">
                        <span class="flag flag-{{ $user_country ?? '' }}"></span> <a href="#countryModal" class="popup-with-zoom-anim">{{ __('quickad.change_country') }}</a>
                    </span>
                    @endif
                </div>
            </div>
            <div class="popular-cities clr">
                <p>{{ __('quickad.popular_cities') }}</p>

                <div class="list row">

                    <ul class="col-lg-12 col-md-12 popularcity">
                        @foreach($popularcity ?? [] as $popularcity)
                        {{ data_get($popularcity ?? [], 'tpl', '') }}
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="viewport">
                <div class="full" id="getCities">
                    <div class="col-sm-12 col-md-12 loader" style="display: none"></div>
                    <div id="results" class="animate-bottom">
                        <ul class="column cities">
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
<!--Country-Cities-changes-Model-->
{{ $ad_header_top ?? '' }}
<!-- Wrapper -->
<div id="wrapper">

    <header id="header-container" class="transparent">
        <!-- Header -->
        <div id="header">
            <div class="container">
                <div class="left-side">
                    <div id="logo">
                        <a href="{{ $link['INDEX'] ?? '#' }}"><img src="{{ $site_url ?? '' }}storage/logo/{{ $site_logo ?? '' }}" alt="{{ $site_title ?? '' }}"></a>
                    </div>
                    <nav class="navigation">
                        <ul>
                            @if(($country_type ?? "")=="multi")
                            <li>
                                <a href="#countryModal" class="country-flag popup-with-zoom-anim"
                                   title="{{ __('quickad.change_country') }}"
                                   data-tippy-placement="right">

                                    <img src="{{ $site_url ?? '' }}includes/assets/plugins/flags/images/{{ $user_country ?? '' }}.png"/>
                                </a>
                            </li>
                            @endif
                            <li class="d-none d-lg-block">
                                <a href="{{ $link['LISTING'] ?? '#' }}"><i class="icon-feather-list"></i> {{ __('quickad.find_ads') }}</a>
                            </li>
                        </ul>
                    </nav>
                    <div class="clearfix"></div>
                    <!-- Mobile Navigation -->
                    <nav class="mmenu-init">
                        <ul class="mm-listview">
                            <li><a href="{{ $link['LISTING'] ?? '#' }}">{{ __('quickad.find_ads') }}</a></li>
                            @if(($logged_in ?? ""))
                            <li><a href="{{ $link['DASHBOARD'] ?? '#' }}">{{ __('quickad.dashboard') }}</a></li>
                            <li><a href="{{ $link['MYADS'] ?? '#' }}">{{ __('quickad.my_ads') }}</a></li>
                            <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}">{{ __('quickad.my_profile') }}</a></li>
                            <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}">{{ __('quickad.membership') }}</a></li>
                            <li><a href="{{ $link['TRANSACTION'] ?? '#' }}">{{ __('quickad.transaction') }}</a></li>
                            @if(($wchat_on_off ?? "")=='on' || ($quickchat_ajax_on_off ?? "")=='on' || ($quickchat_socket_on_off ?? "")=='on')
                            <li><a href="{{ $link['MESSAGE'] ?? '#' }}">{{ __('quickad.message') }}</a></li>
                            @endif
                            <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="button ripple-effect">{{ __('quickad.post_free_ad') }}</a></li>
                            <li><a href="{{ $link['LOGOUT'] ?? '#' }}">{{ __('quickad.logout') }}</a></li>
                            {{ $else ?? '' }}
                            <li><a href="{{ $link['LOGIN'] ?? '#' }}">{{ __('quickad.login') }}</a></li>
                            <li><a href="{{ $link['SIGNUP'] ?? '#' }}">{{ __('quickad.register') }}</a></li>
                            <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="button ripple-effect">{{ __('quickad.post_free_ad') }}</a></li>
                            @endif
                        </ul>
                    </nav>
                </div>
                <div class="right-side">
                    @if(($logged_in ?? ""))
                    <div class="header-widget padding-right-0 d-none d-lg-block">
                        <div class="header-notifications user-menu">
                            <div class="header-notifications-trigger">
                                <a href="#"><i class="icon-feather-user"></i> {{ $username ?? '' }}<i
                                            class="icon-feather-chevron-down"></i></a>
                            </div>
                            <div class="header-notifications-dropdown">
                                <ul class="user-menu-small-nav">
                                    <li><a href="{{ $link['DASHBOARD'] ?? '#' }}"><i class="icon-feather-grid"></i> {{ __('quickad.dashboard') }}</a></li>
                                    <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}"><i class="icon-feather-user"></i> {{ __('quickad.my_profile') }}</a></li>
                                    <li><a href="{{ $link['MYADS'] ?? '#' }}"><i class="icon-feather-briefcase"></i> {{ __('quickad.my_ads') }}</a></li>
                                    @if(($wchat_on_off ?? "")=='on' || ($quickchat_ajax_on_off ?? "")=='on' || ($quickchat_socket_on_off ?? "")=='on')
                                    <li><a href="{{ $link['MESSAGE'] ?? '#' }}"><i class="icon-feather-message-circle"></i> {{ __('quickad.message') }}</a></li>
                                    @endif
                                    <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}"><i class="icon-feather-gift"></i> {{ __('quickad.membership') }}</a></li>
                                    <li><a href="{{ $link['TRANSACTION'] ?? '#' }}"><i class="icon-feather-file-text"></i> {{ __('quickad.transaction') }}</a></li>

                                    <li><a href="{{ $link['LOGOUT'] ?? '#' }}"><i class="icon-feather-log-out"></i> {{ __('quickad.logout') }}</a>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="header-widget d-none d-lg-block">
                        <nav class="navigation">
                            <ul>
                                @if(!($logged_in ?? ""))
                                <li>
                                    <a href="#sign-in-dialog" class="popup-with-zoom-anim"><i
                                                class="icon-feather-log-in"></i> {{ __('quickad.login') }}</a>
                                </li>
                                <li><a href="{{ $link['SIGNUP'] ?? '#' }}">{{ __('quickad.register') }}</a></li>
                                @endif
                                <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="button ripple-effect post-job">{{ __('quickad.post_free_ad') }}</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    @if(($lang_sel ?? ""))
                    <div class="header-widget">
                        <div class="btn-group bootstrap-select language-switcher">
                            <button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown"
                                    title="English">
                                <span class="filter-option pull-left" id="selected_lang">{{ __('quickad.code') }}</span>&nbsp;
                                <span class="caret"></span>
                            </button>
                            <div class="dropdown-menu open">
                                <ul class="dropdown-menu inner">
                                    @foreach($langs ?? [] as $langs)
                                        <li data-lang="{{ data_get($langs ?? [], 'name', '') }}" data-code="{{ data_get($langs ?? [], 'code', '') }}">
                                            <a role="menuitem" tabindex="-1" rel="alternate"
                                               href="{{ $link['HOME'] ?? '#' }}/{{ data_get($langs ?? [], 'code', '') }}">{{ data_get($langs ?? [], 'name', '') }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                    <span class="mmenu-trigger">
                <button class="hamburger hamburger--collapse" type="button">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </span>
                </div>

            </div>
        </div>

    </header>
    <div class="clearfix"></div>
    @if(($userstatus ?? "")=="0" && ($non_active_msg ?? "")=="1")
    <div class="user-status-message">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <i class="icon-lock text-18"></i>
                    <span>{{ __('quickad.welcome') }} <strong>{{ $username ?? '' }}</strong>, {{ __('quickad.goto_ur_email') }} <strong>{{ $useremail ?? '' }}</strong>  {{ __('quickad.verify_email_address') }}</span>
                </div>
                <div class="col-lg-4">
                    <a class="button ripple-effect" rel="nofollow" target="_blank" role="button" href="http://{{ $emaildomain ?? '' }}/">{{ __('quickad.goto_ur_email') }}</a>
                    <a class="button ripple-effect gray resend_buttons{{ $user_id ?? '' }} resend" href='javascript:void(0);' id="{{ $user_id ?? '' }}">{{ __('quickad.resend_email') }}</a>
                    <span class='resend_count' id='resend_count{{ $user_id ?? '' }}'></span>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Country Picker -->
    <div class="zoom-anim-dialog mfp-hide dialog-with-tabs popup-dialog big-dialog" id="countryModal">
        <ul class="popup-tabs-nav">
            <li><a href="#country"><i class="icon-feather-map-pin"></i> {{ __('quickad.select_your_country') }}</a></li>
        </ul>
        <div class="popup-tabs-container">
            <div class="popup-tab-content" id="country">

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-with-icon margin-bottom-30">
                            <input class="with-border" type="text" placeholder="{{ __('quickad.search') }}..." id="country-modal-search">
                            <i class="icon-feather-search"></i>
                        </div>
                    </div>
                    <ul id="countries" class="column col-md-12 col-sm-12 cities">
                        @foreach($countrylist ?? [] as $countrylist)
                            <li data-name="{{ data_get($countrylist ?? [], 'name', '') }}"><span class="flag flag-{{ data_get($countrylist ?? [], 'lowercase_code', '') }}"></span> <a
                                        href="{{ $link['HOME'] ?? '#' }}/{{ data_get($countrylist ?? [], 'lang', '') }}/{{ data_get($countrylist ?? [], 'lowercase_code', '') }}"
                                        data-id="{{ data_get($countrylist ?? [], 'id', '') }}"
                                        data-name="{{ data_get($countrylist ?? [], 'name', '') }}"> {{ data_get($countrylist ?? [], 'name', '') }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{ $ad_header_bottom ?? '' }}
