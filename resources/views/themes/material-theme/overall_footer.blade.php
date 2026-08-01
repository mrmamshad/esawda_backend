<footer class="footer-v2">
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    <div class="footer-widget widget-about" style="height: 165px;">
                        <div class="widget-top">
                            <h3 class="widget-title">{{ $site_title ?? '' }}</h3>
                        </div>
                        <div class="widget-body">
                            <p>{{ $footer_text ?? '' }}</p>

                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="footer-widget widget-contact" style="height: 165px;">
                        <div class="widget-top">
                            <h3 class="widget-title">{{ __('quickad.company') }}</h3>
                        </div>
                        <div class="widget-body">
                            <ul class="list-unstyled">
                                @if(($address ?? "")!="") <li><i class="fa fa-location-arrow"></i> {{ $address ?? '' }}</li>@endif
                                @if(($phone ?? "")!="") <li><i class="fa fa-phone"></i> {{ $phone ?? '' }}</li>@endif
                                @if(($email ?? "")!="") <li><i class="fa fa-envelope-o"></i> {{ $email ?? '' }}</li>@endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="footer-widget widget-contact" style="height: 165px;">
                        <div class="widget-top">
                            <h3 class="widget-title">{{ __('quickad.help') }} &amp; {{ __('quickad.support') }}</h3>
                        </div>
                        <div class="widget-body">
                            <ul class="list-unstyled">
                                <li><i class="fa fa-angle-double-right"></i> <a href="{{ $link['FAQ'] ?? '#' }}">FAQs</a></li>
                                <li><i class="fa fa-angle-double-right"></i> <a href="{{ $link['REPORT'] ?? '#' }}">{{ __('quickad.report') }}</a></li>
                                <li><i class="fa fa-angle-double-right"></i> <a href="{{ $link['FEEDBACK'] ?? '#' }}">{{ __('quickad.feedback') }}</a></li>
                                <li><i class="fa fa-angle-double-right"></i> <a href="{{ $link['CONTACT'] ?? '#' }}">{{ __('quickad.contact') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="footer-widget widget-news" style="height: 240px;">
                        <div class="widget-top">
                            <h3 class="widget-title">{{ __('quickad.latest_ads') }}</h3>
                        </div>
                        <div class="widget-body">
                            @foreach($item ?? [] as $item)
                            <div class="media">
                                <div class="media-left">
                                    <a href="{{ data_get($item ?? [], 'link', '') }}">
                                        <img class="media-object" src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="Thumb" width="85">
                                    </a>
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading"><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a></h4>
                                    <a href="{{ data_get($item ?? [], 'link', '') }}" class="read">{{ __('quickad.read_more') }} <i class="fa fa-caret-right"></i></a>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="col-sm-3">
                <div class="footer-col"><p>&copy; {{ $copyright_text ?? '' }}</p></div>
            </div>
            <div class="col-sm-6">
                <div class="footer-col">
                    <div class="navi">
                        <ul>
                            @foreach($htmlpage ?? [] as $htmlpage)
                            <li><a href="{{ data_get($htmlpage ?? [], 'link', '') }}">{{ data_get($htmlpage ?? [], 'title', '') }}</a></li>
                            @endforeach
                            <li><a href="{{ $link['SITEMAP'] ?? '#' }}">{{ __('quickad.site_map') }}</a></li>
                            @if(($country_type ?? "")=="multi") <li><a href="{{ $link['COUNTRY'] ?? '#' }}">{{ __('quickad.countries') }}</a></li>@endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="footer-col foot-social">
                    <p>
                        {{ __('quickad.follow_us') }}
                        @if(($facebook_link ?? "")!="") <a href="{{ $facebook_link ?? '' }}" target="_blank"><i class="fa fa-facebook-square"></i></a>@endif
                        @if(($twitter_link ?? "")!="") <a href="{{ $twitter_link ?? '' }}" target="_blank"><i class="fa fa-twitter-square"></i></a>@endif
                        @if(($googleplus_link ?? "")!="") <a href="{{ $googleplus_link ?? '' }}" target="_blank"><i class="fa fa-google-plus-square"></i></a>@endif
                        @if(($youtube_link ?? "")!="") <a href="{{ $youtube_link ?? '' }}" target="_blank"><i class="fa fa-youtube-square"></i></a>@endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

@if(($switcher ?? "")=="1")
<!--/styleswitch-->
<div class="styleswitch">
    <div class="styleswitch-lover">
        <a href="#" class="toggler"><i class="fa fa-cog fa-spin"></i></a>
        <h4>{{ __('quickad.change_color') }}</h4>
        <ul class="preset-list clearfix" id="styleswitch">
            <li><a href="javascript: void(0)" title="switch styling" id="color1">#f44336</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color2">#E91E63</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color3">#9C27B0</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color4">#673AB7</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color5">#3F51B5</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color6">#2196F3</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color7">#03A9F4</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color8">#00BCD4</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color9">#009688</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color10">#4CAF50</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color11">#8BC34A</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color12">#CDDC39</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color13">#4611a7</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color14">#FFC107</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color15">#FF9800</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color16">#FF5722</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color17">#795548</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color18">#9E9E9E</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color19">#607D8B</a></li>
            <li><a href="javascript: void(0)" title="switch styling" id="color20">#776B26</a></li>
        </ul>

        <br>
        <h4>{{ __('quickad.change_theme') }}</h4>
        <div class="dropdown theme-dropdown" id="theme-dropdown">
            <button class="btn dropdown-toggle btn-default-lite" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="false"><span id="selected_theme">Classic</span><span class="caret"></span>
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
<!--start footer section-->
</div> <!--end page-wrapper-->
<a href="#page-header" class="to-top scroll" data-show-after-scroll="600"><i class="arrow_up"></i></a>
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
</script>

<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/bootstrap/js/bootstrap.min.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/js/jquery.validate.min.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/js/jquery.fitvids.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/js/owl.carousel.min.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/js/jquery.nouislider.all.min.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/js/jquery.trackpad-scroll-emulator.min.js'></script>
<script src='{{ $site_url ?? '' }}includes/assets/js/jquery.lazyload.min.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/js/custom2.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/js/custom.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/materialize/js/materialize.js'></script>

<!-- Sweet-Alert  -->
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/sweetalert/sweetalert.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/sweetalert/jquery.sweet-alert.custom.js"></script>

<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/js/user-ajax.js'></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script>
    /* THIS PORTION OF CODE IS ONLY EXECUTED WHEN THE USER THE LANGUAGE & THEME(CLIENT-SIDE) */
    $(function () {
        $('#lang-dropdown').on('click', '.dropdown-menu li', function (e) {
            var lang = $(this).data('lang');
            if (lang != null) {
                var res = lang.substr(0, 2);
                $('#selected_lang').html(res);
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
    $(document).ready(function(){
        var lang = $.cookie('Quick_lang');
        if (lang!=null) {
            var res = lang.substr(0, 2);
            $('#selected_lang').html(res);
        }
        var theme = $.cookie('Quick_theme');
        if (theme!=null) {
            var thm = theme.substr(0, theme.indexOf('-'));
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
</body> </html>