{{-- Ported from templates/thenext-theme/overall_header.tpl --}}
@php
    $base = rtrim($site_url ?? url('/'), '/') . '/';
    $tpl  = $tpl_name ?? config('quickad.active_theme', 'thenext-theme');
    $ver  = $version ?? config('quickad.version', '10.4');
    $dir  = $language_direction ?? (__('quickad.dir') === 'rtl' ? 'rtl' : 'ltr');
    $link = $link ?? [];
    $loggedIn = !empty(auth()->id());
    $user = auth()->user();
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>@if(($page_title ?? '') !== ''){{ $page_title }} - @endif{{ $site_title ?? 'Quickad' }}</title>
    <meta name="author" content="{{ $site_title ?? '' }}">
    <meta name="keywords" content="{{ $page_meta_keywords ?? '' }}">
    <meta name="description" content="{{ $page_meta_description ?? '' }}">

    <link rel="shortcut icon" href="{{ $base }}storage/logo/{{ $site_favicon ?? 'favicon.png' }}">

    <script>
        var themecolor      = '{{ $theme_color ?? '' }}';
        var mapcolor        = '{{ $map_color ?? '' }}';
        var siteurl         = '{{ $base }}';
        var template_name   = '{{ $tpl }}';
        var country_code    = "{{ $user_country ?? '' }}";
    </script>

    <style>
        :root{@foreach(($colors ?? []) as $c)--theme-color-{{ data_get($c, 'id', '') }}: {{ data_get($c, 'value', '') }};@endforeach}
    </style>

    {{-- Core stylesheets --}}
    <link rel="stylesheet" href="{{ $base }}includes/assets/css/icons.css">
    <link rel="stylesheet" href="{{ $base }}includes/assets/plugins/flags/flags.min.css">
    <link rel="stylesheet" href="{{ $base }}includes/assets/plugins/styleswitcher/styleswitcher.css">
    <link rel="stylesheet" href="{{ $base }}templates/{{ $tpl }}/css/style.css?ver={{ $ver }}">
    <link rel="stylesheet" href="{{ $base }}templates/{{ $tpl }}/css/slick.css">
    <link rel="stylesheet" href="{{ $base }}templates/{{ $tpl }}/css/color.css">
    @if($dir === 'rtl')
        <link rel="stylesheet" href="{{ $base }}templates/{{ $tpl }}/css/rtl.css">
    @endif

    {{-- jQuery FIRST (fixes "jQuery is not defined") --}}
    <script src="{{ $base }}templates/{{ $tpl }}/js/jquery-3.4.1.min.js"></script>
    <script src="{{ $base }}includes/assets/plugins/styleswitcher/jquery.style-switcher.js"></script>

    <script>var ajaxurl = "{{ $app_url ?? ($base . 'user-ajax.php') }}";</script>

    <script>
        $(document).ready(function () {
            $('.resend').on('click', function () {
                var the_id = $(this).attr('id');
                $(this).html("<i class='fa fa-spinner fa-pulse'></i>");
                $.ajax({
                    type: "POST",
                    data: "action=email_verify&id=" + the_id,
                    url: ajaxurl,
                    success: function (data) {
                        $("span#resend_count" + the_id).html(
                            '<a class="button ripple-effect gray" href="javascript:void(0);">' + data + '</a>'
                        ).fadeIn();
                        $("a.resend_buttons" + the_id).remove();
                    }
                });
                return false;
            });
        });
    </script>

    {!! $external_code ?? '' !!}
</head>
<body data-role="page" class="{{ $dir }}" id="page"
      data-ipapi="{{ $live_location_api ?? '' }}"
      data-showlocationicon="{{ $location_track_icon ?? '' }}">

