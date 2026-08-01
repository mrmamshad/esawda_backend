@include('partials.header')
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/css/user-html.css" rel="stylesheet" type="text/css"/>
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/postad/slick.css" rel="stylesheet" type="text/css">
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/postad/detail-page.css" rel="stylesheet" type="text/css">

<!-- starReviews stylesheet -->
<link href="{{ $site_url ?? '' }}plugins/starreviews/assets/css/starReviews.css" rel="stylesheet" type="text/css"/>
<div class="container">
    <ol class="breadcrumb">
        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
        <li class="active">{{ $item_title ?? '' }}</li>
    </ol>

    <div class="item-single row">

        <div class="item-content col-xs-12 col-sm-7 col-md-8">

            <article class="inner">
                <header>
                    <h1>{{ $item_title ?? '' }}
                     <span class="label-wrap hidden-sm hidden-xs">

                        @if(($item_featured ?? "")=="1") <span class="label featured"> {{ __('quickad.featured') }}</span> @endif
                        @if(($item_urgent ?? "")=="1") <span class="label urgent"> {{ __('quickad.urgent') }}</span> @endif
                        @if(($item_highlight ?? "")=="1") <span class="label highlight"> {{ __('quickad.highlight') }}</span> @endif
                    </span>
                    </h1>
                    <ul class="info-list">
                        <li><i class="fa fa-map-marker"></i><a href="#">{{ $item_city ?? '' }}, {{ $item_country ?? '' }}</a></li>
                        <li><i class="fa fa-clock-o"></i>{{ $item_created ?? '' }}</li>
                        <li><i class="fa fa-eye"></i> {{ __('quickad.ad_views') }}: {{ $item_view ?? '' }}</li>
                        <li><i class="fa fa-bookmark"></i>{{ __('quickad.id') }}: {{ $item_id ?? '' }}</li>
                    </ul>
                </header>
                @if(($show_image_slider ?? "")=="1")
                <div class="item-gallery-slider">
                    <div class="item-lg-images">
                        <a href="#" class="trigger-gallery"><i class="fa fa-arrows-alt"></i></a>
                        <div class="slick-carousel slick-lg-images" data-asnav=".slick-sm-images" data-fade="true" data-slides-scroll="1" data-dots="false" data-nav="false" data-slides="1" data-slides-lg="1" data-slides-md="1" data-slides-sm="1" data-loop="true" data-auto="true">
                            {{ $item_screens_big ?? '' }}
                        </div>
                    </div>
                    <div class="item-sm-images">
                        <div class="slick-carousel slick-sm-images" data-focus="true" data-asnav=".slick-lg-images"  data-slides-scroll="1" data-dots="false" data-nav="true" data-prev="fa fa-chevron-left" data-next="fa fa-chevron-right" data-slides="6" data-slides-lg="4" data-slides-md="4" data-slides-sm="2" data-loop="true" data-auto="false">
                            {{ $item_screens_sm ?? '' }}

                        </div>
                    </div>
                </div>

                <div class="full-width-gallery">
                    <div class="inner">
                        <div class="container">
                            <div class="gallery-lg-area">
                                <a href="#" class="close-lg-gallery"><i class="fa fa-close"></i></a>
                                <div class="slick-carousel slick-gallery-lg-images" data-asnav=".slick-gallery-thumbs" data-fade="true" data-slides-scroll="1" data-dots="false" data-nav="false" data-slides="1" data-slides-lg="1" data-slides-md="1" data-slides-sm="1" data-loop="true" data-auto="false">

                                    {{ $item_screens_big ?? '' }}

                                </div>
                            </div>
                        </div>
                        <div class="gallery-thumbs">
                            <div class="container">
                                <div class="gallery-thumbs-inner">
                                    <div class="slick-carousel slick-gallery-thumbs" data-focus="true" data-asnav=".slick-gallery-lg-images"  data-slides-scroll="1" data-dots="false" data-nav="true" data-prev="fa fa-chevron-left" data-next="fa fa-chevron-right" data-slides="6" data-slides-lg="4" data-slides-md="4" data-slides-sm="2" data-loop="true" data-auto="false">
                                        {{ $item_screens_big ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if(($item_customfield ?? "")!="")
                <div class="quick-info">
                    <div class="detail-title">
                        <h2 class="title-left">{{ __('quickad.additional_details') }}</h2>
                    </div>
                    <ul class="clearfix">
                        @foreach($item_custom ?? [] as $item_custom)
                        <li>
                            <div class="inner clearfix">
                                <span class="label">{{ data_get($item_custom ?? [], 'title', '') }}</span>
                                <span class="desc">{{ data_get($item_custom ?? [], 'value', '') }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @foreach($item_custom_checkbox ?? [] as $item_custom_checkbox)
                <div class="text-widget">
                    <div class="detail-title">
                        <h2 class="title-left">{{ data_get($item_custom_checkbox ?? [], 'title', '') }}</h2>
                    </div>
                    <ul class="amenities">{{ data_get($item_custom_checkbox ?? [], 'value', '') }}</ul>
                </div>
                @endforeach


                <div class="text-widget">
                    <div class="detail-title">
                        <h2 class="title-left">{{ __('quickad.description') }}</h2>
                    </div>
                    <div class="inner">
                        <div class="user-html">{{ $item_desc ?? '' }}</div>
                    </div>
                </div>
                @if(($show_tag ?? "")=="1")
                <div class="text-widget">
                    <div class="detail-title">
                        <h2 class="title-left">{{ __('quickad.product_tag') }}</h2>
                    </div>
                    <div class="inner">
                        <ul class="tags">
                            {{ $item_tag ?? '' }}
                        </ul>
                    </div>
                </div>
                @endif
                <section>

                    <!-- **** Start reviews **** -->
                    <div class="starReviews text-widget">
                        <div class="detail-title">
                            <h2 class="title-left">{{ __('quickad.reviews') }}</h2>
                        </div>
                        <!-- This is where your product ID goes -->
                        <div id="review-productId" class="review-productId" style="">{{ $item_id ?? '' }}</div>
                        <!-- Show current reviews -->
                        <div class="show-reviews">
                            <div class="loader" style="margin: 0 auto;"></div>
                        </div>
                        <hr>

                        @if(($logged_in ?? "")=="0")
                        <div style="padding-top: 10px"><a class="modal-trigger btn btn-primary"  data-toggle="modal" data-target="#loginPopUp" href="#loginPopUp">{{ __('quickad.logintoreview') }}</a></div>
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

                </section>

                <section id="send-message">
                    <div class="property-description detail-block">
                        <div class="detail-title">
                            <h2 class="title-left">{{ __('quickad.email') }} to {{ $item_authoruname ?? '' }}</h2>
                        </div>

                        <div class="alert alert-success" id="email_success" style="display: none">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ __('quickad.mailsenttoseller') }}
                        </div>
                        <div class="alert alert-danger" id="email_error" style="display: none">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ __('quickad.error_try_again') }}
                        </div>
                        <form method="post" id="email_contact_seller" action="email_contact_seller">
                            <div id="post_loading" class="loader" style="display: none;margin: 0 auto;"></div>
                            <div class="input-field">
                                <label for="name">{{ __('quickad.full_name') }}</label>
                                <input type="text" name="name" id="name" value="">
                            </div>
                            <div class="input-field">
                                <label for="email">{{ __('quickad.emailad') }}</label>
                                <input type="email" name="email" id="email" value="">
                            </div>
                            <div class="input-field">
                                <label for="phone">{{ __('quickad.phone_no') }}</label>
                                <input type="text" name="phone" id="phone" value="">
                            </div>
                            <div class="input-field">
                                <label for="message">{{ __('quickad.enter_your_message') }}</label>
                                <textarea name="message" class="materialize-textarea" id="message" rows="4"></textarea>
                            </div>
                            <div class="input-field">
                                <input type="hidden" class="form-control" name="id" value="{{ $item_id ?? '' }}">
                                <input type="hidden" class="form-control" name="sendemail" value="1">
                                <input type="submit" class="btn btn-primary btn-rounded" value="{{ __('quickad.send_mail') }}">
                            </div>

                        </form>
                        <!--end form-->
                    </div>
                </section>
            </article>


        </div>
        <aside class="sidebar col-xs-12 col-sm-5 col-md-4">
            <div class="inner">
                @if(($item_price ?? "")!="0")
                <div class="price-widget short-widget">
                    <strong>{{ $item_price ?? '' }}</strong>
                    <span>{{ $item_negotiate ?? '' }}</span>
                </div>
                @endif
                <div class="user-widget text-center">
                    <div class="profile-picture">
                        <img width="70px" style="min-height:73px" src="{{ $site_url ?? '' }}storage/profile/{{ $item_authorimg ?? '' }}" alt="{{ $item_authoruname ?? '' }}">
                    </div>
                    <h4><a href="{{ $item_authorlink ?? '' }}">{{ $item_authoruname ?? '' }}</a>
                        @if(($sub_image ?? "")!="")
                        <img src="{{ $sub_image ?? '' }}" alt="{{ $sub_title ?? '' }}" title="{{ $sub_title ?? '' }}" width="24px"/>
                        @endif
                    </h4>

                    @if(($item_hide_phone ?? "")=="no")
                    <div><i class="fa fa-phone"></i> <strong><a href="tel:{{ $item_phone ?? '' }}">{{ $item_phone ?? '' }}</a></strong></div>
                    @endif
                    <a href="{{ $item_authorlink ?? '' }}" class="link">{{ __('quickad.view_profile') }}</a>
                    <div style="padding: 10px 0;">
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

                        @if(($logged_in ?? "")&($zechat ?? "")=="1&on")

                        @endif

                        <a href="#send-message"><button type="button" class="btn btn-primary">{{ __('quickad.reply_mail') }}</button></a>

                    </div>
                    <a href="{{ $link['REPORT'] ?? '#' }}" class="link" style="color: red">{{ __('quickad.report_this_ad') }}</a>
                </div>
                <div class="share-widget">
                    <span>{{ __('quickad.share_ad') }}</span>
                    <div class="social-share"></div>
                </div>
                <div class="map-widget map height-250px" id="map-detail">

                </div>

                <div class="spnser-widget hidden">
                    <img src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/img/spenser.jpg" alt="spnser" width="100%">
                </div>
                <section style="margin-top: 20px;">
                    <h2>{{ __('quickad.similar_ads') }}</h2>

                    @foreach($item ?? [] as $item)
                    <div class="item">
                        <a href="{{ data_get($item ?? [], 'link', '') }}" class="ad-listing">
                            <div class="description">
                                <div class="label label-default">{{ data_get($item ?? [], 'category', '') }}</div>
                                <h3>{{ data_get($item ?? [], 'product_name', '') }}</h3>
                                <!--<h4>Posted by : {{ data_get($item ?? [], 'username', '') }}</h4>
                                <h4>Location : {{ data_get($item ?? [], 'cityname', '') }}</h4>-->
                                <h4>{{ __('quickad.posted_on') }} : {{ data_get($item ?? [], 'created_at', '') }}</h4>
                            </div>
                            <!--end description-->
                            <div class="image bg-transfer">
                                <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}">
                            </div>
                            <!--end image-->
                        </a>
                    </div>
                    @endforeach

                </section>
            </div>
        </aside>

    </div>

</div>

<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/postad/slick.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/postad/app.js"></script>

<script type="text/javascript">
    function socialShare() {
        var socialButtonsEnabled = 1;
        if (socialButtonsEnabled == 1) {
            $('head').append($('<link rel="stylesheet" type="text/css">').attr('href', 'https://cdn.jsdelivr.net/jquery.jssocials/1.4.0/jssocials.css'));
            $('head').append($('<link rel="stylesheet" type="text/css">').attr('href', 'https://cdn.jsdelivr.net/jquery.jssocials/1.4.0/jssocials-theme-flat.css'));
            $.getScript("{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/js/jssocials.min.js", function (data, textStatus, jqxhr) {
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

