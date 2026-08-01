@include('partials.header')
<!-- home-one-info -->
<section class="clearfix home-one">
    <!-- world -->
    <div id="banner-two" style="background-image:url({{ $site_url ?? '' }}storage/banner/{{ $banner_image ?? '' }});background-size: cover;">
        <div class="overlay"></div>
        <div class="d-flex align-items-center h-100">
            <div class="container">
                <div class="row text-center">

                    <div class="col-sm-12 ">
                        <div class="banner">

                            <h1 class="title">{{ __('quickad.home_banner_heading') }}</h1>
                            <h3>{{ __('quickad.home_banner_tagline') }}</h3>

                            <!-- banner-form -->
                            <form autocomplete="off" class="form-inline" method="get" action="{{ $link['LISTING'] ?? '#' }}" accept-charset="UTF-8" style="display:block">
                                <div class="search-banner-wrapper">
                                    <div class="search-banner row justify-content-center no-gutters">

                                        <div class="col-md-6">
                                            <div class="form-group bg-white d-flex align-items-center px-3 mb-3 mb-lg-0 border-right">
                                                <label for="textwords" class="font-weight-bold">{{ __('quickad.what') }} </label>
                                                <input autocomplete="off" type="text" class="form-control border-0 qucikad-ajaxsearch-input" placeholder="{{ __('quickad.what_look_for') }}" data-prev-value="0" data-noresult="{{ __('quickad.more_results_for') }}">
                                                <i class="qucikad-ajaxsearch-close fa fa-times-circle" aria-hidden="true" style="display: none;"></i>
                                                <div id="qucikad-ajaxsearch-dropdown" size="0" tabindex="0" style="display: none; overflow-y: hidden; outline: none; cursor: -webkit-grab;">
                                                    <ul>
                                                        @foreach($category ?? [] as $category)
                                                        <li class="qucikad-ajaxsearch-li-cats" data-catid="{{ data_get($category ?? [], 'slug', '') }}">
                                                            @if((data_get($category ?? [], 'picture', ''))=="")
                                                            <i class="qucikad-as-caticon {{ data_get($category ?? [], 'icon', '') }}"></i>
                                                            @endif
                                                            @if((data_get($category ?? [], 'picture', ''))!="")
                                                            <img src="{{ data_get($category ?? [], 'picture', '') }}"/>
                                                            @endif
                                                            <span class="qucikad-as-cat">{{ data_get($category ?? [], 'name', '') }}</span>
                                                        </li>
                                                        @endforeach
                                                    </ul>

                                                    <div style="display:none" id="def-cats">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group pl-3 live-location-search">
                                                <label for="city" class="font-weight-bold">{{ __('quickad.where') }} </label>
                                                <div data-option="{{ $auto_detect_location ?? '' }}" class="loc-tracking"><i class="fa fa-crosshairs"></i></div>
                                                <input type="text" class="form-control border-0" id="searchStateCity" name="location" placeholder="{{ __('quickad.your_city') }}">

                                                <input type="hidden" name="latitude" id="latitude" value="">
                                                <input type="hidden" name="longitude" id="longitude" value="">
                                                <input type="hidden" name="placetype" id="searchPlaceType" value="">
                                                <input type="hidden" name="placeid" id="searchPlaceId" value="">
                                                <input type="hidden" id="input-keywords" name="keywords" value="">
                                                <input type="hidden" id="input-maincat" name="cat" value=""/>
                                                <input type="hidden" id="input-subcat" name="subcat" value=""/>
                                                <button data-ajax-response='map' type="submit" name="searchform" class="btn btn-primary ml-auto">
                                                    <i class="fa fa-search"></i>
                                                    <span class="align-middle ml-2 dn-text-sm">{{ __('quickad.search') }}</span>
                                                </button>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </form>
                            <!-- banner-form -->


                        </div>
                    </div>
                    <!-- banner -->
                </div>
            </div>
        </div>
    </div>