<!-- Wrapper -->
<div id="wrapper">

    <header id="header-container" class="transparent">
        <!-- Header -->
        <div id="header">
            <div class="container">
                <div class="left-side">
                    <div id="logo">
                        <a href="{{ $link['INDEX'] ?? $base }}">
                            <img src="{{ $base }}storage/logo/{{ $site_logo ?? 'material-theme_logo.png' }}"
                                 alt="{{ $site_title ?? 'Quickad' }}">
                        </a>
                    </div>
                    <nav class="navigation">
                        <ul>
                            <li class="d-none d-lg-block">
                                <a href="{{ $link['LISTING'] ?? '#' }}"><i class="icon-feather-list"></i>
                                    {{ __('quickad.find_ads') }}</a>
                            </li>
                        </ul>
                    </nav>
                    <div class="clearfix"></div>
                    <!-- Mobile Navigation -->
                    <nav class="mmenu-init">
                        <ul class="mm-listview">
                            <li><a href="{{ $link['LISTING'] ?? '#' }}">{{ __('quickad.find_ads') }}</a></li>
                            @if($loggedIn)
                                <li><a href="{{ $link['DASHBOARD'] ?? '#' }}">{{ __('quickad.dashboard') }}</a></li>
                                <li><a href="{{ $link['MYADS'] ?? '#' }}">{{ __('quickad.my_ads') }}</a></li>
                                <li><a href="{{ ($link['PROFILE'] ?? '#') . '/' . ($user->username ?? '') }}">{{ __('quickad.my_profile') }}</a></li>
                                <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}">{{ __('quickad.membership') }}</a></li>
                                <li><a href="{{ $link['TRANSACTION'] ?? '#' }}">{{ __('quickad.transaction') }}</a></li>
                                <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="button ripple-effect">{{ __('quickad.post_free_ad') }}</a></li>
                                <li><a href="{{ $link['LOGOUT'] ?? '#' }}">{{ __('quickad.logout') }}</a></li>
                            @else
                                <li><a href="{{ $link['LOGIN'] ?? '#' }}">{{ __('quickad.login') }}</a></li>
                                <li><a href="{{ $link['SIGNUP'] ?? '#' }}">{{ __('quickad.register') }}</a></li>
                                <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="button ripple-effect">{{ __('quickad.post_free_ad') }}</a></li>
                            @endif
                        </ul>
                    </nav>
                </div>
                <div class="right-side">
                    @if($loggedIn)
                        <div class="header-widget padding-right-0 d-none d-lg-block">
                            <div class="header-notifications user-menu">
                                <div class="header-notifications-trigger">
                                    <a href="#"><i class="icon-feather-user"></i> {{ $user->username ?? '' }}<i
                                            class="icon-feather-chevron-down"></i></a>
                                </div>
                                <div class="header-notifications-dropdown">
                                    <ul class="user-menu-small-nav">
                                        <li><a href="{{ $link['DASHBOARD'] ?? '#' }}"><i class="icon-feather-grid"></i> {{ __('quickad.dashboard') }}</a></li>
                                        <li><a href="{{ ($link['PROFILE'] ?? '#') . '/' . ($user->username ?? '') }}"><i class="icon-feather-user"></i> {{ __('quickad.my_profile') }}</a></li>
                                        <li><a href="{{ $link['MYADS'] ?? '#' }}"><i class="icon-feather-briefcase"></i> {{ __('quickad.my_ads') }}</a></li>
                                        <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}"><i class="icon-feather-gift"></i> {{ __('quickad.membership') }}</a></li>
                                        <li><a href="{{ $link['TRANSACTION'] ?? '#' }}"><i class="icon-feather-file-text"></i> {{ __('quickad.transaction') }}</a></li>
                                        <li><a href="{{ $link['LOGOUT'] ?? '#' }}"><i class="icon-feather-log-out"></i> {{ __('quickad.logout') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="header-widget d-none d-lg-block">
                        <nav class="navigation">
                            <ul>
                                @unless($loggedIn)
                                    <li>
                                        <a href="#sign-in-dialog" class="popup-with-zoom-anim">
                                            <i class="icon-feather-log-in"></i> {{ __('quickad.login') }}
                                        </a>
                                    </li>
                                    <li><a href="{{ $link['SIGNUP'] ?? '#' }}">{{ __('quickad.register') }}</a></li>
                                @endunless
                                <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="button ripple-effect post-job">{{ __('quickad.post_free_ad') }}</a></li>
                            </ul>
                        </nav>
                    </div>
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
