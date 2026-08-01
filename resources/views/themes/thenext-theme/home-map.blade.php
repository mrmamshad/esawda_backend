@include('partials.header')

<div id="road_map" class="map"></div>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!-- banner-form -->
            <div class="banner-form banner-form-full">
                <form action="{{ $link['LISTING'] ?? '#' }}" method="get" id="hero-home-map">
                    <!-- category-change -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="dropdown category-dropdown"><a data-toggle="dropdown" href="#"><span class="change-text">{{ __('quickad.select_category') }}</span><i class="fa fa-navicon"></i></a>{{ $cat_dropdown ?? '' }}</div>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="serachStr" placeholder="{{ __('quickad.what') }} ?" style="padding: 0px;">
                        </div>
                        <div class="col-md-3 banner-icon geo-location"><i class="fa fa-map-marker"></i>
                            <input type="text" class="form-control" id="address-autocomplete" name="location" placeholder="{{ __('quickad.where') }} ?" style=" border-left: 1px solid #e0e0e0;">
                            <input type="hidden" id="latitude" name="latitude"/>
                            <input type="hidden" id="longitude" name="longitude"/>
                            <input type="hidden" id="locality" name="locality"/>
                            <input type="hidden" id="administrative_area_level_2" name="city"/>
                            <input type="hidden" id="administrative_area_level_1" name="state"/>
                            <input type="hidden" id="country" name="country"/>
                        </div>
                        <div class="col-md-2">
                            <input type="hidden" id="input-maincat" name="searchBox" value=""/>
                            <input type="hidden" id="input-subcat" name="subcat" value=""/>
                            <button data-ajax-response='map' data-ajax-auto-zoom="1" type="submit" name="searchform"
                                    class="form-control"><i class="fa fa-search"></i> {{ __('quickad.search') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- banner-form -->
        </div>
    </div>
</div>

<!-- Category Boxes -->
<div class="section margin-top-65">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-headline centered margin-bottom-15">
                    <h3>{{ __('quickad.all_categories') }}</h3>
                </div>
                <div class="categories-container">
                    @foreach($cat ?? [] as $cat)
                        <a href="{{ data_get($cat ?? [], 'catlink', '') }}" class="category-box">
                            <div class="category-box-icon margin-bottom-10">
                                @if((data_get($cat ?? [], 'picture', ''))=="")
                                <div class="category-icon"><i class="{{ data_get($cat ?? [], 'icon', '') }}"></i></div>
                                {{ $else ?? '' }}
                                <div class="category-icon">
                                    <img class="lazy-load" src="{{ data_get($cat ?? [], 'picture', '') }}" alt="{{ data_get($cat ?? [], 'main_title', '') }}">
                                </div>
                                @endif
                            </div>
                            <div class="category-box-counter">{{ data_get($cat ?? [], 'main_ads_count', '') }}</div>
                            <div class="category-box-content">
                                <h3>{{ data_get($cat ?? [], 'main_title', '') }} <small>({{ data_get($cat ?? [], 'main_ads_count', '') }})</small></h3>
                            </div>
                            <div class="category-box-arrow">
                                <i class="fa fa-chevron-right"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
                @if(($top_adstatus ?? "")=="1")
                <div class="quickad-section text-center margin-top-15" id="quickad-top">{{ $top_adscode ?? '' }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Features POST -->
<div class="section margin-top-45 padding-top-65 padding-bottom-65">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-headline margin-top-0 margin-bottom-35">
                    <h3>{{ __('quickad.premium_ads') }}</h3>
                    <a href="{{ $link['LISTING'] ?? '#' }}?filter=premium" class="headline-link">{{ __('quickad.view_more') }}</a>
                </div>
                <div class="listings-container grid-layout margin-top-35">
                    <div class="row" style="width: 100%;">
                        @foreach($item ?? [] as $item)
                            <div class="col-xl-4">
                                <div class='feat_property @if((data_get($item ?? [], 'highlight', ''))=="1") highlight @endif'>
                                    <div class="thumb">
                                        <img class="img-whp lazy-load" src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}">
                                        <div class="thmb_cntnt">
                                            <ul class="tag mb0">
                                                @if((data_get($item ?? [], 'featured', ''))=="1") <li class="list-inline-item featured"><a href="#"> {{ __('quickad.featured') }}</li> @endif
                                                @if((data_get($item ?? [], 'urgent', ''))=="1") <li class="list-inline-item urgent"><a href="#"> {{ __('quickad.urgent') }}</li> @endif
                                            </ul>

                                            @if((data_get($item ?? [], 'price', ''))!="0")
                                            <a class="fp_price" href="#">{{ data_get($item ?? [], 'price', '') }}</a>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="details">
                                        <div class="tc_content">
                                            <p class="text-thm"><a href="{{ data_get($item ?? [], 'subcatlink', '') }}"><i class="la la-tags"></i> {{ data_get($item ?? [], 'sub_category', '') }}</a></p>
                                            <h4><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a></h4>
                                            <p><i class="la la-map-marker"></i> {{ data_get($item ?? [], 'location', '') }}</p>
                                            <ul class="prop_details mb0">
                                                {{ data_get($item ?? [], 'cf_tpl', '') }}
                                            </ul>
                                        </div>
                                        <div class="listing-footer">
                                            <a class="author-link" href="{{ $link['PROFILE'] ?? '#' }}/{{ data_get($item ?? [], 'username', '') }}"><i class="fa fa-user" aria-hidden="true"></i> {{ data_get($item ?? [], 'username', '') }}</a>
                                            <span><i class="fa fa-calendar-o" aria-hidden="true"></i> {{ data_get($item ?? [], 'created_at', '') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if(($top_adstatus ?? "")=="1")
                <div class="quickad-section text-center margin-top-15" id="quickad-top">{{ $top_adscode ?? '' }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Featured POST / End -->

<!-- Latest POST -->
<div class="section gray padding-top-65 padding-bottom-75">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-headline margin-top-0 margin-bottom-35">
                    <h3>{{ __('quickad.latest_ads') }}</h3>
                    <a href="{{ $link['LISTING'] ?? '#' }}" class="headline-link">{{ __('quickad.view_more') }}</a>
                </div>
                <div class="latest_property listings-container compact-layout margin-top-35">
                    @foreach($item2 ?? [] as $item2)
                        <div class='job-listing @if((data_get($item2 ?? [], 'highlight', ''))=="1") highlight @endif'>
                            <div class="job-listing-details">
                                <div class="job-listing-company-logo">
                                    <img class="lazy-load" src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item2 ?? [], 'picture', '') }}" alt="{{ data_get($item2 ?? [], 'product_name', '') }}">
                                </div>
                                <div class="job-listing-description">

                                    <h3 class="job-listing-title margin-bottom-10"><a href="{{ data_get($item2 ?? [], 'link', '') }}">{{ data_get($item2 ?? [], 'product_name', '') }}</a>
                                        @if((data_get($item2 ?? [], 'featured', ''))=="1") <div class="badge blue"> {{ __('quickad.featured') }}</div> @endif
                                        @if((data_get($item2 ?? [], 'urgent', ''))=="1") <div class="badge yellow"> {{ __('quickad.urgent') }}</div> @endif
                                    </h3>
                                    <span class="job-type"><a href="{{ data_get($item2 ?? [], 'catlink', '') }}"><i class="la la-tags"></i> {{ data_get($item2 ?? [], 'category', '') }}</a></span>
                                    <div class="job-listing-footer">
                                        <ul class="prop_details">
                                            {{ data_get($item2 ?? [], 'cf_tpl', '') }}
                                        </ul>
                                        <ul>
                                            <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ data_get($item2 ?? [], 'username', '') }}"><i class="la la-user"></i> {{ data_get($item2 ?? [], 'username', '') }}</a></li>
                                            <li><i class="la la-map-marker"></i> {{ data_get($item2 ?? [], 'location', '') }}</li>
                                            @if((data_get($item2 ?? [], 'price', ''))!="0")
                                            <li><i class="la la-credit-card"></i> {{ data_get($item2 ?? [], 'price', '') }}</li>
                                            @endif
                                            <li><i class="la la-clock-o"></i> {{ data_get($item2 ?? [], 'created_at', '') }}</li>
                                        </ul>
                                    </div>
                                </div>

                            </div>

                        </div>
                    @endforeach
                </div>
                @if(($bottom_adstatus ?? "")=="1")
                <div class="quickad-section text-center margin-top-15" id="quickad-top">{{ $bottom_adscode ?? '' }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Latest POST / End -->

@if(($testimonials_enable ?? "") && ($show_testimonials_home ?? ""))
<div class="section gray padding-top-55 padding-bottom-55">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <!-- Section Headline -->
                <div class="section-headline centered margin-top-0 margin-bottom-25">
                    <h3>{{ __('quickad.testimonials') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="fullwidth-carousel-container margin-top-20">
        <div class="testimonial-carousel testimonials">
            @foreach($testimonials ?? [] as $testimonials)
                <div class="single-testimonial">
                    <div class="single-inner style-2">
                        <div class="testimonial-content">
                            {{ data_get($testimonials ?? [], 'content', '') }}
                        </div>
                        <div class="author-info">
                            <div class="image">
                                <img class="lazy-load" src="{{ $site_url ?? '' }}storage/testimonials/{{ data_get($testimonials ?? [], 'image', '') }}" alt="{{ data_get($testimonials ?? [], 'name', '') }}">
                            </div>
                            <h5 class="name">{{ data_get($testimonials ?? [], 'name', '') }}</h5>
                            <span class="designation">{{ data_get($testimonials ?? [], 'designation', '') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@if(($show_membershipplan_home ?? ""))
<!-- Membership Plans -->
<div class="section padding-top-60 padding-bottom-75">
    <div class="container">
        <div class="row">

            <div class="col-xl-12">
                <!-- Section Headline -->
                <div class="section-headline centered margin-top-0 margin-bottom-75">
                    <h3>{{ __('quickad.membershipplan') }}</h3>
                </div>
            </div>


            <div class="col-xl-12">
                <form name="form1" method="post" action="{{ $link['MEMBERSHIP'] ?? '#' }}">
                    <div class="billing-cycle-radios margin-bottom-70">
                        @if(($total_monthly ?? "")!="0")
                        <div class="radio billed-monthly-radio">
                            <input id="radio-monthly" name="billed-type" type="radio" value="monthly" checked="">
                            <label for="radio-monthly"><span class="radio-label"></span> {{ __('quickad.monthly') }}</label>
                        </div>
                        @endif
                        @if(($total_annual ?? "")!="0")
                        <div class="radio billed-yearly-radio">
                            <input id="radio-yearly" name="billed-type" type="radio" value="yearly">
                            <label for="radio-yearly"><span class="radio-label"></span> {{ __('quickad.yearly') }}</label>
                        </div>
                        @endif
                        @if(($total_lifetime ?? "")!="0")
                        <div class="radio billed-lifetime-radio">
                            <input id="radio-lifetime" name="billed-type" type="radio" value="lifetime">
                            <label for="radio-lifetime"><span class="radio-label"></span> {{ __('quickad.lifetime') }}</label>
                        </div>
                        @endif
                    </div>
                    <!-- Pricing Plans Container -->
                    <div class="pricing-plans-container">
                        @foreach($sub_types ?? [] as $sub_types)
                            <!-- Plan -->
                            <div class='pricing-plan @if((data_get($sub_types ?? [], 'recommended', ''))=="yes") recommended @endif'>
                                @if((data_get($sub_types ?? [], 'recommended', ''))=="yes") <div class="recommended-badge">{{ __('quickad.recommended') }}</div> @endif
                                <h3>{{ data_get($sub_types ?? [], 'title', '') }}</h3>
                                @if((data_get($sub_types ?? [], 'id', ''))=="free" || (data_get($sub_types ?? [], 'id', ''))=="trial")
                                <div class="pricing-plan-label"><strong>
                                        @if((data_get($sub_types ?? [], 'id', ''))=="free")
                                        {{ __('quickad.free') }}
                                        {{ $else ?? '' }}
                                        {{ __('quickad.trial') }}
                                        @endif
                                    </strong></div>
                                {{ $else ?? '' }}
                                @if(($total_monthly ?? "")!="0")
                                <div class="pricing-plan-label billed-monthly-label"><strong>{{ data_get($sub_types ?? [], 'monthly_price', '') }}</strong>/ {{ __('quickad.monthly') }}</div>
                                @endif
                                @if(($total_annual ?? "")!="0")
                                <div class="pricing-plan-label billed-yearly-label"><strong>{{ data_get($sub_types ?? [], 'annual_price', '') }}</strong>/ {{ __('quickad.yearly') }}</div>
                                @endif
                                @if(($total_lifetime ?? "")!="0")
                                <div class="pricing-plan-label billed-lifetime-label"><strong>{{ data_get($sub_types ?? [], 'lifetime_price', '') }}</strong> {{ __('quickad.lifetime') }}</div>
                                @endif
                                @endif
                                <div class="pricing-plan-features">
                                    <strong>{{ __('quickad.features_of') }} {{ data_get($sub_types ?? [], 'title', '') }}</strong>
                                    <ul>
                                        <li>{{ data_get($sub_types ?? [], 'limit', '') }} {{ __('quickad.ad_post_limit') }}</li>
                                        <li>{{ data_get($sub_types ?? [], 'duration', '') }} {{ __('quickad.days') }} {{ __('quickad.ad_exp_in') }}</li>
                                        <li>{{ __('quickad.featured_fee') }} {{ $currency_sign ?? '' }}{{ data_get($sub_types ?? [], 'featured_fee', '') }} {{ __('quickad.for') }} {{ data_get($sub_types ?? [], 'featured_duration', '') }} {{ __('quickad.days') }}</li>
                                        <li>
                                            {{ __('quickad.urgent_fee') }} {{ $currency_sign ?? '' }}{{ data_get($sub_types ?? [], 'urgent_fee', '') }} {{ __('quickad.for') }} {{ data_get($sub_types ?? [], 'urgent_duration', '') }} {{ __('quickad.days') }}
                                        </li>
                                        <li>
                                            {{ __('quickad.highlight_fee') }} {{ $currency_sign ?? '' }}{{ data_get($sub_types ?? [], 'highlight_fee', '') }} {{ __('quickad.for') }} {{ data_get($sub_types ?? [], 'highlight_duration', '') }} {{ __('quickad.days') }}
                                        </li>
                                        <li>
                                            @if((data_get($sub_types ?? [], 'top_search_result', ''))=="yes")
                                            <span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span>
                                            {{ $else ?? '' }}
                                            <span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span>
                                            @endif
                                            {{ __('quickad.top_search_result') }}
                                        </li>
                                        <li>
                                            @if((data_get($sub_types ?? [], 'show_on_home', ''))=="yes")
                                            <span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span>
                                            {{ $else ?? '' }}
                                            <span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span>
                                            @endif
                                            {{ __('quickad.show_on_home') }}
                                        </li>
                                        <li>
                                            @if((data_get($sub_types ?? [], 'show_in_home_search', ''))=="yes")
                                            <span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span>
                                            {{ $else ?? '' }}
                                            <span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span>
                                            @endif
                                            {{ __('quickad.show_in_home_search') }}
                                        </li>
                                        {{ data_get($sub_types ?? [], 'custom_settings', '') }}
                                    </ul>
                                </div>
                                @if((data_get($sub_types ?? [], 'Selected', ''))=="0")
                                <button type="submit" class="button full-width margin-top-20 ripple-effect" name="upgrade" value="{{ data_get($sub_types ?? [], 'id', '') }}">{{ __('quickad.upgrade') }}</button>
                                @endif
                                @if((data_get($sub_types ?? [], 'Selected', ''))=="1")
                                <a href="javascript:void(0);" class="button full-width margin-top-20 ripple-effect">
                                    {{ __('quickad.current_plan') }}
                                </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Membership Plans / End-->
@endif

@if(($blog_enable ?? "") && ($show_blog_home ?? ""))
<div class="section padding-top-55 padding-bottom-65">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="section-headline centered margin-top-0 margin-bottom-45">
                    <h3>{{ __('quickad.recent_blog') }}</h3>
                </div>
                <div class="listings-container grid-layout grid-layout-3">
                    @foreach($recent_blog ?? [] as $recent_blog)
                        <div class="job-listing blog-listing">
                            <div class="job-listing-details">
                                @if(($blog_banner ?? ""))
                                <div class="job-listing-company-logo">
                                    <a href="{{ data_get($recent_blog ?? [], 'link', '') }}">
                                        <img class="lazy-load" src="{{ $site_url ?? '' }}storage/blog/{{ data_get($recent_blog ?? [], 'image', '') }}" alt="{{ data_get($recent_blog ?? [], 'title', '') }}">
                                    </a>
                                </div>
                                @endif
                                <div class="job-listing-description">
                                    <div class="blog-cat">{{ data_get($recent_blog ?? [], 'categories', '') }}</div>
                                    <h3 class="job-listing-title"><a href="{{ data_get($recent_blog ?? [], 'link', '') }}">{{ data_get($recent_blog ?? [], 'title', '') }}</a>
                                    </h3>

                                    <p class="job-listing-text margin-top-10">{{ data_get($recent_blog ?? [], 'description', '') }}</p>
                                </div>
                            </div>
                            <div class="job-listing-footer">
                                <ul>
                                    <li>
                                        <img src="{{ $site_url ?? '' }}storage/profile/{{ data_get($recent_blog ?? [], 'author_pic', '') }}" class="author-avatar"> {{ __('quickad.by') }}
                                        <a href="{{ data_get($recent_blog ?? [], 'author_link', '') }}">{{ data_get($recent_blog ?? [], 'author', '') }}</a>
                                    </li>
                                    <li><i class="la la-clock-o"></i> {{ data_get($recent_blog ?? [], 'created_at', '') }}</li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(($bottom_adstatus ?? "")=="1")
                <div class="quickad-section text-center margin-top-15" id="quickad-top">{{ $bottom_adscode ?? '' }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif


<script>
    var loginurl = "{{ $link['LOGIN'] ?? '#' }}?ref=index.php";

    (function($) {
        var $window = $(window),
            $html = $('.compact-list-layout');

        $window.resize(function resize(){
            if ($window.width() < 768) {
                return $html.addClass('grid-layout');
            }

            $html.removeClass('grid-layout');
        }).trigger('resize');
    })(jQuery);
</script>

<!-- world-gmap -->
<link href="{{ $site_url ?? '' }}includes/assets/plugins/map/google/map-marker.css" type="text/css" rel="stylesheet">
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/jquery-migrate-1.2.1.min.js'></script>
<script type='text/javascript' src='//maps.google.com/maps/api/js?key={{ $gmap_api_key ?? '' }}&#038;libraries=places%2Cgeometry&#038;ver=2.2.1'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/richmarker-compiled.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/markerclusterer_packed.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/gmapAdBox.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/maps.js'></script>
<script>
    $('#address-autocomplete').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    var _latitude = {{ $latitude ?? '' }};
    var _longitude = {{ $longitude ?? '' }};
    var element = "road_map";
    var site_url = '{{ $site_url ?? '' }}';
    var path = '{{ $site_url ?? '' }}';
    var color = '{{ $map_color ?? '' }}';
    var optimizedDatabaseLoading = 0;
    var markerTarget = "gmapAdBox";
    var sidebarResultTarget = "sidebar";
    var showMarkerLabels = true;
    var mapDefaultZoom = {{ $zoom ?? '' }};
    var getCity = true;
    var Countries = '{{ $specific_country ?? '' }}';
    if (Countries != "") {
        var getCountry = Countries;
    }
    else {
        var getCountry = "all";
    }

    heroMap(_latitude, _longitude, element, markerTarget, sidebarResultTarget, showMarkerLabels, mapDefaultZoom);
    loginurl = "{{ $link['LOGIN'] ?? '#' }}?ref=index.php";
</script>
@include('partials.footer')
