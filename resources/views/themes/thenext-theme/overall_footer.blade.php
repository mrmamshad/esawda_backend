{{ $ad_footer_top ?? '' }}
<!-- Footer -->
<div id="footer">
    <div class="footer-middle-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 col-lg-5 col-md-12">
                    <div class="footer-logo">
                        <img src="{{ $site_url ?? '' }}storage/logo/{{ $site_logo ?? '' }}" alt="Footer Logo">
                    </div>
                    <p>{{ $footer_text ?? '' }}</p>
                </div>
                <div class="col-xl-1 col-lg-1">
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4">
                    <div class="footer-links">
                        <h3>{{ __('quickad.my_account') }}</h3>
                        <ul>
                            @if(($logged_in ?? ""))
                            <li><a href="{{ $link['MYADS'] ?? '#' }}">{{ __('quickad.my_ads') }}</a></li>
                            <li><a href="{{ $link['PENDINGADS'] ?? '#' }}">{{ __('quickad.pending_ads') }}</a></li>
                            <li><a href="{{ $link['LOGOUT'] ?? '#' }}">{{ __('quickad.logout') }}</a></li>
                            {{ $else ?? '' }}
                            <li><a href="{{ $link['LOGIN'] ?? '#' }}">{{ __('quickad.login') }}</a></li>
                            <li><a href="{{ $link['SIGNUP'] ?? '#' }}">{{ __('quickad.register') }}</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4">
                    <div class="footer-links">
                        <h3>{{ __('quickad.help_support') }}</h3>
                        <ul>
                            @if(($blog_enable ?? ""))
                            <li><a href="{{ $link['BLOG'] ?? '#' }}">{{ __('quickad.blog') }}</a></li>
                            @endif
                            @if(($testimonials_enable ?? ""))
                            <li><a href="{{ $link['TESTIMONIALS'] ?? '#' }}">{{ __('quickad.testimonials') }}</a></li>
                            @endif
                            <li><a href="{{ $link['FAQ'] ?? '#' }}">{{ __('quickad.faq') }}</a></li>
                            <li><a href="{{ $link['FEEDBACK'] ?? '#' }}">{{ __('quickad.feedback') }}</a></li>
                            <li><a href="{{ $link['CONTACT'] ?? '#' }}">{{ __('quickad.contact') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-4">
                    <div class="footer-links">
                        <h3>{{ __('quickad.information') }}</h3>
                        <ul>
                            <li><a href="{{ $link['ADVERTISE_HERE'] ?? '#' }}">{{ __('quickad.advertise_here') }}</a></li>
                            @foreach($htmlpage ?? [] as $htmlpage)
                            <li><a href="{{ data_get($htmlpage ?? [], 'link', '') }}">{{ data_get($htmlpage ?? [], 'title', '') }}</a></li>
                            @endforeach
                            @if(($country_type ?? "")=="multi")
                            <li><a href="{{ $link['COUNTRIES'] ?? '#' }}">{{ __('quickad.countries') }}</a></li>
                            @endif
                            <li><a href="{{ $link['SITEMAP'] ?? '#' }}">{{ __('quickad.site_map') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="footer-rows-left">
                        <div class="footer-row">
                            <span class="footer-copyright-text">{{ $copyright_text ?? '' }}</span>
                        </div>
                    </div>
                    <div class="footer-rows-right">
                        <div class="footer-row">
                            <ul class="footer-social-links">
                                @if(($facebook_link ?? "")!="")
                                <li><a href="{{ $facebook_link ?? '' }}" target="_blank"><i class="la la-facebook"></i></a></li>
                                @endif
                                @if(($twitter_link ?? "")!="")
                                <li><a href="{{ $twitter_link ?? '' }}" target="_blank"><i class="la la-twitter"></i></a></li>
                                @endif
                                @if(($googleplus_link ?? "")!="")
                                <li><a href="{{ $googleplus_link ?? '' }}" target="_blank"><i class="la la-pinterest"></i></a></li>
                                @endif
                                @if(($youtube_link ?? "")!="")
                                <li><a href="{{ $youtube_link ?? '' }}" target="_blank"><i class="la la-youtube-play"></i></a></li>
                                @endif
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@if(($cookie_consent ?? ""))
<!-- Cookie constent -->
<div class="cookieConsentContainer">
    <div class="cookieTitle">
        <h3>{{ __('quickad.cookies') }}</h3>
    </div>
    <div class="cookieDesc">
        <p>{{ __('quickad.cookies_message') }}
            @if(($cookie_link ?? "") != '')
            <a href="{{ $cookie_link ?? '' }}">{{ __('quickad.cookie_policy') }}</a>
            @endif
        </p>
    </div>
    <div class="cookieButton">
        <a href="javascript:void(0)" class="button cookieAcceptButton">{{ __('quickad.cookies_accept') }}</a>
    </div>
</div>
@endif

@if(!($logged_in ?? ""))
<!-- Sign In Popup -->
<div id="sign-in-dialog" class="zoom-anim-dialog mfp-hide dialog-with-tabs popup-dialog">
    <ul class="popup-tabs-nav">
        <li><a href="#login">{{ __('quickad.login') }}</a></li>
    </ul>
    <div class="popup-tabs-container">
        <div class="popup-tab-content" id="login">
            <div class="welcome-text">
                <h3>{{ __('quickad.welcome_back') }}</h3>
                <span>{{ __('quickad.dont_have_account') }} <a href="{{ $link['SIGNUP'] ?? '#' }}">{{ __('quickad.signup_now') }}</a></span>
            </div>
            @if(($facebook_app_id ?? "")!='' || ($google_app_id ?? "")!='')
            <div class="social-login-buttons">
                @if(($facebook_app_id ?? "")!='')
                <button class="facebook-login ripple-effect" onclick="fblogin()"><i class="fa fa-facebook"></i> {{ __('quickad.login_via_facebook') }}</button>
                @endif

                @if(($google_app_id ?? "")!='')
                <button class="google-login ripple-effect" onclick="gmlogin()"><i class="fa fa-google"></i> {{ __('quickad.login_via_google') }}</button>
                @endif
            </div>
            <div class="social-login-separator"><span>{{ __('quickad.or') }}</span></div>
            @endif
            <form id="login-form" method="post" action="{{ $site_url ?? '' }}login?ref={{ $ref_url ?? '' }}">
                <div id="login-status" class="notification error" style="display:none"></div>
                <div class="input-with-icon-left">
                    <i class="la la-user"></i>
                    <input type="text" class="input-text with-border" name="username" id="username"
                           placeholder="{{ __('quickad.username') }} / {{ __('quickad.email') }}" required/>
                </div>

                <div class="input-with-icon-left">
                    <i class="la la-unlock"></i>
                    <input type="password" class="input-text with-border" name="password" id="password"
                           placeholder="{{ __('quickad.password') }}" required/>
                </div>
                <div class="row mt-6 mb-6">
                    <div class="col-6 align-items-center d-flex">
                        <div class="checkbox">
                            <input type="checkbox" id="remember" name="remember" value="1">
                            <label for="remember"><span class="checkbox-icon"></span> {{ __('quickad.remember_me') }}</label>
                        </div>
                    </div>
                    <div class="col-6 text-right">
                        <a href="{{ $link['LOGIN'] ?? '#' }}?fstart=1" class="forgot-password">{{ __('quickad.forgotpass') }}</a>
                    </div>
                </div>
                <button id="login-button" class="button full-width button-sliding-icon ripple-effect" type="submit" name="submit">{{ __('quickad.login') }} <i class="icon-feather-arrow-right"></i></button>
            </form>
            @if(($sms_verify_mode ?? "")=="1")
            <a href="{{ $link['LOGIN'] ?? '#' }}?loginphone=1" class="button full-width button-sliding-icon ripple-effect margin-top-10">{{ __('quickad.login_with_phone') }} <i class="icon-feather-arrow-right"></i></a>
            @endif
        </div>

    </div>
</div>
@endif

@if(($switcher ?? "")=="1")
<!--/styleswitch-->
<div class="styleswitch">
    <div class="styleswitch-lover">
        <a href="#" class="toggler"><i class="fa fa-cog fa-spin"></i></a>
        <h4>{{ __('quickad.change_theme') }}</h4>
        <div class="dropdown theme-dropdown" id="theme-dropdown">
            <button class="btn dropdown-toggle btn-default-lite" type="button" id="dropdownMenu1" data-toggle="dropdown"
                    aria-expanded="false"><span id="selected_theme">Next Theme</span><span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdownMenu1">
                <li role="presentation" data-theme="thenext-theme"><a role="menuitem" tabindex="-1" rel="alternate" href="#">Next Theme</a></li>
                <li role="presentation" data-theme="classic-theme"><a role="menuitem" tabindex="-1" rel="alternate" href="#">Classic Theme</a></li>
                <li role="presentation" data-theme="material-theme"><a role="menuitem" tabindex="-1" rel="alternate" href="#">Material Theme</a></li>
                <li role="presentation" data-theme="modern-theme"><a role="menuitem" tabindex="-1" rel="alternate" href="#">Modern Theme</a></li>
            </ul>
        </div>

    </div>
</div>
@endif
<!--/End:styleswitch-->
<script>
    var session_uname = "{{ $username ?? '' }}";
    var session_uid = "{{ $user_id ?? '' }}";
    var session_img = "{{ $userpic ?? '' }}";
    // Language Var
    var LANG_ENABLE_CHAT_YOURSELF = "{{ __('quickad.enable_chat_yourself') }}";
    var LANG_JUST_NOW = "{{ __('quickad.just_now') }}";
    var LANG_PREVIEW = "{{ __('quickad.preview') }}";
    var LANG_SEND = "{{ __('quickad.send') }}";
    var LANG_FILENAME = "{{ __('quickad.filename') }}";
    var LANG_STATUS = "{{ __('quickad.status') }}";
    var LANG_SIZE = "{{ __('quickad.size') }}";
    var LANG_DRAG_FILES_HERE = "{{ __('quickad.drag_files_here') }}";
    var LANG_STOP_UPLOAD = "{{ __('quickad.stop_upload') }}";
    var LANG_ADD_FILES = "{{ __('quickad.add_files') }}";
    var LANG_TYPE_A_MESSAGE = "{{ __('quickad.type_a_message') }}";
    var LANG_ADD_FILES_TEXT = "{{ __('quickad.add_files_text') }}";
    var LANG_LOGGED_IN_SUCCESS = "{{ __('quickad.logged_in_success') }}";
    var LANG_ERROR_TRY_AGAIN = "{{ __('quickad.error_try_again') }}";
    var LANG_ERROR = "{{ __('quickad.error') }}";
    var LANG_CANCEL = "{{ __('quickad.cancel') }}";
    var LANG_DELETED = "{{ __('quickad.deleted') }}";
    var LANG_ARE_YOU_SURE = "{{ __('quickad.are_you_sure') }}";
    var LANG_YOU_WANT_DELETE = "{{ __('quickad.you_want_delete') }}";
    var LANG_YES_DELETE = "{{ __('quickad.yes_delete') }}";
    var LANG_AD_DELETED = "{{ __('quickad.ad_deleted') }}";
    var LANG_SHOW = "{{ __('quickad.show') }}";
    var LANG_HIDE = "{{ __('quickad.hide') }}";
    var LANG_HIDDEN = "{{ __('quickad.hidden') }}";
    var LANG_ADD_FAV = "{{ __('quickad.add_favourite') }}";
    var LANG_REMOVE_FAV = "{{ __('quickad.remove_favourite') }}";
    var LANG_SELECT_CITY = "{{ __('quickad.select_city') }}";
    //Chat
    var LANG_CHATS = "{{ __('quickad.chats') }}";
    var LANG_NO_MSG_FOUND = "{{ __('quickad.no_msg_found') }}";
    var LANG_ONLINE = "{{ __('quickad.online') }}";
    var LANG_OFFLINE = "{{ __('quickad.offline') }}";
    var LANG_TYPING = "{{ __('quickad.typing') }}";
    var LANG_GOT_MESSAGE = "{{ __('quickad.got_message') }}";
    var openstreet_access_token = '{{ $openstreet_access_token ?? '' }}';
</script>

<!-- Scripts -->
<script src='{{ $site_url ?? '' }}includes/assets/js/jquery.lazyload.min.js'></script>
<script src='{{ $site_url ?? '' }}includes/assets/js/custom.js?ver={{ $version ?? '' }}'></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/mmenu.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/tippy.all.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/bootstrap-select.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/snackbar.js?ver=3"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/magnific-popup.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/jquery.cookie.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/jquery.nicescroll.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/slick.min.js?ver={{ $version ?? '' }}"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/custom.js?ver={{ $version ?? '' }}"></script>
<script src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/user-ajax.js?ver={{ $version ?? '' }}'></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script>
    /* THIS PORTION OF CODE IS ONLY EXECUTED WHEN THE USER THE LANGUAGE & THEME(CLIENT-SIDE) */
    $(function () {
        $('#lang-dropdown').on('click', '.dropdown-menu li', function (e) {
            var lang = $(this).data('lang');
            var code = $(this).data('code');
            if (lang != null) {
                var res = lang.substr(0, 2);
                $('#selected_lang').html(code);
                $.cookie('Quick_lang', lang,{ path: '/' });
                location.reload();
            }
        });

        $('#theme-dropdown').on('click', '.dropdown-menu li', function (e) {
            var theme = $(this).data('theme');
            var theme_url = "{{ $site_url ?? '' }}theme/"+theme;
            window.location.href = theme_url;
        });
    });
    $(document).ready(function () {
        var theme = $.cookie('Quick_theme');
        if (theme != null) {
            var thm = theme.substr(0, theme.indexOf('-'));
            var thm = thm+" Theme";
            $('#selected_theme').html(thm);
        }
    });
</script>

@if(($logged_in ?? "") && ($quickchat_socket_on_off ?? "")=='on')
<script>
    var ws_protocol = window.location.href.indexOf("https://")==0?"wss":"ws";
    var ws_host = '{{ $socket_host ?? '' }}';
    var ws_port = '{{ $socket_port ?? '' }}';
    var WEBSOCKET_URL = ws_protocol+'://'+ws_host+':'+ws_port+'/quickchat';
    var filename = "{{ $quickchat_ajax_secret_file ?? '' }}.php";
    var plugin_directory = "plugins/quickchat-socket/"+filename;
</script>
<link type="text/css" rel="stylesheet" media="all" href="{{ $site_url ?? '' }}plugins/quickchat-socket/assets/chatcss/chatbox.css"/>
<div id="quickchat-rtl"></div>
<script>
    if ($("body").hasClass("rtl")) {
        $('#quickchat-rtl').append('<link rel="stylesheet" type="text/css" href="{{ $site_url ?? '' }}plugins/quickchat-socket/assets/chatcss/chatbox-rtl.css">');
        var rtl = true;
    }else{
        var rtl = false;
    }
</script>
<!--Websocket Version Js-->
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-socket/assets/chatjs/quickchat-websocket.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-socket/plugins/smiley/js/emojione.min.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-socket/plugins/smiley/smiley.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-socket/assets/chatjs/lightbox.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-socket/assets/chatjs/chatbox.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-socket/assets/chatjs/chatbox_custom.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-socket/plugins/uploader/plupload.full.min.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-socket/plugins/uploader/jquery.ui.plupload/jquery.ui.plupload.js"></script>
<table id="lightbox" style="display: none;height: 100%">
    <tr><td height="10px"><p><img src="{{ $site_url ?? '' }}plugins/quickchat-socket/plugins/images/close-icon-white.png" width="30px" style="cursor: pointer"/></p></td></tr>
    <tr><td valign="middle"><div id="content"><img src="#"/></div></td></tr>
</table>
ELSEIF({{ $logged_in ?? '' }} && '{{ $quickchat_ajax_on_off ?? '' }}'=='on'){
<script>
    var filename = "{{ $quickchat_ajax_secret_file ?? '' }}.php";
    var plugin_directory = "plugins/quickchat-ajax/"+filename;
</script>
<link type="text/css" rel="stylesheet" media="all" href="{{ $site_url ?? '' }}plugins/quickchat-ajax/assets/chatcss/chatbox.css"/>
<div id="quickchat-rtl"></div>
<script>
    if ($("body").hasClass("rtl")) {
        $('#quickchat-rtl').append('<link rel="stylesheet" type="text/css" href="{{ $site_url ?? '' }}plugins/quickchat-ajax/assets/chatcss/chatbox-rtl.css">');
        var rtl = true;
    }else{
        var rtl = false;
    }
</script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-ajax/plugins/smiley/js/emojione.min.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-ajax/plugins/smiley/smiley.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-ajax/assets/chatjs/lightbox.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-ajax/assets/chatjs/chatbox.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-ajax/assets/chatjs/chatbox_custom.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-ajax/plugins/uploader/plupload.full.min.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/quickchat-ajax/plugins/uploader/jquery.ui.plupload/jquery.ui.plupload.js"></script>
<table id="lightbox" style="display: none;height: 100%">
    <tr><td height="10px"><p><img src="{{ $site_url ?? '' }}plugins/quickchat-ajax/plugins/images/close-icon-white.png" width="30px" style="cursor: pointer"/></p></td></tr>
    <tr><td valign="middle"><div id="content"><img src="#"/></div></td></tr>
</table>

ELSEIF({{ $logged_in ?? '' }} && '{{ $zechat_on_off ?? '' }}'=='on'){
<script>
    var filename = "{{ $zechat_secret_file ?? '' }}.php";
    var plugin_directory = "plugins/zechat/"+filename;
</script>
<link type="text/css" rel="stylesheet" media="all" href="{{ $site_url ?? '' }}plugins/zechat/app/includes/chatcss/chat.css"/>
<div id="zechat-rtl"></div>
<script>
    if ($("body").hasClass("rtl")) {
        $('#zechat-rtl').append('<link rel="stylesheet" type="text/css" href="{{ $site_url ?? '' }}plugins/zechat/app/includes/chatcss/chat-rtl.css">');
        var rtl = true;
    }else{
        var rtl = false;
    }
</script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/zechat/app/plugins/smiley/js/emojione.min.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/zechat/app/plugins/smiley/smiley.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/zechat/app/includes/chatjs/lightbox.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/zechat/app/includes/chatjs/chat.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}plugins/zechat/app/includes/chatjs/custom.js"></script>
<script type="text/javascript"
        src="{{ $site_url ?? '' }}plugins/zechat/app/plugins/uploader/plupload.full.min.js"></script>
<script type="text/javascript"
        src="{{ $site_url ?? '' }}plugins/zechat/app/plugins/uploader/jquery.ui.plupload/jquery.ui.plupload.js"></script>

<table id="lightbox" style="display: none;height: 100%">
    <tr><td height="10px"><p><img src="{{ $site_url ?? '' }}plugins/zechat/app/plugins/images/close-icon-white.png" width="30px" style="cursor: pointer"/></p></td></tr>
    <tr><td valign="middle"><div id="content"><img src="#"/></div></td></tr>
</table>
@endif

<!-- footer -->
<link href="{{ $site_url ?? '' }}plugins/banner-admanager/css/ubm.css?ver=2.50" rel="stylesheet">
<script src="{{ $site_url ?? '' }}plugins/banner-admanager/js/ubm-jsonp.js?ver=2.50"></script>
</body>
</html>
