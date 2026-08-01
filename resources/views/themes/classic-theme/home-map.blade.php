@include('partials.header')
<!-- world-gmap -->
<section id="main" class="clearfix home-two">
    <!-- gmap -->
    <div id="road_map" class="map"></div>
    <div class="container">
        <div class="row">
            <!-- banner -->
            <div class="col-sm-12">
                <div class="banner">
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
            <!-- banner -->
        </div>
        <!-- row -->
        <div class="section services">
            <style>
                .single-service figure {
                    display: none;
                }
            </style>
            @foreach($cat ?? [] as $cat)
            <!-- single-service -->
            <div class="single-service">
                @if((data_get($cat ?? [], 'picture', ''))=="")
                <div class="services-icon"><i class="{{ data_get($cat ?? [], 'icon', '') }}"></i></div>
                @endif
                @if((data_get($cat ?? [], 'picture', ''))!="")
                <div class="services-icon"><img src="{{ data_get($cat ?? [], 'picture', '') }}"/></div>
                @endif
                <h5><a href="{{ data_get($cat ?? [], 'catlink', '') }}">{{ data_get($cat ?? [], 'main_title', '') }}</a></h5>
                <ul>
                    {{ data_get($cat ?? [], 'sub_title', '') }}
                </ul>
            </div>
            <!-- single-service -->
            @endforeach
        </div>
        <!-- services -->
        <!-- quickad-section-Mobile -->
        <div class="quickad-section" id="quickad-top">{{ $top_adscode ?? '' }}</div>
        <!-- quickad-section-Mobile -->

        <!-- featured-slide -->
        <div class='section recommended-ads @if(($post_premium_listing ?? "")=="0") hidden @endif'>
            <div class="row">
                <div class="col-sm-12">
                    <div class="featured-top">
                        <h4>{{ __('quickad.premium_ads') }}</h4>
                    </div>
                </div>
            </div>
            <!-- featured-slider -->
            <div class="recommended-slider" id="serchlist">
                <div id="recommended-slider-id">
                    @foreach($item ?? [] as $item)
                    <div class="quick-item @if(" {{ data_get($item ?? [], 'highlight', '') }}"=="1") highlight @endif">
                    <!-- item-image -->
                    <div class="item-image-box">
                        <div class="item-image"><a href="{{ data_get($item ?? [], 'link', '') }}"><img
                                src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="Image"
                                class="img-responsive"></a>

                            <div class="item-badges">
                                @if((data_get($item ?? [], 'featured', ''))=="1") <span class="featured">{{ __('quickad.featured') }}</span>@endif
                                @if((data_get($item ?? [], 'urgent', ''))=="1") <span>{{ __('quickad.urgent') }}</span>@endif
                            </div>
                        </div>
                        <!-- item-image -->
                    </div>
                    <div class="item-info {{ data_get($item ?? [], 'highlight_bg', '') }}">
                        <!-- ad-info -->
                        <div class="ad-info">
                            <h4 class="item-title"><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a></h4>
                            <ol class="breadcrumb">
                                <li><a href="{{ data_get($item ?? [], 'catlink', '') }}">{{ data_get($item ?? [], 'category', '') }}</a></li>
                                <li class="hidden"><a title="{{ data_get($item ?? [], 'sub_category', '') }}" href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a>
                                </li>
                            </ol>
                            <ul class="item-details">
                                <li><i class="fa fa-map-marker"></i><a href="{{ data_get($item ?? [], 'citylink', '') }}">{{ data_get($item ?? [], 'location', '') }}</a></li>
                                <li><i class="fa fa-clock-o"></i>{{ data_get($item ?? [], 'created_at', '') }}</li>
                            </ul>
                            <div class="ad-meta">
                                @if((data_get($item ?? [], 'price', ''))!="0") <span class="item-price"> {{ data_get($item ?? [], 'price', '') }} </span> @endif
                                <ul class="contact-options pull-right" id="set-favorite">
                                    <li><a href="#" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}"
                                           data-action="setFavAd" class="fav_{{ data_get($item ?? [], 'id', '') }} fa fa-heart @if((data_get($item ?? [], 'favorite', ''))=="1") active @endif"></a></li>
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
            <!-- featured-slider -->
        </div>
        <!-- #featured-slider -->
    </div>
    <!-- featured-slide -->

    <!-- recent-slide -->
    <div class="section recommended-ads">
        <div class="row">
            <div class="col-sm-12">
                <div class="featured-top">
                    <h4>{{ __('quickad.latest_ads') }}</h4>
                </div>
            </div>
        </div>
        <!-- recent-slider -->
        <div class="recommended-slider" id="serchlist">
            <div id="recent-slider-id">
                @foreach($item2 ?? [] as $item2)
                <div class="quick-item @if(" {{ data_get($item2 ?? [], 'highlight', '') }}"=="1") highlight @endif">
                <!-- item-image -->
                <div class="item-image-box">
                    <div class="item-image"><a href="{{ data_get($item2 ?? [], 'link', '') }}"><img
                            src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item2 ?? [], 'picture', '') }}" alt="Image"
                            class="img-responsive"></a>

                        <div class="item-badges">
                            @if((data_get($item2 ?? [], 'featured', ''))=="1") <span class="featured">{{ __('quickad.featured') }}</span>@endif
                            @if((data_get($item2 ?? [], 'urgent', ''))=="1") <span>{{ __('quickad.urgent') }}</span>@endif
                        </div>
                    </div>
                    <!-- item-image -->
                </div>
                <div class="item-info {{ data_get($item2 ?? [], 'highlight_bg', '') }}">
                    <!-- ad-info -->
                    <div class="ad-info">
                        <h4 class="item-title"><a href="{{ data_get($item2 ?? [], 'link', '') }}">{{ data_get($item2 ?? [], 'product_name', '') }}</a></h4>
                        <ol class="breadcrumb">
                            <li><a href="{{ data_get($item2 ?? [], 'catlink', '') }}">{{ data_get($item2 ?? [], 'category', '') }}</a></li>
                            <li class="hidden"><a title="{{ data_get($item2 ?? [], 'sub_category', '') }}" href="{{ data_get($item2 ?? [], 'subcatlink', '') }}">{{ data_get($item2 ?? [], 'sub_category', '') }}</a>
                            </li>
                        </ol>
                        <ul class="item-details">
                            <li><i class="fa fa-map-marker"></i><a href="{{ data_get($item2 ?? [], 'citylink', '') }}">{{ data_get($item2 ?? [], 'location', '') }}</a></li>
                            <li><i class="fa fa-clock-o"></i>{{ data_get($item2 ?? [], 'created_at', '') }}</li>
                        </ul>
                        <div class="ad-meta">
                            @if((data_get($item2 ?? [], 'price', ''))!="0") <span class="item-price"> {{ data_get($item2 ?? [], 'price', '') }} </span> @endif

                            <ul class="contact-options pull-right" id="set-favorite">
                                <li><a href="#" data-item-id="{{ data_get($item2 ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd"
                                       class="fav_{{ data_get($item2 ?? [], 'id', '') }} fa fa-heart @if((data_get($item2 ?? [], 'favorite', ''))=="1") active @endif"></a></li>
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
        <!-- recent-slider -->
    </div>
    <!-- #recent-slider -->
    </div>
    <!-- recent-slide -->

    <div class="quickad-section" id="quickad-bottom">{{ $bottom_adscode ?? '' }}</div>
    </div>
    <!-- container -->
</section>
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
    var path = '{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/';
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