</section>
<section class="clearfix">
    <!-- world -->
    <div class="container">
        <!-- main-content -->
        <div class="main-content" id="serchlist">
            <!-- row -->
            <div class="row">
                {{ $ad_home_page_below_search_section ?? '' }}
                <!-- product-list -->
                <div>
                    <!-- categorys -->
                    <div class="section category-quickad text-center">
                        <ul class="category-list">
                            @foreach($cat ?? [] as $cat)
                            <li class="category-item"><a href="{{ data_get($cat ?? [], 'catlink', '') }}">
                                    @if((data_get($cat ?? [], 'picture', ''))=="")
                                    <div class="category-icon"><i class="{{ data_get($cat ?? [], 'icon', '') }}"></i></div>
                                    @endif
                                    @if((data_get($cat ?? [], 'picture', ''))!="")
                                    <div class="category-icon">
                                        <img class="lazy-load" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ data_get($cat ?? [], 'picture', '') }}" alt="{{ data_get($cat ?? [], 'main_title', '') }}">
                                    </div>
                                    @endif

                                <span class="category-title">{{ data_get($cat ?? [], 'main_title', '') }}</span></a>
                            </li>
                            <!-- category-item -->
                            @endforeach
                        </ul>
                    </div>
					<!-- category-ad -->
                    <!-- quickad-section -->
                    {{ $ad_home_page_below_category_section ?? '' }}
                    <!-- quickad-section -->
                    <!-- featured-slide -->
                      <!-- featured-slide -->
                    <div class='section featured-slide @if(($post_premium_listing ?? "")=="0") hidden @endif'>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="section-title featured-top">
                                    <h4>{{ __('quickad.premium_ads') }}</h4>
                                </div>
                            </div>
                        </div>
                        <!-- featured-slider -->
                        <div class="featured-slider">
                            <div id="featured-slider" >
                                @foreach($item ?? [] as $item)
                                <div class="quick-item">
                                <!-- item-image -->
                                <div class="item-image-box">
                                    <div class="item-image"><a href="{{ data_get($item ?? [], 'link', '') }}"><img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}" class="img-responsive"></a>

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
                                        <h4 class="item-title">
                                            @if((data_get($item ?? [], 'sub_image', ''))!="")
                                            <img src="{{ data_get($item ?? [], 'sub_image', '') }}" width="24px" alt="{{ data_get($item ?? [], 'sub_title', '') }}" title="{{ data_get($item ?? [], 'sub_title', '') }}" style="display: inline-block;width: 24px"/>
                                            @endif
                                            <a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>

                                        </h4>
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
                        <!-- featured-slider -->
                    </div>
                        <!-- #featured-slider -->
                    </div>
                    <!-- featured-slide -->
                    {{ $ad_home_page_below_featured_section ?? '' }}
                    <!-- recent-slide -->
                     <div class="section recommended-ads">
                        <div class="row">
                           <div class="col-sm-12">  
                                <div class="featured-top"> 
                                    <h4>{{ __('quickad.latest_ads') }}</h4>
                                </div>
                            </div>
                        
                            <!-- recent-slider -->
                            @foreach($item2 ?? [] as $item2)

                            <div class="col-md-4 col-xs-6" style="margin-bottom: 30px;">

                                <div class="quick-item">
                                    <!-- item-image -->
                                    <div class="item-image-box">
                                        <div class="item-image" style="height:200px">
                                            <a href="{{ data_get($item2 ?? [], 'link', '') }}">
                                                <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item2 ?? [], 'picture', '') }}" alt="{{ data_get($item2 ?? [], 'product_name', '') }}" class="img-responsive">
                                            </a>
                                            <div class="item-badges">
                                                @if((data_get($item2 ?? [], 'featured', ''))=="1") <span class="featured">{{ __('quickad.featured') }}</span>@endif
                                                @if((data_get($item2 ?? [], 'urgent', ''))=="1") <span>{{ __('quickad.urgent') }}</span> @endif
                                            </div>
                                        </div>
                                        <!-- item-image -->
                                    </div>
                                    <div class="item-info {{ data_get($item2 ?? [], 'highlight_bg', '') }}">
                                        <!-- ad-info -->
                                        <div class="ad-info">
                                            <h4 class="item-title">
                                                @if((data_get($item2 ?? [], 'sub_image', ''))!="")
                                                <img src="{{ data_get($item2 ?? [], 'sub_image', '') }}" width="16px" alt="{{ data_get($item2 ?? [], 'sub_title', '') }}" title="{{ data_get($item2 ?? [], 'sub_title', '') }}" style="display: inline-block;width: 16px"/>
                                                @endif
                                                <a href="{{ data_get($item2 ?? [], 'link', '') }}">{{ data_get($item2 ?? [], 'product_name', '') }}</a>
                                            </h4>
                                            <ol class="breadcrumb">
                                                <li><a href="{{ data_get($item2 ?? [], 'catlink', '') }}">{{ data_get($item2 ?? [], 'category', '') }}</a></li>
                                                <li class="hidden"><a title="{{ data_get($item2 ?? [], 'sub_category', '') }}" href="{{ data_get($item2 ?? [], 'subcatlink', '') }}">{{ data_get($item2 ?? [], 'sub_category', '') }}</a></li>
                                            </ol>
                                            <ul class="item-details">
                                                <li><i class="fa fa-map-marker"></i><a href="{{ data_get($item2 ?? [], 'citylink', '') }}">{{ data_get($item2 ?? [], 'location', '') }}</a></li>
                                            </ul>
                                            <div class="ad-meta">
                                                @if((data_get($item2 ?? [], 'price', ''))!="0") <span class="item-price"> {{ data_get($item2 ?? [], 'price', '') }} </span> @endif
                                                <ul class="contact-options pull-right" id="set-favorite">
                                                    <li><a href="#" data-item-id="{{ data_get($item2 ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd" class="fav_{{ data_get($item2 ?? [], 'id', '') }} fa fa-heart @if((data_get($item2 ?? [], 'favorite', ''))=="1") active @endif"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!-- ad-info -->
                                    </div>
                                    <!-- item-info -->
                                </div>
                            </div>

                             <!-- quick-item -->
                             @endforeach

                        </div>
                    </div>
                    <!-- #recent-slider -->
                    {{ $ad_home_page_below_latest_section ?? '' }}
                    </div>


                </div>
                <!-- product-list -->
            </div>
            <!-- row -->
        </div>
        <!-- main-content -->
    </div>
    <!-- container -->
</section>
<!-- home-one-info -->
<script>
    var loginurl = "{{ $link['LOGIN'] ?? '#' }}?ref=index.php";
</script>

@include('partials.footer')




