@include('partials.header')

<div id="page-content" style="transform: translateY(0px);">


    <div class="quickad-section height-600px has-map">
        <div class="map-wrapper">
            <div class="map" id="map-submit"></div>
        </div>
        <!--end map-wrapper-->

        <div class="search-expandable">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="search-inner-wrap">
                            <div class="search-expand-btn btn-primary waves-effect waves-light">{{ __('quickad.advance_search') }}</div>
                            <div class="advanced-search advanced-search-hidden">
                                <form class="" method="get" action="#">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-2">
                                            <div class="input-field">
                                                <input type="text" name="serachStr" placeholder="{{ __('quickad.what') }} ?">
                                            </div>
                                            <!--end form-group-->
                                        </div>
                                        <!--end col-md-4-->
                                        <div class="col-md-4 col-sm-2">
                                            <div class="input-field">
                                                <input type="text" id="address-autocomplete" name="location" placeholder="{{ __('quickad.where') }} ?">
                                                <input type="hidden" id="latitude" name="latitude" />
                                                <input type="hidden" id="longitude" name="longitude" />
                                                <input type="hidden" id="locality" name="locality" disabled="true" value=""/>
                                                <input type="hidden" id="administrative_area_level_2" name="city" placeholder="city" value="">
                                                <input type="hidden" id="administrative_area_level_1" name="state" placeholder="state" value="">
                                                <input type="hidden" id="country-input" name="country" placeholder="country" value="">
                                            </div>
                                            <!--end form-group-->
                                        </div>
                                        <!--end col-md-4-->
                                        <div class="col-md-3 col-sm-4">
                                            <div class="input-field">
                                                <select name="searchBox" class="meterialselect" required>
                                                    <option value="">{{ __('quickad.all_categories') }}</option>
                                                    @foreach($category ?? [] as $category)
                                                    <option value="{{ data_get($category ?? [], 'id', '') }}" {{ data_get($category ?? [], 'selected', '') }}>{{ data_get($category ?? [], 'name', '') }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!--end form-group-->
                                        </div>
                                        <!--end col-md-4-->
                                        <div class="col-md-1 col-sm-4">
                                            <div class="input-field">
                                                <button data-ajax-response='map' type="submit" name="searchform" class="btn btn-primary pull-right darker waves-effect waves-light"><i class="fa fa-search"></i> {{ __('quickad.search') }}</button>
                                            </div>
                                            <!--end form-group-->
                                        </div>
                                        <!--end col-md-4-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--end quickad-section-->



    <section class="block">
        <div class="container">
            <div class="section-title">
                <div class="center">
                    <h2>{{ __('quickad.browse_listing') }}</h2>
                </div>
            </div>
            <!--end section-title-->
            <div class="categories-list">
                <div class="row">
<!--Category display dynamically -->
                    @foreach($cat ?? [] as $cat)
                    <div class="col-md-3 col-sm-3">
                        <div class="list-item min-height-150">
                            <div class="title">
                                <div class="icon"><i class="{{ data_get($cat ?? [], 'icon', '') }}"></i></div>
                                <h3><a href="{{ data_get($cat ?? [], 'catlink', '') }}">{{ data_get($cat ?? [], 'main_title', '') }}</a></h3>
                            </div>
                            <div class="tse-scrollable catListing">
                                <div class="tse-content">
                                    <ul>{{ data_get($cat ?? [], 'sub_title', '') }}</ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
<!--Category display dynamically-->
                </div>
                <!--end row-->
            </div>
            <!--end categories-list-->
        </div>
        <!--end container-->
    </section>
    <!--end block-->
    @if(($post_premium_listing ?? "")=="0")
    <style>
        #premium_crousel{ display: none !important;}
    </style>
    @endif
    <section class="block background-is-dark" id="premium_crousel">
        <div class="container">
            <div class="section-title vertical-aligned-elements">
                <div class="element text-align-right">
                    <h2 class="invisible-on-mobile pull-left featured-ads-label">{{ __('quickad.premium_ads') }}</h2>
                    <div id="premium-nav" class="gallery-nav"></div>
                </div>
            </div>
            <!--end section-title-->
        </div>
        <div class="gallery featured container">
            <div class="owl-carousel" data-owl-items="4" data-owl-loop="1" data-owl-auto-width="1" data-owl-nav="1" data-owl-dots="1" data-owl-nav-container="#premium-nav">
                @foreach($item ?? [] as $item)
                <div class="ribbon-pad">
                    <div class="item mar-left-zero" data-id="1">
                        <div class="premium">
                            @if((data_get($item ?? [], 'featured', ''))=="1") <span class="listing-box-premium featured">{{ __('quickad.featured') }}</span> @endif
                            @if((data_get($item ?? [], 'urgent', ''))=="1") <span class="listing-box-premium urgent">{{ __('quickad.urgent') }}</span> @endif
                            @if((data_get($item ?? [], 'highlight', ''))=="1") <span class="listing-box-premium highlight">{{ __('quickad.highlight') }}</span> @endif

                        </div>
                        <div class="ad-listing">
                            <div class="description">
                                <div class="label label-default"><a href="{{ data_get($item ?? [], 'catlink', '') }}">{{ data_get($item ?? [], 'category', '') }}</a></div>
                                <h3 title="{{ data_get($item ?? [], 'product_name', '') }}"><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a></h3>
                                <h4>{{ data_get($item ?? [], 'location', '') }}</h4>
                            </div>
                            <!--end description-->
                            <div class="image bg-transfer"><img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}"></div>
                            <!--end image-->
                        </div>
                        <div class="additional-info {{ data_get($item ?? [], 'highlight_bg', '') }}">
                            <ul class="icondetail">
                                <li><i class="fa fa-th-list"></i> {{ __('quickad.sub_category') }} :
                                    <a title="{{ data_get($item ?? [], 'sub_category', '') }}" href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a>
                                </li>
                                <li><i class="fa fa-map-marker"></i> {{ __('quickad.location') }} : {{ data_get($item ?? [], 'location', '') }}</li>
                                <li><i class="fa fa-calendar"></i> {{ __('quickad.posted_on') }} : {{ data_get($item ?? [], 'created_at', '') }} </li>
                                <li><i class="fa fa-user"></i> {{ __('quickad.posted_by') }} : <a href="{{ data_get($item ?? [], 'author_link', '') }}" target="_blank">{{ data_get($item ?? [], 'username', '') }}</a></li>
                            </ul>

                            <!--end controls-more-->
                        </div>
                        <!--end additional-info-->
                    </div>
                    <!--end item-->
                </div>
                @endforeach

            </div>
        </div>
        <!--end gallery-->
        <div class="background-wrapper">
            <div class="background-color background-color-default">

            </div>
        </div>
        <!--end background-wrapper-->
    </section>
    <!--end block-->


    <section class="block background-is-dark" style="background: #8e8e89;">
        <div class="container">
            <div class="section-title vertical-aligned-elements">
                <div class="element text-align-right">
                    <h2 class="invisible-on-mobile pull-left featured-ads-label">{{ __('quickad.latest_ads') }}</h2>
                    <div id="latest-nav" class="gallery-nav"></div>
                </div>
            </div>
            <!--end section-title-->
        </div>
        <div class="gallery featured container">
            <div class="owl-carousel" data-owl-items="4" data-owl-loop="1" data-owl-auto-width="1" data-owl-nav="1" data-owl-dots="1" data-owl-nav-container="#latest-nav">
                @foreach($item2 ?? [] as $item2)
                <div class="ribbon-pad">
                    <div class="item mar-left-zero" data-id="1">
                        <div class="premium">
                            @if((data_get($item2 ?? [], 'featured', ''))=="1") <span class="listing-box-premium featured">{{ __('quickad.featured') }}</span> @endif
                            @if((data_get($item2 ?? [], 'urgent', ''))=="1") <span class="listing-box-premium urgent">{{ __('quickad.urgent') }}</span> @endif
                            @if((data_get($item2 ?? [], 'highlight', ''))=="1") <span class="listing-box-premium highlight">{{ __('quickad.highlight') }}</span> @endif

                        </div>
                        <div class="ad-listing">
                            <div class="description">
                                <div class="label label-default"><a href="{{ data_get($item2 ?? [], 'catlink', '') }}">{{ data_get($item2 ?? [], 'category', '') }}</a></div>
                                <h3 title="{{ data_get($item2 ?? [], 'product_name', '') }}"><a href="{{ data_get($item2 ?? [], 'link', '') }}">{{ data_get($item2 ?? [], 'product_name', '') }}</a></h3>
                                <h4>{{ data_get($item2 ?? [], 'location', '') }}</h4>
                            </div>
                            <!--end description-->
                            <div class="image bg-transfer"><img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item2 ?? [], 'picture', '') }}"></div>
                            <!--end image-->
                        </div>
                        <div class="additional-info {{ data_get($item2 ?? [], 'highlight_bg', '') }}">
                            <ul class="icondetail">
                                <li><i class="fa fa-th-list"></i> {{ __('quickad.sub_category') }} :
                                    <a title="{{ data_get($item2 ?? [], 'sub_category', '') }}" href="{{ data_get($item2 ?? [], 'subcatlink', '') }}">{{ data_get($item2 ?? [], 'sub_category', '') }}</a>
                                </li>
                                <li><i class="fa fa-map-marker"></i> {{ __('quickad.location') }} : {{ data_get($item2 ?? [], 'location', '') }}</li>
                                <li><i class="fa fa-calendar"></i> {{ __('quickad.posted_on') }} : {{ data_get($item2 ?? [], 'created_at', '') }} </li>
                                <li><i class="fa fa-user"></i> {{ __('quickad.posted_by') }} : <a href="{{ data_get($item2 ?? [], 'author_link', '') }}" target="_blank">{{ data_get($item2 ?? [], 'username', '') }}</a></li>
                            </ul>

                            <!--end controls-more-->
                        </div>
                        <!--end additional-info-->
                    </div>
                    <!--end item-->
                </div>
                @endforeach

            </div>
        </div>
        <!--end gallery-->
        <div class="background-wrapper">
            <div class="background-color background-color-default">

            </div>
        </div>
        <!--end background-wrapper-->
    </section>
    <!--end block-->

</div>

<link href="{{ $site_url ?? '' }}includes/assets/plugins/map/google/map-marker.css" type="text/css" rel="stylesheet">
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/jquery-migrate-1.2.1.min.js'></script>
<script type='text/javascript' src='//maps.google.com/maps/api/js?key={{ $gmap_api_key ?? '' }}&#038;libraries=places%2Cgeometry&#038;ver=2.2.1'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/richmarker-compiled.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/markerclusterer_packed.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/gmapAdBox.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/maps.js'></script>

<script>
    $('#address-autocomplete').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    var _latitude = {{ $latitude ?? '' }};
    var _longitude = {{ $longitude ?? '' }};
    var element = "map-submit";
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
    if(Countries != ""){
        var str = Countries;
        var str_array = str.split(',');
        var getCountry = [];
        for(var i = 0; i < str_array.length; i++) {
            getCountry.push(str_array[i]);
        }
    }
    else{
        var getCountry = "all";
    }
    heroMap(_latitude,_longitude, element, markerTarget, sidebarResultTarget, showMarkerLabels, mapDefaultZoom);


</script>

@include('partials.footer')