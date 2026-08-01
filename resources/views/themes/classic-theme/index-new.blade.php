@include('partials.header')
<style>
    .category-quickad-1 {
        background-color: transparent;
        display: inline-block;
        padding: 0px;
        border-radius: 3px;
        width: 100%;
        margin: 0;
    }
    .category-quickad-1 ul.category-list {
        display: flex;
    }
    .category-quickad-1 .category-list .category-item {
        width: 25%;
        float: left;
        display: inline-block;
        margin: 0 21px;
    }
    .category-quickad-1 .category-title {
        color: #000000;
        display: block;
        font-size: 14px;
        /* white-space: nowrap; */
        overflow: hidden;
        line-height: initial;
        margin-top: 10px;
    }

    .category-quickad-1 .category-icon img {
        width: 30px;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
    }
    .category-quickad-1 .category-icon {
        min-height: 37px;
        width: 100%;
        display: flex;
        justify-content: center;
        position:relative;
    }
    .category-quickad-1 .category-icon::before{
        content:"";;
        position:absolute;
        top:50%;
        left:50%;
        transform:translate(-50%,-50%);
        background: #fff;
        width:50px;
        height:50px;
        border-radius:50px;
    }

    .category-quickad-1 .category-icon i {
        font-size: 40px;
        color: #6f6f6f
    }
    .category-quickad-1 .category-quantity {
        color: #b5b5b5
    }
    .category-ads, .category-list .category-item .fa-chevron-right{
        display: none;
    }
    @media only screen and (max-width: 991px){
        .category-quickad-1 ul.category-list {
            display: block;
        }
        .category-quickad-1 .category-list .category-item {
            text-align: left;
            width: 100%;
            border-bottom: 1px solid #eee;
            padding: 0px 15px;
            position: relative;
            margin: 0;
        }
        .category-quickad-1 .category-list .category-item {
            padding: 0px 15px;
            margin: 0;
        }
        .category-quickad-1 .category-item a {
            display: flex;
            padding: 8px 0;
        }
        .category-quickad-1 .category-icon {
            display: inline-block;
            vertical-align: middle;
            position: relative;
            width: 45px;
            height: 45px;
            margin-left: 10px;
        }
        .category-quickad-1 .category-icon::before {
            display:none;
        }
        .category-ads, .category-list .category-item .fa-chevron-right {
            display: block;
        }
        .category-ads {
            display: block;
            position: absolute;
            left: 71px;
            bottom: 5px;
            color: #9f9f9f !important;
        }
        .category-quickad-1 .category-item .fa-chevron-right {
            display: block;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
    }

</style>
<!-- home-one-info -->
<section class="clearfix home-one" id="page" data-ipapi="ip_api">
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
                                                <div data-option="no" class="loc-tracking"><i class="fa fa-crosshairs"></i></div>
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
<section class="clearfix" style ="padding-bottom:0">
    <!-- world -->
    <div class="container">
        <!-- main-content -->
        <div class="main-content" id="serchlist">
            <!-- row -->
            <div class="row">

                <!-- advertisement Left-->
                <!-- product-list -->
                <div class="col-md-12">
                    <!-- categorys -->
                    <div class="category-quickad-1 text-center">
                        <ul class="category-list">
                            <li class = "category-item post-ads">
                                <a href ="#">
                                <div class="category-icon">
                                    <i class= "fa fa-plus"></i>
                                </div>
                                <div class =" post-details">
                                    <span class="post-title">KwickBiz for Business Owners..</span>
                                    <span class="post-subtitle">Click here to add your business</span>
                                </div>
                                </a>
                            </li>
                            @foreach($cat ?? [] as $cat)
                            <li class="category-item"><a href="{{ data_get($cat ?? [], 'catlink', '') }}">
                                    @if((data_get($cat ?? [], 'picture', ''))=="")
                                    <div class="category-icon"><i class="{{ data_get($cat ?? [], 'icon', '') }}"></i></div>
                                    @endif
                                    @if((data_get($cat ?? [], 'picture', ''))!="")
                                    <div class="category-icon"><img src="{{ data_get($cat ?? [], 'picture', '') }}"/></div>
                                    @endif

                                <span class="category-title">{{ data_get($cat ?? [], 'main_title', '') }}</span>
                                <span class="category-ads">{{ data_get($cat ?? [], 'main_ads_count', '') }} ads</span>
                                <i class = "fa fa-chevron-right"></i>
                                </a>
                            </li>
                            <!-- category-item -->
                            @endforeach
                        </ul>
                    </div>
					<!-- category-ad -->
                    <!-- quickad-section -->
                    <div class="quickad-section text-center" id="quickad-top">{{ $ad_home_page_below_category_section ?? '' }}</div>
                    <!-- quickad-section -->
                    <!-- featured-slide -->
                      <!-- featured-slide -->
                    

                    <!-- featured-slide -->
                    <!-- recent-slide -->
                     <div id = "home-recommends" class="section recommended-ads">
                        <div class="row">
                           <div class="col-sm-12">  
                                <div class="featured-top"> 
                                    <h4>{{ __('quickad.latest_ads') }}</h4>
                                </div>
                            </div>
                        
                            <!-- recent-slider -->
                            @foreach($item ?? [] as $item)

                            <div class="col-md-3 col-xs-6" style="margin-bottom: 30px;">

                                <div class="quick-item">
                                    <!-- item-image -->
                                    <div class="item-image-box">
                                        <div class="item-image" title = "{{ data_get($item ?? [], 'product_name', '') }} for sale in {{ data_get($item ?? [], 'city', '') }}">
                                            <a href="{{ data_get($item ?? [], 'link', '') }}" title = "{{ data_get($item ?? [], 'product_name', '') }} for sale in {{ data_get($item ?? [], 'city', '') }}">
                                                <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}" class="img-responsive"{{ data_get($item ?? [], 'product_name', '') }}>
                                            </a>
                                            <div class="item-badges">
                                                @if((data_get($item ?? [], 'featured', ''))=="1") <span class="featured">{{ __('quickad.featured') }}</span>@endif
                                                @if((data_get($item ?? [], 'urgent', ''))=="1") <span>{{ __('quickad.urgent') }}</span> @endif
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
                            </div>

                             <!-- quick-item -->
                             @endforeach
                             
                             <!-- here -->
                             
                             <div class="col-lg-12 text-center">
                                <a href=/search class="btn btn-info" data-target="#"><i class="fa fa-file"></i> View More</a>

                        </div>
                    </div>
                    <!-- #recent-slider -->
                    </div> 
                    </div>
                    
                    <div class="popular-cities clr">
                        <p>Popular cities in <a href="/search">Nigeria</a>, <a href="/post ad">Post free ads in Nigeria</a>, <a href="/search">Online classifieds in Nigeria</a>, <a href="/search">Real estate in Nigeria</a>

                        <div class="list row">

                            <ul class="col-lg-12 col-md-12 popularcity">
                                @foreach($popularcity ?? [] as $popularcity)
                                {{ data_get($popularcity ?? [], 'tpl', '') }}
                                @endforeach
                            </ul>
                        </div>
                        <div class="footer-post-ad clearfix hidden-xs"> <p class="clearfix" style="width:100%"> Are you a business owner? You can list your business on KwickBiz and be found online. &nbsp; <a href="/post-ad" class="btn btn-info" data-target="#"><i class="fa fa-plus"></i>  List your Business</a></p></div>
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




