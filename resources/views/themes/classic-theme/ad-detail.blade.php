@include('partials.header')
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/user-html.css" rel="stylesheet" type="text/css"/>
<!-- starReviews stylesheet -->
<link href="{{ $site_url ?? '' }}plugins/starreviews/assets/css/starReviews.css" rel="stylesheet" type="text/css"/>
<!-- main -->
<section id="main" class="clearfix details-page">
    <div class="container" id="serchlist">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li><a href="{{ $item_catlink ?? '' }}">{{ $item_category ?? '' }}</a></li>
                <li class="active">{{ $item_sub_category ?? '' }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>{{ __('quickad.back_result') }}</a></div>
            </ol>
            <!-- breadcrumb -->
        </div>
        <div class="section banner">
            <!-- banner-form -->
            <div class="banner-form banner-form-full">
                <form method="get" action="{{ $site_url ?? '' }}listing" name="locationForm" id="ListingForm">
                    <!-- category-change -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="dropdown category-dropdown"><a data-toggle="dropdown" href="#"><span class="change-text">{{ __('quickad.select_category') }}</span><i class="fa fa-navicon"></i></a>{{ $cat_dropdown ?? '' }}</div>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="keywords" value="" placeholder="{{ __('quickad.what') }} ?" style="padding: 0px;">
                        </div>
                        <div class="col-md-3 banner-icon"><i class="fa fa-map-marker"></i>
                            <input type="text" class="form-control location" id="searchStateCity" name="location" placeholder="{{ __('quickad.where') }} ?" >
                            <input type="hidden" name="placetype" id="searchPlaceType" value="">
                            <input type="hidden" name="placeid" id="searchPlaceId" value="">
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" id="input-maincat" name="cat" value=""/>
                            <input type="hidden" id="input-subcat" name="subcat" value=""/>
                            <button data-ajax-response='map' type="submit" name="Submit" class="form-control"><i class="fa fa-search"></i> {{ __('quickad.search') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- banner-form -->
        </div>
        <!-- banner -->
        <div class="section slider">
            <div class="row">
                <!-- carousel -->
                <div class="col-md-8">
                    <div class="ad-details">

                        @if(($item_hide_phone ?? "")=="no") <ul class="detail-corner-icon"><li><a href="tel:{{ $item_phone ?? '' }}" class="fa fa-phone tooltip-parent field-tip"><span class="tip-content">{{ $item_phone ?? '' }}</span></a></li></ul> @endif

                        <h1 class="title">{{ $item_title ?? '' }}
                            <span class="label-wrap hidden-sm hidden-xs">
                            @if(($item_featured ?? "")=="1") <span class="label featured"> {{ __('quickad.featured') }}</span> @endif
                            @if(($item_urgent ?? "")=="1") <span class="label urgent"> {{ __('quickad.urgent') }}</span> @endif
                            @if(($item_highlight ?? "")=="1") <span class="label highlight"> {{ __('quickad.highlight') }}</span> @endif
                            </span>
                        </h1>
                        <span class="icon"><i class="fa fa-clock-o"></i><a href="#">{{ $item_created ?? '' }}</a></span>
                        <span class="icon"><i class="fa fa-map-marker"></i><a href="#">{{ $item_city ?? '' }}, {{ $item_country ?? '' }}</a></span>
                        <span class="icon"><i class="fa fa-eye"></i><a href="#">{{ __('quickad.ad_views') }}:{{ $item_view ?? '' }}</a></span>
                        <span> {{ __('quickad.ad_id') }}:<a href="#" class="time"> {{ $item_id ?? '' }}</a></span>
                    </div>

                    @if(($show_image_slider ?? "")=="1")
                    <figure class="ad-detail-page">
                        <div id="product-carousel" class="carousel slide" data-ride="carousel" style="position: inherit">

                            <!-- Wrapper for slides -->
                            <?php
                            if("{{ $item_price ?? '' }}"!="0"){
                                echo '<div class="ribbon ribbon-clip ribbon-reverse"><span class="ribbon-inner">{{ $item_price ?? '' }}</span></div>';
                            }
                            ?>
                            <div class="carousel-inner" role="listbox">{{ $item_screens_classb ?? '' }}
                                <!-- Controls -->
                                <a class="left carousel-control" href="#product-carousel" role="button" data-slide="prev"><i class="fa fa-chevron-left"></i></a>
                                <a class="right carousel-control" href="#product-carousel" role="button" data-slide="next"><i class="fa fa-chevron-right"></i></a>
                                <!-- Controls -->
                            </div>
                            <!-- carousel-inner -->
                            <!-- Indicators -->
                            <ol class="carousel-indicators">{{ $item_screens_classsm ?? '' }}</ol>
                        </div>

                    </figure>
                    @endif

                    <div class="description-info">
                        <div class="ads-details">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab-details" data-toggle="tab" aria-expanded="false"><h4>{{ __('quickad.ads_details') }}</h4></a></li>
                                <li><a href="#tab-reviews" data-toggle="tab" aria-expanded="true"><h4>{{ __('quickad.reviews') }} ({{ $itemreview ?? '' }})</h4></a></li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-details">
                                    <div class="quick-info">
                                        <div class="detail-title"><h2 class="title-left">{{ __('quickad.additional_details') }}</h2></div>
                                        <ul class="clearfix">
                                            <li><div class="inner clearfix"><span class="label">{{ __('quickad.ad_id') }}</span><span class="desc">{{ $item_id ?? '' }}</span></div></li>
                                            <li><div class="inner clearfix"><span class="label">{{ __('quickad.posted_on') }}</span><span class="desc">{{ $item_created ?? '' }}</span></div></li>
                                            <li><div class="inner clearfix"><span class="label">{{ __('quickad.ad_views') }}</span><span class="desc">{{ $item_view ?? '' }}</span></div></li>
                                            @if(($item_price ?? "")!="0")
                                            <li><div class="inner clearfix"><span class="label">{{ __('quickad.price') }}</span><span class="desc">{{ $item_price ?? '' }} {{ $item_negotiate ?? '' }}</span></div></li>
                                            @endif


                                            @if(($item_customfield ?? "")!="0")

                                            @foreach($item_custom ?? [] as $item_custom)
                                                <li><div class="inner clearfix"><span class="label">{{ data_get($item_custom ?? [], 'title', '') }}</span><span class="desc">{{ data_get($item_custom ?? [], 'value', '') }}</span></div></li>
                                            @endforeach

                                            @endif
                                        </ul>
                                    </div>
                                    @foreach($item_custom_textarea ?? [] as $item_custom_textarea)
                                        <div class="text-widget">
                                            <div class="detail-title"><h2 class="title-left">{{ data_get($item_custom_textarea ?? [], 'title', '') }}</h2></div>
                                            <div class="inner"><div class="user-html">{{ data_get($item_custom_textarea ?? [], 'value', '') }}</div></div>
                                        </div>
                                    @endforeach


                                    @foreach($item_custom_checkbox ?? [] as $item_custom_checkbox)
                                        <div class="text-widget">
                                            <div class="detail-title"><h2 class="title-left">{{ data_get($item_custom_checkbox ?? [], 'title', '') }}</h2></div>
                                            <ul class="amenities">{{ data_get($item_custom_checkbox ?? [], 'value', '') }}</ul>
                                        </div>
                                    @endforeach

                                    <div class="description">
                                        <div class="detail-title"><h2 class="title-left">{{ __('quickad.description') }}</h2></div>
                                        <div class="user-html">{{ $item_desc ?? '' }}</div>
                                        <!-- <p class="show-more"></p>
                                        <a href="#" class="show-more-button" data-more-title="{{ __('quickad.show_more') }}"
                                           data-less-title="{{ __('quickad.show_less') }}"><i class="fa fa-angle-down"></i></a> -->
                                    </div>

                                    @if(($show_tag ?? "")=="1")
                                    <div class="text-widget">
                                        <div class="detail-title"><h2 class="title-left">{{ __('quickad.product_tag') }}</h2></div>
                                        <div class="inner"><ul class="tags">{{ $item_tag ?? '' }}</ul></div>
                                    </div>
                                    @endif

                                    <div class="description @if(($post_address_mode ?? "")!="1") hidden @endif">
                                        <div class="detail-title"><h2 class="title-left">{{ __('quickad.location') }}</h2></div>
                                        <div>
                                            <div class="map-widget map height-200px" id="singleListingMap" data-latitude="{{ $item_lat ?? '' }}" data-longitude="{{ $item_long ?? '' }}" data-map-icon="fa fa-marker"></div>
                                            <span><a href="https://maps.google.com/?q={{ $item_location ?? '' }}" target="_blank" rel="nofollow">{{ $item_location ?? '' }}</a></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="reviews-widget tab-pane" id="tab-reviews">
                                    <!-- **** Start reviews **** -->
                                    <div class="starReviews text-widget">
                                        <!-- This is where your product ID goes -->
                                        <div id="review-productId" class="review-productId" style="">{{ $item_id ?? '' }}</div>
                                        <!-- Show current reviews -->
                                        <div class="show-reviews"><div class="loader" style="margin: 0 auto;"></div></div>
                                        <hr>

                                        @if(($logged_in ?? "")=="0")
                                        <div style="padding-top: 10px"><a class="modal-trigger btn btn-primary" href="#loginPopUp">{{ __('quickad.logintoreview') }}</a></div>
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
                            <!-- /.tab content -->
                        </div>
                    </div>
                </div>
                <!-- Controls -->
                <!-- slider-text -->
                <div class="col-md-4">
                    <div class="ad-details">
                        <div class="aside">
                            <div class="aside-header">{{ __('quickad.contact_advertiser') }}</div>
                            <div class="aside-body text-center">
                                <!-- short-info -->
                                <div class="user-info ">
                                    <div class="profile-picture">
                                        <img width="70px" style="min-height:73px" src="{{ $site_url ?? '' }}storage/profile/small_{{ $item_authorimg ?? '' }}" alt="{{ $item_authoruname ?? '' }}">
                                    </div>
                                    <h4><a href="{{ $item_authorlink ?? '' }}"> {{ $item_authorname ?? '' }} @if(($item_authorname ?? "")=="") {{ $item_authoruname ?? '' }} @endif</a>
                                        @if(($sub_image ?? "")!="")
                                        <img src="{{ $sub_image ?? '' }}" alt="{{ $sub_title ?? '' }}" title="{{ $sub_title ?? '' }}" width="24px"/>
                                        @endif
                                    </h4>
                                    <p><strong>{{ __('quickad.joined') }}: </strong><a href="#">{{ $item_authorjoined ?? '' }}</a></p>
                                    @if(($item_hide_phone ?? "")=="no")
                                    <p><strong>{{ __('quickad.phone') }} : </strong><a href="tel:{{ $item_phone ?? '' }}">{{ $item_phone ?? '' }}</a></p>
                                    @endif
                                </div>
                                <!-- short-info -->

                                <!-- contact-advertiser -->
                                <div class="contact-advertiser">

                                    @if(($logged_in ?? ""))

                                    @if(($zechat ?? "")=='on' || ($quickchat ?? "")=='on' || ($quickchat_socket_on_off ?? "")=='on')
                                    <button type="button" class="btn btn-warning start_zechat zechat-hide-under-768px"
                                            data-chatid="{{ $item_authorid ?? '' }}_{{ $item_id ?? '' }}"
                                            data-postid="{{ $item_id ?? '' }}"
                                            data-userid="{{ $item_authorid ?? '' }}"
                                            data-fullname="{{ $item_authorname ?? '' }}"
                                            data-username="{{ $item_authoruname ?? '' }}"
                                            data-userimage="{{ $item_authorimg ?? '' }}"
                                            data-userstatus="{{ $item_authoronline ?? '' }}"><i class="fa fa-comment-o"></i> {{ __('quickad.chat_now') }}</button>

                                    <a href="{{ $quickchat_url ?? '' }}" class="btn btn-warning zechat-show-under-768px">{{ __('quickad.chat_now') }} <i class="fa fa-comment-o"></i></a>
                                    @endif

                                    {{ $else ?? '' }}
                                    <a class="modal-trigger btn btn-warning" href="#loginPopUp"><i class="fa fa-comment-o"></i>{{ __('quickad.login_chat') }}</a>
                                    @endif
                                    <a href="#" class="btn btn-info" data-toggle="modal" data-target="#emailToSeller"><i class="fa fa-envelope"></i>{{ __('quickad.reply_mail') }}</a>
                                </div>
                                <!-- contact-advertiser -->
                                <!-- social-links -->
                                <div class="social-links text-center">
                                    <h4>{{ __('quickad.share_ad') }}</h4>
                                    <div class="social-share"></div>
                                    <!--end social-->
                                </div>
                                <!-- social-links -->
                            </div>
                        </div>

                        <!-- Rating-info -->
                        <div class="aside margin-top-20">
                            <div class="aside-body ">
                                <div class="more-info">
                                    <!-- **** Start reviews **** -->
                                    <div class="starReviews">
                                        <!-- Show average-rating -->
                                        <div class="average-rating"><div class="small_loader" style="margin: 0 auto;"></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Rating-info -->
                        <!-- short-info -->
                        <div class="aside margin-top-20">
                            <div class="aside-body ">
                                <div class="more-info">
                                    <h4>{{ __('quickad.more_info') }}</h4>
                                    <!-- social-icon -->
                                    <ul id="set-favorite">
                                        <li><a href="#" data-item-id="{{ $item_id ?? '' }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd" class="fav_{{ $item_id ?? '' }} fa fa-heart @if(($item_favorite ?? "")=="1") active @endif"><span style="font-family: 'Open Sans', sans-serif;color: #707070;font-size: 15px;">{{ __('quickad.save_as_favourite') }}</span></a>
                                        </li>
                                    </ul>
                                    <ul>
                                        <li><i class="fa fa-user-plus"></i><a href="{{ $item_authorlink ?? '' }}">{{ __('quickad.more_ads') }}<span> {{ $item_authoruname ?? '' }} </span></a></li>
                                        <li><i class="fa fa-exclamation-triangle"></i><a href="{{ $link['REPORT'] ?? '#' }}">{{ __('quickad.report_this_ad') }}</a></li>
                                    </ul>
                                    <!-- social-icon -->
                                </div>
                            </div>
                        </div>
                        <!-- short-info -->

                        <!-- Advertise-Box -->
                        @if(($right_adstatus ?? "")=="1")
                        <div class="quickad-section" id="quickad-right">
                            <div class="text-center visible-md visible-lg">
                                {{ $right_adscode ?? '' }}
                            </div>
                        </div>
                        @endif
                        <!-- Advertise-Box -->
                    </div>
                </div>
                <!-- slider-text -->
        </div>
    </div>
    <!-- slider -->
    <!-- featured-slide -->
    <div class="section recommended-ads">
        <div class="row">
            <div class="col-sm-12">
                <div class="featured-top"><h4>{{ __('quickad.recommended_ads') }}</h4></div>
            </div>
        </div>
        <!-- featured-slider -->
        <div class="recommended-slider">
            <div id="recommended-slider-id">
                @foreach($item ?? [] as $item)
                    <!-- quick-item -->
                    <div class='quick-item @if(" {{ data_get($item ?? [], 'highlight', '') }}"=="1") highlight @endif'>
                        <!-- item-image -->
                        <div class="item-image-box">
                            <div class="item-image">
                                <a href="{{ data_get($item ?? [], 'link', '') }}"><img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}" class="img-responsive"></a>
                                <div class="item-badges">
                                    @if((data_get($item ?? [], 'featured', ''))=="1") <span class="featured">{{ __('quickad.featured') }}</span> @endif
                                    @if((data_get($item ?? [], 'urgent', ''))=="1") <span>{{ __('quickad.urgent') }}</span> @endif
                                </div>
                            </div>
                        </div>
                        <!-- item-image -->
                        <div class="item-info">
                            <!-- ad-info -->
                            <div class="ad-info">
                                <h4 class="item-title"><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a></h4>
                                <ol class="breadcrumb"><li><a href="{{ data_get($item ?? [], 'catlink', '') }}">{{ data_get($item ?? [], 'category', '') }}</a></li></ol>
                                <ul class="item-details">
                                    <li><i class="fa fa-map-marker"></i><a href="{{ data_get($item ?? [], 'citylink', '') }}">{{ data_get($item ?? [], 'cityname', '') }}, {{ data_get($item ?? [], 'country', '') }}</a></li>
                                    <li><i class="fa fa-clock-o"></i>{{ data_get($item ?? [], 'created_at', '') }}</li>
                                </ul>
                                <div class="ad-meta">
                                    @if((data_get($item ?? [], 'price', ''))!="0") <span class="item-price"> {{ data_get($item ?? [], 'price', '') }}</span> @endif
                                    <ul class="contact-options pull-right" id="set-favorite">
                                        <li><a href="#" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd" class="fav_{{ data_get($item ?? [], 'id', '') }} fa fa-heart @if((data_get($item ?? [], 'favorite', ''))=="1") active @endif"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <!-- ad-info -->
                        </div>
                        <!-- item-info -->
                    </div>
                    <!-- quick-item -->
                @endforeach
            </div>
        </div>
        <!-- #featured-slider -->
    </div>
    <!-- featured -->
    </div>
    <!-- container -->
</section>
<!-- main -->
<!-- Modal -->
<div id="emailToSeller" class="modal fade" role="dialog">
    <div class="modal-dialog"><!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{ __('quickad.send_mail') }} {{ __('quickad.to') }} {{ $item_authoruname ?? '' }}</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" id="email_success" style="display: none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{ __('quickad.mailsenttoseller') }}</div>
                <div class="alert alert-danger" id="email_error" style="display: none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>{{ __('quickad.error_try_again') }}</div>
                <div class="feed-back-form">
                    <form method="post" id="email_contact_seller" action="email_contact_seller">
                        <div id="post_loading" class="loader" style="display: none;margin: 0 auto;"></div>
                        <input type="text" class="form-control" name="name" placeholder="{{ __('quickad.full_name') }}" required="" style="width: 100%">
                        <input type="text" class="form-control" name="email" placeholder="{{ __('quickad.emailad') }}" required="" style="width: 100%">
                        <input type="text" class="form-control" name="phone" placeholder="{{ __('quickad.phone_no') }}" style="width: 100%">
                        <!---728x90--->
                        <span>{{ __('quickad.message') }} ?</span>
                        <textarea type="text" class="form-control" name="message" placeholder="{{ __('quickad.enter_your_message') }}..." required="" rows="2" style="width: 100%;height: 100px"></textarea>
                        <input type="hidden" class="form-control" name="id" value="{{ $item_id ?? '' }}">
                        <input type="hidden" class="form-control" name="sendemail" value="1">
                        <input type="submit" class="btn btn-outline" value="{{ __('quickad.send_mail') }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("#email_contact_seller").on('submit', function() {

        $('#email_contact_seller #post_loading').show();
        var action = $("#email_contact_seller").attr('action');
        var form_data = $(this).serialize();

        $.ajax({
            type: "POST",
            url: ajaxurl+'?action='+action,
            data: form_data,
            success: function (response) {
                if (response == "success") {
                    $('#email_success').show();
                }
                else {
                    $('#email_error').show();
                }
                $('#email_contact_seller #post_loading').hide();
            }
        });
        return false;
    });

    $('.show-more-button').on('click', function (e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $('.show-more').toggleClass('visible');
        if ($('.show-more').is(".visible")) {
            var el = $('.show-more'),
                    curHeight = el.height(),
                    autoHeight = el.css('height', 'auto').height();
            el.height(curHeight).animate({
                height: autoHeight
            }, 400);
        } else {
            $('.show-more').animate({
                height: '100px'
            }, 400);
        }
    });
