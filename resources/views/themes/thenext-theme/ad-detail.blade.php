@include('partials.header')
<div id="titlebar" class="margin-bottom-0">
    <div class="container">
        <div class="row">
            <div class="col-md-7 col-sm-12">
                <h2>{{ $item_title ?? '' }}
                    @if(($item_featured ?? "")=="1") <div class="badge blue"> {{ __('quickad.featured') }}</div> @endif
                    @if(($item_urgent ?? "")=="1") <div class="badge yellow"> {{ __('quickad.urgent') }}</div> @endif
                    @if(($item_highlight ?? "")=="1") <div class="badge red"> {{ __('quickad.highlight') }}</div> @endif
                </h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li><a href="{{ $item_catlink ?? '' }}">{{ $item_category ?? '' }}</a></li>
                        <li><a href="{{ $item_subcatlink ?? '' }}">{{ $item_sub_category ?? '' }}</a></li>
                    </ul>
                </nav>
            </div>

            @if(($logged_in ?? ""))
                @if(($zechat ?? "")=='on' || ($quickchat ?? "")=='on' || ($quickchat_socket_on_off ?? "")=='on')
                <div class="col-md-5 col-sm-12">
                    <div class="right-side">

                        <button type="button" class="button ripple-effect popup-with-zoom-anim start_zechat hide-under-768px"
                                data-chatid="{{ $item_authorid ?? '' }}_{{ $item_id ?? '' }}"
                                data-postid="{{ $item_id ?? '' }}"
                                data-userid="{{ $item_authorid ?? '' }}"
                                data-fullname="{{ $item_authorname ?? '' }}"
                                data-username="{{ $item_authoruname ?? '' }}"
                                data-userimage="{{ $item_authorimg ?? '' }}"
                                data-userstatus="{{ $item_authoronline ?? '' }}">{{ __('quickad.chat_now') }} <i class="icon-feather-message-circle"></i></button>

                        <a href="{{ $quickchat_url ?? '' }}" class="button ripple-effect show-under-768px">{{ __('quickad.chat_now') }} <i class="icon-feather-message-circle"></i></a>

                    </div>
                </div>
                @endif
            {{ $else ?? '' }}
                <div class="col-md-5 col-sm-12">
                    <div class="right-side">
                        <a href="#sign-in-dialog" class="button ripple-effect popup-with-zoom-anim">{{ __('quickad.login_chat') }} <i class="icon-feather-message-circle"></i></a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@if(($show_image_slider ?? "")=="1")
<!-- Slider -->
<div class="fullwidth-property-slider margin-bottom-0">
    @foreach($item_screenshot ?? [] as $item_screenshot)
        <a href="{{ $site_url ?? '' }}/storage/products/{{ data_get($item_screenshot ?? [], 'image', '') }}" data-background-image="{{ $site_url ?? '' }}/storage/products/{{ data_get($item_screenshot ?? [], 'image', '') }}" class="item mfp-gallery"></a>
    @endforeach
