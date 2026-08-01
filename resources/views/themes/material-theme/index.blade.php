@include('partials.header')

<div id="page-content" style="transform: translateY(0px);">
    <div class="quickad-section has-background height-600px">
        <div class="wrapper">
            <div class="inner">
                <div class="container">
                    <div class="page-title center">
                        <h1 class="title">{{ __('quickad.home_banner_heading') }}</h1>
                        <h2>{{ __('quickad.home_banner_tagline') }}</h2>
                    </div>

                    <!--end page-title-->
                    <div class="row">
                        <div class="">
                            <div class="container">
                                <div class="pad-50-lr">
                                    <form class="" id="withimg" method="get" action="{{ $link['LISTING'] ?? '#' }}">
                                        <div class="row">
                                            <div class="col-md-5 col-sm-5">
                                                <div class="input-field">
                                                    <input type="text" name="keywords" placeholder="{{ __('quickad.what') }} ?" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-3">
                                                <div class="form-group input-field tg-inputwithicon live-location-search" id="country-popup">
                                                    <div data-option="{{ $auto_detect_location ?? '' }}" class="loc-tracking"><i class="fa fa-crosshairs"></i></div>
                                                    <i class="fa fa-close hidden" id="clear-city" style="display: none"></i>
                                                    <input type="text" id="inputStateCity" name="location" placeholder="{{ __('quickad.where') }} ?" autocomplete="off">

                                                    <div id="searchDisplay"></div>
                                                    <input type="hidden" name="latitude" id="latitude" value="">
                                                    <input type="hidden" name="longitude" id="longitude" value="">
                                                    <input type="hidden" name="placetype" id="searchPlaceType" value="">
                                                    <input type="hidden" name="placeid" id="searchPlaceId" value="">
                                                </div>
                                            </div>
                                            <!--end col-md-4-->
                                            <div class="col-md-3 col-sm-3">
                                                <div  class="input-field">
                                                    <select name="cat" class="meterialselect">
                                                        <option value="">{{ __('quickad.all_categories') }}</option>
                                                        @foreach($category ?? [] as $category)
                                                        <option value="{{ data_get($category ?? [], 'id', '') }}" {{ data_get($category ?? [], 'selected', '') }}>{{ data_get($category ?? [], 'name', '') }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                            </div>
                                            <!--end col-md-4-->
                                            <div class="col-md-1 col-sm-1">
                                                <div class="input-field">
                                                    <button type="submit" name="Submit" class="btn btn-defauilt btn-rounded "><i class="fa fa-search"></i> {{ __('quickad.search') }}</button>
                                                </div>
                                                <!--end form-group-->
                                            </div>
                                            <!--end col-md-4-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                </div>
                                <!--end form-hero-->
                            </div>
                            <!--end container-->
                        </div>
                        <!--end search-form-->
                    </div>
                    <!--end row-->
                </div>
                <!--end container-->
            </div>
            <!--end inner-->
        </div>
        <!--end wrapper-->
        <div class="background-wrapper">
            <div class="bg-transfer opacity-30"><img src="{{ $site_url ?? '' }}storage/banner/{{ $banner_image ?? '' }}" alt=""></div>
            <div class="background-color background-color-black"></div>
        </div>

        <!--end background-wrapper-->
    </div>


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
                                <h3 title="{{ data_get($item ?? [], 'product_name', '') }}"><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
                                    @if((data_get($item ?? [], 'sub_image', ''))!="")
                                    <img src="{{ data_get($item ?? [], 'sub_image', '') }}" alt="{{ data_get($item ?? [], 'sub_title', '') }}" title="{{ data_get($item ?? [], 'sub_title', '') }}" style="width: 24px;display: inline-block;"/>
                                    @endif
                                </h3>
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
                                <h3 title="{{ data_get($item2 ?? [], 'product_name', '') }}"><a href="{{ data_get($item2 ?? [], 'link', '') }}">{{ data_get($item2 ?? [], 'product_name', '') }}</a>
                                    @if((data_get($item2 ?? [], 'sub_image', ''))!="")
                                    <img src="{{ data_get($item2 ?? [], 'sub_image', '') }}" alt="{{ data_get($item2 ?? [], 'sub_title', '') }}" title="{{ data_get($item2 ?? [], 'sub_title', '') }}" style="width: 24px;display: inline-block;"/>
                                    @endif
                                </h3>
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


@include('partials.footer')