</script>
<script async="async" type="text/javascript">
    function socialShare() {
        var socialButtonsEnabled = 1;
        if (socialButtonsEnabled == 1) {
            $('head').append($('<link rel="stylesheet" type="text/css">').attr('href', 'https://cdn.jsdelivr.net/jquery.jssocials/1.4.0/jssocials.css'));
            $('head').append($('<link rel="stylesheet" type="text/css">').attr('href', 'https://cdn.jsdelivr.net/jquery.jssocials/1.4.0/jssocials-theme-flat.css'));
            $.getScript("{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/social-share/jssocials.min.js", function (data, textStatus, jqxhr) {
                $(".social-share").jsSocials({
                    showLabel: false,
                    showCount: false,
                    shares: ["email", "twitter", "facebook", "googleplus", "linkedin", "pinterest", "whatsapp"]
                });
            });
        }
    }
    //  Social Share -------------------------------------------------------------------------------------------------------
    if ($(".social-share").length) {
        socialShare();
    }

    var loginurl = "{{ $link['LOGIN'] ?? '#' }}?ref=listing";
</script>
<!-- jQuery Form Validator -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.34/jquery.form-validator.min.js"></script>
<!-- jQuery Barrating plugin -->
<script src="{{ $site_url ?? '' }}plugins/starreviews/assets/js/jquery.barrating.js"></script>
<!-- jQuery starReviews -->
<script src="{{ $site_url ?? '' }}plugins/starreviews/assets/js/starReviews.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        /* Activate our reviews */
        $().reviews('.starReviews');
    });
</script>


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