</div>
@endif
<div class="container margin-top-50">
    <div class="row">

        <!-- Content -->
        <div class="col-xl-8 col-lg-8 content-right-offset">

            <div class="single-page-section">
                <h3>{{ __('quickad.ads_details') }}</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="job-property">
                            <i class="la la-map-marker"></i>
                            <span>{{ __('quickad.location') }}</span>
                            <h5>{{ $item_city ?? '' }}, {{ $item_state ?? '' }}</h5>
                        </div>
                    </div>
                    @if(($item_price ?? "") != '0')
                    <div class="col-md-6">
                        <div class="job-property">
                            <i class="la la-credit-card"></i>
                            <span>{{ __('quickad.price') }}</span>
                            <h5>{{ $item_price ?? '' }} @if(($item_negotiate ?? "") != '') <span class="badge green d-inline-block">{{ $item_negotiate ?? '' }}</span> @endif</h5>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <div class="job-property">
                            <i class="la la-clock-o"></i>
                            <span>{{ __('quickad.posted_on') }}</span>
                            <h5>{{ $item_created ?? '' }}</h5>
                        </div>
                    </div>

                    @if(($item_hide_phone ?? "")=="no" && ($item_phone ?? ""))
                    <div class="col-md-6">
                        <div class="job-property">
                            <i class="la la-phone"></i>
                            <span>{{ __('quickad.phone_no') }}</span>
                            <h5>{{ $item_phone ?? '' }}</h5>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="single-page-section">
                <h3>{{ __('quickad.additional_details') }}</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="job-property">
                            <i class="icon-feather-hash"></i>
                            <span>{{ __('quickad.ad_id') }}</span>
                            <h5>{{ $item_id ?? '' }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="job-property">
                            <i class="icon-feather-eye"></i>
                            <span>{{ __('quickad.ad_views') }}</span>
                            <h5>{{ $item_view ?? '' }}</h5>
                        </div>
                    </div>
                    @if(($item_customfield ?? "")!="0")

                    @foreach($item_custom ?? [] as $item_custom)
                        <div class="col-md-6">
                            <div class="job-property">
                                @if((data_get($item_custom ?? [], 'icon', ''))!="")
                                <img src="{{ data_get($item_custom ?? [], 'icon', '') }}" width="24"/>
                                {{ $else ?? '' }}
                                <i class="icon-feather-chevron-right"></i>
                                @endif
                                <span>{{ data_get($item_custom ?? [], 'title', '') }}</span>
                                <h5>{{ data_get($item_custom ?? [], 'value', '') }}</h5>
                            </div>
                        </div>
                    @endforeach
                    @endif

                    @foreach($item_custom_checkbox ?? [] as $item_custom_checkbox)
                        <div class="col-md-12">
                            <div class="job-property">
                                @if((data_get($item_custom_checkbox ?? [], 'icon', ''))!="")
                                <img src="{{ data_get($item_custom_checkbox ?? [], 'icon', '') }}" width="24"/>
                                {{ $else ?? '' }}
                                <i class="icon-feather-chevron-right"></i>
                                @endif
                                <span>{{ data_get($item_custom_checkbox ?? [], 'title', '') }}</span>
                                <h5 class="row">
                                    <ul class="listing-features checkboxes">
                                        {{ data_get($item_custom_checkbox ?? [], 'value', '') }}
                                    </ul>
                                </h5>
                            </div>
                        </div>
                    @endforeach
                </div>
                @foreach($item_custom_textarea ?? [] as $item_custom_textarea)
                    <div class="job-property">
                        @if((data_get($item_custom_textarea ?? [], 'icon', ''))!="")
                        <img src="{{ data_get($item_custom_textarea ?? [], 'icon', '') }}" width="24"/>
                        {{ $else ?? '' }}
                        <i class="icon-feather-chevron-right"></i>
                        @endif
                        <span>{{ data_get($item_custom_textarea ?? [], 'title', '') }}</span>
                        <h5>{{ data_get($item_custom_textarea ?? [], 'value', '') }}</h5>
                    </div>
                @endforeach

            </div>

            <div class="single-page-section">
                <h3>{{ __('quickad.description') }}</h3>
                <div class="user-html @if(($item_showmore ?? "")=='1') show-more @endif">
                    {{ $item_desc ?? '' }}
                    @if(($item_showmore ?? "")=='1') <a href="#" class="show-more-button">{{ __('quickad.show_more') }} <i class="fa fa-angle-down"></i></a> @endif
                </div>
            </div>
            @if(($show_tag ?? ""))
            <div class="single-page-section">
                <h3>{{ __('quickad.tags') }}</h3>
                <ul class="job-tags">
                    {{ $item_tag ?? '' }}
                </ul>
            </div>
            @endif
            @if((($post_address_mode ?? "")=='1') && (($item_lat ?? "")!=''))
            <div class="single-page-section">
                <h3>{{ __('quickad.location') }}</h3>
                <div id="single-job-map-container">
                    <div class="map-widget map" id="singleListingMap" data-latitude="{{ $item_lat ?? '' }}" data-longitude="{{ $item_long ?? '' }}" data-map-icon="fa fa-marker"></div>
                    <span><a href="https://maps.google.com/?q={{ $item_location ?? '' }}" target="_blank" rel="nofollow">{{ $item_location ?? '' }}</a></span>
                </div>
            </div>
            @endif
            <div class="single-page-section">
                <h3>{{ __('quickad.reviews') }} ({{ $itemreview ?? '' }})</h3>

                <!-- **** Start reviews **** -->
                <div class="starReviews text-widget">
                    <!-- This is where your product ID goes -->
                    <div id="review-productId" class="review-productId" style="">{{ $item_id ?? '' }}</div>
                    <!-- Show current reviews -->
                    <div class="show-reviews"><div class="loader" style="margin: 0 auto;"></div></div>
                    <hr>

                    @if(($logged_in ?? "")=="0")
                    <div style="padding-top: 10px"><a href="#sign-in-dialog" class="ripple-effect popup-with-zoom-anim btn btn-primary">{{ __('quickad.logintoreview') }}</a></div>
                    @endif
                    @if(($logged_in ?? "")=="1")
                    <!-- Add new review -->
                    <div class="add-review"></div>
                    @endif

                    <script type="text/javascript">
                        var LANG_ADDREVIEWS     = "{{ __('quickad.addreviews') }}";
                        var LANG_SUBMITREVIEWS  = "{{ __('quickad.submitreviews') }}";
                        var LANG_HOW_WOULD_RATE = "{{ __('quickad.how_would_rate') }}";
                        var LANG_REVIEWS        = "{{ __('quickad.reviews') }}";
                        var LANG_YOURREVIEWS    = "{{ __('quickad.yourreviews') }}";
                        var LANG_ENTER_REVIEW   = "{{ __('quickad.enter_review') }}";
                        var LANG_STAR           = "{{ __('quickad.star') }}";
                    </script>
                </div>
                <!-- **** End reviews **** -->
            </div>
        </div>
        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-4">
            <div class="sidebar-container">
                <!-- Sidebar Widget -->
                <div class="sidebar-widget">
                    <div class="job-detail-box">
                        <div class="job-detail-box-headline text-center">{{ __('quickad.contact_advertiser') }}</div>
                        <div class="job-detail-box-inner">
                            <div class="job-company-logo">
                                <a href="{{ $item_authorlink ?? '' }}">
                                    <img src="{{ $site_url ?? '' }}storage/profile/{{ $item_authorimg ?? '' }}" alt="{{ $item_authoruname ?? '' }}">
                                </a>
                            </div>
                            <h2>
                                <a href="{{ $item_authorlink ?? '' }}">{{ $item_authorname ?? '' }} @if(($item_authorname ?? "")=="") {{ $item_authoruname ?? '' }} @endif</a>
                                @if(($sub_image ?? "")!="")
                                <img src="{{ $sub_image ?? '' }}" alt="{{ $sub_title ?? '' }}" title="{{ $sub_title ?? '' }}" width="24px"/>
                                @endif
                            </h2>
                            <ul>
                                <li>
                                    <i class="la la-clock-o"></i>
                                    <span>{{ $item_authorjoined ?? '' }}</span>
                                </li>

                                @if(($item_phone ?? "") != "" && ($non_login_phone_show ?? "")=="1")
                                    <li class="tg-btnphone">
                                        <i class="icon-feather-phone-call"></i>
                                        <span data-last="{{ $item_phone ?? '' }}"><a href="tel:{{ $item_phone ?? '' }}" rel="nofollow"><em>{{ __('quickad.show_phone_no') }}</em></a></span>
                                    </li>
                                ELSEIF({{ $logged_in ?? '' }} && "{{ $item_phone ?? '' }}" != ""){
                                    <li class="tg-btnphone">
                                        <i class="icon-feather-phone-call"></i>
                                        <span data-last="{{ $item_phone ?? '' }}"><a href="tel:{{ $item_phone ?? '' }}" rel="nofollow"><em>{{ __('quickad.show_phone_no') }}</em></a></span>
                                    </li>
                                @endif

                            </ul>
                            @if(($logged_in ?? ""))

                                @if(($zechat ?? "")=='on' || ($quickchat ?? "")=='on' || ($quickchat_socket_on_off ?? "")=='on')
                                <button type="button" class="button ripple-effect full-width margin-top-10 start_zechat zechat-hide-under-768px"
                                        data-chatid="{{ $item_authorid ?? '' }}_{{ $item_id ?? '' }}"
                                        data-postid="{{ $item_id ?? '' }}"
                                        data-userid="{{ $item_authorid ?? '' }}"
                                        data-fullname="{{ $item_authorname ?? '' }}"
                                        data-username="{{ $item_authoruname ?? '' }}"
                                        data-userimage="{{ $item_authorimg ?? '' }}"
                                        data-userstatus="{{ $item_authoronline ?? '' }}"
                                        data-posttitle="{{ $item_title ?? '' }}"
                                        data-postlink="{{ $item_link ?? '' }}">{{ __('quickad.chat_now') }} <i class="icon-feather-message-circle"></i></button>
                                <a href="{{ $quickchat_url ?? '' }}" class="button ripple-effect full-width margin-top-10 zechat-show-under-768px">{{ __('quickad.chat_now') }} <i class="icon-feather-message-circle"></i></a>
                                @endif

                            {{ $else ?? '' }}
                                <a href="#sign-in-dialog" class="button ripple-effect popup-with-zoom-anim full-width margin-top-10">{{ __('quickad.login_chat') }} <i class="icon-feather-message-circle"></i></a>
                            @endif
                            @if(($non_login_sendemail_allow ?? "")=="1")
                                <a href="#emailToSeller" class="button ripple-effect popup-with-zoom-anim full-width margin-top-10 apply-dialog-button">{{ __('quickad.reply_mail') }} <i class="icon-feather-mail"></i></a>
                            ELSEIF({{ $logged_in ?? '' }}){
                                <a href="#emailToSeller" class="button ripple-effect popup-with-zoom-anim full-width margin-top-10 apply-dialog-button">{{ __('quickad.reply_mail') }} <i class="icon-feather-mail"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="sidebar-widget">
                    <div class="job-detail-box">
                        <div class="job-detail-box-inner">
                            <!-- **** Start reviews **** -->
                            <div class="starReviews">
                                <!-- Show average-rating -->
                                <div class="average-rating"><div class="small_loader" style="margin: 0 auto;"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sidebar Widget -->
                <div class="sidebar-widget">
                    <h3>{{ __('quickad.bookmark_share') }}</h3>
                    <button class="fav-button margin-bottom-20 set-item-fav @if(($item_favorite ?? "") == '1') added @endif" data-item-id="{{ $item_id ?? '' }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd">
                        <span class="fav-icon"></span>
                        <span class="fav-text">{{ __('quickad.save_this_ad') }}</span>
                        <span class="added-text">{{ __('quickad.ad_saved') }}</span>
                    </button>

                    <!-- Share Buttons -->
                    <ul class="share-buttons-icons">
                        <li><a href="mailto:?subject={{ $item_title ?? '' }}&body={{ $item_link ?? '' }}" data-button-color="#dd4b39" title="{{ __('quickad.share_email') }}" data-tippy-placement="top" rel="nofollow" target="_blank"><i class="fa fa-envelope"></i></a></li>
                        <li><a href="https://facebook.com/sharer/sharer.php?u={{ $item_link ?? '' }}" data-button-color="#3b5998" title="{{ __('quickad.share_facebook') }}" data-tippy-placement="top" rel="nofollow" target="_blank"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://twitter.com/share?url={{ $item_link ?? '' }}&text={{ $item_title ?? '' }}" data-button-color="#1da1f2" title="{{ __('quickad.share_twitter') }}" data-tippy-placement="top" rel="nofollow" target="_blank"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $item_link ?? '' }}" data-button-color="#0077b5" title="{{ __('quickad.share_linkedin') }}" data-tippy-placement="top" rel="nofollow" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="https://pinterest.com/pin/create/bookmarklet/?&url={{ $item_link ?? '' }}&description={{ $item_title ?? '' }}" data-button-color="#bd081c" title="{{ __('quickad.share_pinterest') }}" data-tippy-placement="top" rel="nofollow" target="_blank"><i class="fa fa-pinterest-p"></i></a></li>
                        <li><a href="https://web.whatsapp.com/send?text={{ $item_link ?? '' }}" data-button-color="#25d366" title="{{ __('quickad.share_whatsapp') }}" data-tippy-placement="top" rel="nofollow" target="_blank"><i class="fa fa-whatsapp"></i></a></li>
                    </ul>
                </div>
                <div class="sidebar-widget">
                    <h3>{{ __('quickad.more_info') }}</h3>
                    <ul class="related-links">
                        <li>
                            <a href="{{ $item_authorlink ?? '' }}"><i class="la la-suitcase"></i> {{ __('quickad.more_ads') }} {{ $item_authoruname ?? '' }}</a>
                        </li>
                        <li>
                            <a href="{{ $link['REPORT'] ?? '#' }}"><i class="la la-exclamation-triangle"></i> {{ __('quickad.report_this_ad') }}</a>
                        </li>
                    </ul>
                </div>
                <div class="sidebar-widget">
                    <div class="ubm_wrapper">{{ $ad_detail_page_sidebar ?? '' }}</div>
                </div>
            </div>
        </div>
        <div class="ubm_wrapper">{{ $ad_detail_page_above_similar_section ?? '' }}</div>
        @if(($total_items ?? "")!=0)
        <div class="col-md-12 margin-top-30">
            <div class="single-page-section">
                <h3 class="margin-bottom-25">{{ __('quickad.similar_ads') }}</h3>
                <div class="listings-container grid-layout">
                    @foreach($item ?? [] as $item)
                        <div class='job-listing @if((data_get($item ?? [], 'highlight', ''))=="1") highlight @endif'>
                            <div class="job-listing-details">
                                <div class="job-listing-company-logo">
                                    <img class="lazy-load" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}">
                                </div>
                                <div class="job-listing-description">

                                    <h3 class="job-listing-title"><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
                                        @if((data_get($item ?? [], 'featured', ''))=="1") <div class="badge blue"> {{ __('quickad.featured') }}</div> @endif
                                        @if((data_get($item ?? [], 'urgent', ''))=="1") <div class="badge yellow"> {{ __('quickad.urgent') }}</div> @endif
                                        @if((data_get($item ?? [], 'highlight', ''))=="1") <div class="badge blue"> {{ __('quickad.highlight') }}</div>  @endif
                                    </h3>
                                    <ol class="breadcrumb">
                                        <li><a href="{{ data_get($item ?? [], 'catlink', '') }}"><i class="la la-tags"></i> {{ data_get($item ?? [], 'category', '') }}</a></li>
                                        <li><a href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a></li>
                                    </ol>
                                    <h5 class="job-listing-company"><a href="{{ $link['PROFILE'] ?? '#' }}/{{ data_get($item ?? [], 'username', '') }}"><i class="la la-user"></i> {{ data_get($item ?? [], 'username', '') }}</a></h5>
                                </div>

                            </div>
                            <div class="job-listing-footer">
                                <ul>
                                    <li><i class="la la-map-marker"></i> {{ data_get($item ?? [], 'location', '') }}</li>
                                    @if((data_get($item ?? [], 'price', ''))!="0")
                                    <li><i class="la la-credit-card"></i> {{ data_get($item ?? [], 'price', '') }}</li>
                                    @endif
                                    <li><i class="la la-clock-o"></i> {{ data_get($item ?? [], 'created_at', '') }}</li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div id="emailToSeller" class="zoom-anim-dialog mfp-hide dialog-with-tabs popup-dialog">
    <ul class="popup-tabs-nav">
        <li><a href="#tab">{{ __('quickad.send_mail') }} {{ __('quickad.to') }} {{ $item_authoruname ?? '' }}</a></li>
    </ul>
    <div class="popup-tabs-container">
        <div class="popup-tab-content" id="tab">
            <div class="notification error closeable" id="email_error" style="display: none">
                <p>{{ __('quickad.error_try_again') }}</p><a class="close"></a>
            </div>
            <div class="notification success closeable" id="email_success" style="display: none">
                <p>{{ __('quickad.mailsenttoseller') }}</p><a class="close"></a>
            </div>

            <form id="email_contact_seller" method="post" action="email_contact_seller" accept-charset="UTF-8" enctype="multipart/form-data">
                <div class="submit-field">
                    <input type="text" class="form-control" name="name" placeholder="{{ __('quickad.full_name') }}" required="" style="width: 100%">
                    <input type="text" class="form-control" name="email" placeholder="{{ __('quickad.emailad') }}" required="" style="width: 100%">
                    <input type="text" class="form-control" name="phone" placeholder="{{ __('quickad.phone_no') }}" style="width: 100%">
                </div>
                <div class="submit-field">
                    <h5>{{ __('quickad.message') }} *</h5>
                    <textarea cols="30" rows="2" class="form-control" name="message" required="" placeholder="{{ __('quickad.enter_your_message') }}..."></textarea>
                </div>
                <div class="submit-field">
                    @if(($recaptcha_mode ?? "")=="1")
                    <div style="display: inline-block;" class="g-recaptcha" data-sitekey="{{ $recaptcha_public_key ?? '' }}"></div>
                    @endif
                    <span>@if(($recaptch_error ?? "")!="") {{ $recaptch_error ?? '' }} @endif</span>
                </div>
                <input type="hidden" class="form-control" name="id" value="{{ $item_id ?? '' }}">
                <input type="hidden" class="form-control" name="sendemail" value="1">
                <button type="submit" class="button margin-top-35 full-width button-sliding-icon ripple-effect" class="btn btn-outline" id="email_submit_button">{{ __('quickad.send_mail') }}</button>
            </form>
        </div>
    </div>
</div>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/slick.min.js'></script>
<!-- Start jQuery starReviews -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.34/jquery.form-validator.min.js"></script>
<link href="{{ $site_url ?? '' }}plugins/starreviews/assets/css/starReviews.css" rel="stylesheet" type="text/css"/>
<script src="{{ $site_url ?? '' }}plugins/starreviews/assets/js/jquery.barrating.js"></script>
<script src="{{ $site_url ?? '' }}plugins/starreviews/assets/js/starReviews.js"></script>
<style>
    .starReviews hr { margin: 22px 0;}
    .starReviews h2, .starReviews h3 { margin-bottom: 10px;}
    .starReviews { text-align: left;}
    .starReviews label { font-size: 14px;}
</style>
<script type="text/javascript">
    $(document).ready(function () {
        $().reviews('.starReviews');
    });
    @if(($error ?? "")!="")
        $(window).on('load',function () {
            $('.apply-dialog-button').trigger('click');
        });
    @endif
</script>
<!-- END jQuery starReviews -->

@if(($map_type ?? "")=="google")
<link href="{{ $site_url ?? '' }}includes/assets/plugins/map/google/map-marker.css" type="text/css" rel="stylesheet">
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/jquery-migrate-1.2.1.min.js'></script>
<script type='text/javascript' src='//maps.google.com/maps/api/js?key={{ $gmap_api_key ?? '' }}&#038;libraries=places%2Cgeometry&#038;ver=2.2.1'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/richmarker-compiled.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/markerclusterer_packed.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/gmapAdBox.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/maps.js'></script>
<script>
    var _latitude = '{{ $item_lat ?? '' }}';
    var _longitude = '{{ $item_long ?? '' }}';
    var element = "singleListingMap";
    var path = '{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/';
    var getCity = false;
    var color = '{{ $map_color ?? '' }}';
    var site_url = '{{ $site_url ?? '' }}';
    simpleMap(_latitude, _longitude, element);
</script>
{{ $else ?? '' }}
<script>
    var openstreet_access_token = '{{ $openstreet_access_token ?? '' }}';
</script>
<link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/css/style.css">
<!-- Leaflet // Docs: https://leafletjs.com/ -->
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet.min.js"></script>

<!-- Leaflet Maps Scripts (locations are stored in leaflet-quick.js) -->
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-markercluster.min.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-gesture-handling.min.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-quick.js"></script>

<!-- Leaflet Geocoder + Search Autocomplete // Docs: https://github.com/perliedman/leaflet-control-geocoder -->
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-autocomplete.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-control-geocoder.js"></script>
@endif
@include('partials.footer')

