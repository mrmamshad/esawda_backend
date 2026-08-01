{{-- Ported from templates/thenext-theme/overall_footer.tpl --}}
@php
    $base = rtrim($site_url ?? url('/'), '/') . '/';
    $tpl  = $tpl_name ?? config('quickad.active_theme', 'thenext-theme');
    $ver  = $version ?? config('quickad.version', '10.4');
    $link = $link ?? [];
    $loggedIn = !empty(auth()->id());
@endphp

<!-- Footer -->
<div id="footer">
    <div class="footer-middle-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-5 col-lg-5 col-md-12">
                    <div class="footer-logo">
                        <img src="{{ $base }}storage/logo/{{ $site_logo ?? 'material-theme_logo.png' }}" alt="Footer Logo">
                    </div>
                    <p>{!! $footer_text ?? '' !!}</p>
                </div>
                <div class="col-xl-1 col-lg-1"></div>

                <div class="col-xl-2 col-lg-2 col-md-4">
                    <div class="footer-links">
                        <h3>{{ __('quickad.my_account') }}</h3>
                        <ul>
                            @if($loggedIn)
                                <li><a href="{{ $link['MYADS'] ?? '#' }}">{{ __('quickad.my_ads') }}</a></li>
                                <li><a href="{{ $link['PENDINGADS'] ?? '#' }}">{{ __('quickad.pending_ads') }}</a></li>
                                <li><a href="{{ $link['LOGOUT'] ?? '#' }}">{{ __('quickad.logout') }}</a></li>
                            @else
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
                            @if(($blog_enable ?? 1))
                                <li><a href="{{ $link['BLOG'] ?? '#' }}">{{ __('quickad.blog') }}</a></li>
                            @endif
                            @if(($testimonials_enable ?? 1))
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
                            @foreach(($htmlpage ?? []) as $htmlpage)
                                <li><a href="{{ data_get($htmlpage, 'link', '#') }}">{{ data_get($htmlpage, 'title', '') }}</a></li>
                            @endforeach
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
                            <span class="footer-copyright-text">{!! $copyright_text ?? '&copy; ' . date('Y') . ' Quickad. All Rights Reserved.' !!}</span>
                        </div>
                    </div>
                    <div class="footer-rows-right">
                        <div class="footer-row">
                            <ul class="footer-social-links">
                                @if(!empty($facebook_link ?? null))
                                    <li><a href="{{ $facebook_link }}" target="_blank"><i class="la la-facebook"></i></a></li>
                                @endif
                                @if(!empty($twitter_link ?? null))
                                    <li><a href="{{ $twitter_link }}" target="_blank"><i class="la la-twitter"></i></a></li>
                                @endif
                                @if(!empty($youtube_link ?? null))
                                    <li><a href="{{ $youtube_link }}" target="_blank"><i class="la la-youtube-play"></i></a></li>
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
</div>{{-- close #wrapper --}}

<!-- Scripts -->
<script src="{{ $base }}includes/assets/js/jquery.lazyload.min.js"></script>
<script src="{{ $base }}includes/assets/js/custom.js?ver={{ $ver }}"></script>
<script src="{{ $base }}templates/{{ $tpl }}/js/mmenu.min.js"></script>
<script src="{{ $base }}templates/{{ $tpl }}/js/tippy.all.min.js"></script>
<script src="{{ $base }}templates/{{ $tpl }}/js/bootstrap-select.min.js"></script>
<script src="{{ $base }}templates/{{ $tpl }}/js/snackbar.js?ver=3"></script>
<script src="{{ $base }}templates/{{ $tpl }}/js/magnific-popup.min.js"></script>
<script src="{{ $base }}templates/{{ $tpl }}/js/jquery.cookie.min.js"></script>
<script src="{{ $base }}templates/{{ $tpl }}/js/jquery.nicescroll.min.js"></script>
<script src="{{ $base }}templates/{{ $tpl }}/js/slick.min.js?ver={{ $ver }}"></script>
<script src="{{ $base }}templates/{{ $tpl }}/js/custom.js?ver={{ $ver }}"></script>
<script src="{{ $base }}templates/{{ $tpl }}/js/user-ajax.js?ver={{ $ver }}"></script>

<script>
    $(function () {
        $('#lang-dropdown').on('click', '.dropdown-menu li', function () {
            var lang = $(this).data('lang');
            var code = $(this).data('code');
            if (lang != null) {
                $('#selected_lang').html(code);
                $.cookie('Quick_lang', lang, {path: '/'});
                location.reload();
            }
        });

        $('#theme-dropdown').on('click', '.dropdown-menu li', function () {
            var theme = $(this).data('theme');
            window.location.href = "{{ $base }}theme/" + theme;
        });
    });

    $(document).ready(function () {
        var theme = $.cookie ? $.cookie('Quick_theme') : null;
        if (theme != null) {
            var thm = theme.substr(0, theme.indexOf('-')) + " Theme";
            $('#selected_theme').html(thm);
        }
        // Trigger lazyload for images with class .lazy-load
        if (typeof $.fn.lazyload === 'function') {
            $("img.lazy-load").lazyload();
        }
    });
</script>
</body>
</html>
