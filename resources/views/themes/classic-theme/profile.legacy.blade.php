@include('partials.header')
<!-- ad-profile-page -->
<section id="main" class="clearfix  ad-profile-page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.profile') }}</li>
                <div class="pull-right back-result">
                    <a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>{{ __('quickad.back_result') }}</a>
                </div>
            </ol>
            <!-- breadcrumb -->
        </div>
        <!-- Main Content  -->
        <div class="row">
            <!-- Page-Content -->
            <div class="col-sm-12 page-content">
                <div class="panel-user-details">
                    <!-- profile-details -->
                    <div class="user-details section">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="user-img profile-img">
                                    <img src="{{ $site_url ?? '' }}storage/profile/{{ $userimage ?? '' }}" alt="{{ $fullname ?? '' }}" class="img-responsive">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="user-admin">
                                    <h3>{{ $fullname ?? '' }}
                                        @if(($sub_image ?? "")!="")
                                        <img src="{{ $sub_image ?? '' }}" alt="{{ $sub_title ?? '' }}" title="{{ $sub_title ?? '' }}" width="28px"/>
                                        @endif
                                    </h3>

                                    <div>{{ $about ?? '' }}</div>
                                    <section class="contacts">
                                        @if(($username ?? "")!="") <figure class="social-links hidden"><i class="fa fa-user"></i>{{ $username ?? '' }}</figure>@endif
                                        @if(($address ?? "")!="") <figure class="social-links"><i class="fa fa-map-marker"></i><a href="https://maps.google.com/?q={{ $address ?? '' }}" target="_blank" rel="nofollow">{{ $address ?? '' }}</a></figure>@endif
                                        @if(($phone ?? "")!="") <figure class="social-links"><i class="fa fa-phone"></i><a href="tel:{{ $phone ?? '' }}">{{ $phone ?? '' }}</a></figure>@endif
                                        @if(($email ?? "")!="") <figure class="social-links"><a href="mailto:{{ $email ?? '' }}"><i class="fa fa-envelope"></i>{{ $email ?? '' }}</a></figure>@endif
                                        @if(($website ?? "")!="") <figure class="social-links"><i class="fa fa-globe"></i><a href="{{ $website ?? '' }}" target="_blank" rel="nofollow">{{ $website ?? '' }}</a></figure>@endif
                                    </section>
                                    <!--end contacts-->
                                    <!-- social-links -->
                                    <p>{{ __('quickad.share') }} {{ __('quickad.profile') }}</p>
                                    <div class="social-links text-center">
                                        <div class="social-share"></div>
                                        <!--end social-->
                                    </div>
                                    <!-- social-links -->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="user-ads-details">
                                    <div class="site-visit">
                                        <h3><a href="#">{{ $profilevisit ?? '' }}</a></h3>
                                        <small>{{ __('quickad.visits') }}</small>
                                    </div>
                                    <div class="my-quickad">
                                        <h3><a href="#">{{ $userpremiumads ?? '' }}</a></h3>
                                        <small>{{ __('quickad.featured') }}</small>
                                    </div>
                                    <div class="favourites">
                                        <h3><a href="#">{{ $userads ?? '' }}</a></h3>
                                        <small>{{ __('quickad.total_ads') }}</small>
                                    </div>
                                </div>
                                <ul class="social_share margin-top-100 pull-right">
                                    @if(($facebook ?? "")!="")  <li><a href="{{ $facebook ?? '' }}" target="_blank" class="facebook"><i class="fa fa-facebook"></i></a></li> @endif
                                    @if(($twitter ?? "")!="")  <li><a href="{{ $twitter ?? '' }}" target="_blank" class="twitter"><i class="fa fa-twitter"></i></a></li>@endif
                                    @if(($gplus ?? "")!="")  <li><a href="{{ $gplus ?? '' }}" target="_blank" class="google"><i class="fa fa-google-plus"></i></a></li>@endif
                                    @if(($linkedin ?? "")!="")  <li><a href="{{ $linkedin ?? '' }}" target="_blank" class="linkden"><i class="fa fa-linkedin"></i></a></li>@endif
                                    @if(($instagram ?? "")!="")  <li><a href="{{ $instagram ?? '' }}" target="_blank" class="instagram"><i class="fa fa-instagram"></i></a></li>@endif
                                    @if(($youtube ?? "")!="")  <li><a href="{{ $youtube ?? '' }}" target="_blank" class="youtube"><i class="fa fa-youtube"></i></a></li>@endif
                                </ul>
                                <!--end social-->
                            </div>
                        </div>
                    </div>

                    <div class="section banner">
                        <!-- banner-form -->
                        <div class="banner-form">
                            <form method="get" action="#" name="locationForm" id="ListingForm">
                                <!-- category-change -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="dropdown category-dropdown"><a data-toggle="dropdown" href="#"><span class="change-text">{{ __('quickad.select_category') }}</span><i class="fa fa-navicon"></i></a>{{ $cat_dropdown ?? '' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="keywords" value="{{ $keywords ?? '' }}" placeholder="{{ __('quickad.what') }} ?" style="padding: 0px;">
                                    </div>
                                    <div class="col-md-3 banner-icon"><i class="fa fa-map-marker"></i>
                                        <input type="text" class="form-control location" id="searchStateCity" name="location" placeholder="{{ __('quickad.where') }} ?" >
                                        <input type="hidden" name="placetype" id="searchPlaceType" value="">
                                        <input type="hidden" name="placeid" id="searchPlaceId" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="hidden" id="input-maincat" name="cat" value="{{ $maincat ?? '' }}"/>
                                        <input type="hidden" id="input-subcat" name="subcat" value="{{ $subcat ?? '' }}"/>
                                        <input type="hidden" id="input-sort" name="sort" value="{{ $sort ?? '' }}"/>
                                        <input type="hidden" id="input-order" name="order" value="{{ $order ?? '' }}"/>
                                        <input type="hidden" id="input-subcat" name="username" value="{{ $username ?? '' }}"/>
                                        <button data-ajax-response='map' type="submit" name="Submit" class="form-control"><i class="fa fa-search"></i> {{ __('quickad.search') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- banner-form -->
                    </div>
                    <!-- banner -->

                    <!-- profile-details -->
                    <div class="row">
                        @if(($left_adstatus ?? "")=="1")
                        <div class="hidden-xs hidden-sm col-md-2 text-center">
                            <div class="advertisement" id="quickad-left">{{ $left_adscode ?? '' }}</div>
                        </div>
                        @endif

                        <!-- my-quickad -->
                        <div class="my-details section {{ $category_column ?? '' }}">
                            <!-- featured-top -->
                            <div class="featured-top">
                                <div class="filter-section">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h2>{{ __('quickad.all_ads') }}</h2>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="sorting well">
                                                <div class="btn-group pull-right">
                                                    <button class="btn" id="list"><i
                                                            class="fa fa-th-list fa-white icon-white"></i></button>
                                                    <button class="btn" id="grid"><i class="fa fa-th fa"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- featured-top -->
                            <!-- Tab panes -->
                            <div class="" id="serchlist">
                                <div class="searchresult list hideresult" style="display: none;">
                                    @foreach($item ?? [] as $item)
                                    <!-- quick-item -->
                                    <div class="quick-item row">
                                        <!-- item-image -->
                                        <div class="ad-listing">
                                            <div class="image bg-transfer">
                                                <figure>
                                                    <div class="item-badges">
                                                        @if((data_get($item ?? [], 'featured', ''))=="1") <span class="featured">{{ __('quickad.featured') }}</span>@endif
                                                        @if((data_get($item ?? [], 'urgent', ''))=="1") <span>{{ __('quickad.urgent') }}</span>@endif
                                                    </div>
                                                </figure>
                                                <img src="{{ $site_url ?? '' }}storage/products/{{ data_get($item ?? [], 'picture', '') }}"
                                                     alt="{{ data_get($item ?? [], 'product_name', '') }}"></div>
                                            <div class="item-info {{ data_get($item ?? [], 'highlight_bg', '') }} col-sm-12">
                                                <!-- ad-info -->
                                                <div class="ad-info">
                                                    <h4 class="item-title"><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
                                                    </h4>
                                                    <ul class="contact-options pull-right" id="set-favorite">
                                                        <li><a href="#" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}"
                                                               data-action="setFavAd" class="fav_{{ data_get($item ?? [], 'id', '') }} fa fa-heart @if("
                                                               {{ data_get($item ?? [], 'favorite', '') }}"=="1") active @endif"></a></li>
                                                    </ul>
                                                    <ol class="breadcrumb">
                                                        <li><a href="{{ data_get($item ?? [], 'catlink', '') }}">{{ data_get($item ?? [], 'category', '') }}</a></li>
                                                        <li><a href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a>
                                                        </li>
                                                    </ol>
                                                    <ul class="item-details">
                                                        <li><i class="fa fa-map-marker"></i><a href="#">{{ data_get($item ?? [], 'city', '') }}</a></li>
                                                        <li><i class="fa fa-clock-o"></i>{{ data_get($item ?? [], 'created_at', '') }}</li>
                                                    </ul>
                                                    @if((data_get($item ?? [], 'price', ''))!="0") <span class="item-price"> {{ data_get($item ?? [], 'price', '') }} </span> @endif

                                                    <div><a class="view-btn" href="{{ data_get($item ?? [], 'link', '') }}">{{ __('quickad.view_ad') }}</a></div>
                                                </div>
                                                <!-- ad-info -->
                                            </div>
                                            <!-- item-info -->
                                        </div>
                                    </div>
                                    <!-- quick-item -->
                                    @endforeach
                                </div>
                                <div class="searchresult grid hideresult" style="display: none;">
                                    <div class="gird-layout row">
                                        @foreach($item2 ?? [] as $item2)
                                            <div class="col-md-4 col-sm-6 col-xs-12 mar-bot-10 clear-left-3">
                                                <div style="border: 1px solid #f3f3f3;">
                                                    <div class="item-image-box">
                                                        <div class="item-image"><a href="{{ data_get($item2 ?? [], 'link', '') }}"><img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item2 ?? [], 'picture', '') }}" alt="{{ data_get($item2 ?? [], 'product_name', '') }}" class=""></a>

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
                                                            <h4 class="item-title">
                                                                @if((data_get($item2 ?? [], 'sub_image', ''))!="")
                                                                <img src="{{ data_get($item2 ?? [], 'sub_image', '') }}" width="24px" alt="{{ data_get($item2 ?? [], 'sub_title', '') }}" title="{{ data_get($item2 ?? [], 'sub_title', '') }}"/>
                                                                @endif
                                                                <a href="{{ data_get($item2 ?? [], 'link', '') }}">{{ data_get($item2 ?? [], 'product_name', '') }}</a>
                                                            </h4>
                                                            <ol class="breadcrumb">
                                                                <li><a href="{{ data_get($item2 ?? [], 'catlink', '') }}">{{ data_get($item2 ?? [], 'category', '') }}</a></li>
                                                                <li><a href="{{ data_get($item2 ?? [], 'subcatlink', '') }}">{{ data_get($item2 ?? [], 'sub_category', '') }}</a>
                                                                </li>
                                                            </ol>
                                                            <ul class="item-details">
                                                                <li><i class="fa fa-map-marker"></i><a href="{{ data_get($item2 ?? [], 'citylink', '') }}">{{ data_get($item2 ?? [], 'city', '') }}</a></li>
                                                                <li><i class="fa fa-clock-o"></i>{{ data_get($item2 ?? [], 'created_at', '') }}</li>
                                                            </ul>
                                                            <div class="ad-meta">
                                                                @if((data_get($item2 ?? [], 'price', ''))!="0") <span class="item-price"> {{ data_get($item2 ?? [], 'price', '') }} </span> @endif
                                                                <ul class="contact-options pull-right" id="set-favorite">
                                                                    <li>
                                                                        <a href="#" data-item-id="{{ data_get($item2 ?? [], 'id', '') }}"
                                                                           data-userid="{{ $user_id ?? '' }}" data-action="setFavAd"
                                                                           class="fav_{{ data_get($item2 ?? [], 'id', '') }} fa fa-heart @if("
                                                                        {{ data_get($item2 ?? [], 'favorite', '') }}"=="1") active @endif"></a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <!-- ad-info -->
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- quick-item -->
                                        @endforeach
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                @if(($adsfound ?? "")=="0")
                                <h4>{{ __('quickad.no_result_found') }}</h4>
                                @endif
                                <!-- Pagination-->
                                <div class="pagination-container">
                                    <ul class="pagination">
                                        @foreach($pages ?? [] as $pages)@if((data_get($pages ?? [], 'current', ''))=="0")
                                        <li><a href="{{ data_get($pages ?? [], 'link', '') }}">{{ data_get($pages ?? [], 'title', '') }}</a></li>
                                        @endifIF("{{ data_get($pages ?? [], 'current', '') }}"=="1"){
                                        <li class="active"><a>{{ data_get($pages ?? [], 'title', '') }}</a></li>
                                        @endif@endforeach
                                    </ul>
                                </div>
                                <!-- Pagination-->
                            </div>
                        </div>
                        <!-- my-quickad -->
                        <!-- advertisement -->
                        @if(($right_adstatus ?? "")=="1")
                        <div class="hidden-xs hidden-sm col-md-2 text-center">
                            <div class="advertisement" id="quickad-right">{{ $right_adscode ?? '' }}</div>
                        </div>
                        @endif
                        <!-- advertisement -->
                    </div>
                </div>
            </div>
            <!-- # End Page-Content -->
        </div>
        <!-- row -->
    </div>
    <!-- container -->
</section>
<!-- ad-profile-page -->
<script>
    var getMaincatId = '{{ $maincat ?? '' }}';
    var getSubcatId = '{{ $subcat ?? '' }}';

    $(window).bind("load", function () {
        if (getMaincatId != "") {
            $('li a[data-cat-type="maincat"][data-ajax-id="' + getMaincatId + '"]').trigger('click');
        } else if (getSubcatId != "") {
            $('li ul li a[data-cat-type="subcat"][data-ajax-id="' + getSubcatId + '"]').trigger('click');
        } else {
            $('li a[data-cat-type="all"]').trigger('click');
        }
    });
</script>
<script>var loginurl = "{{ $link['LOGIN'] ?? '#' }}?ref=profile.php";</script>

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
</script>
@include('partials.footer') 