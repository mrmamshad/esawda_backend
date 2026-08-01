@include('partials.header')
<!-- main -->
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/checkbox-radio.css" type="text/css" rel="stylesheet" >
<section id="main" class="clearfix category-page">
    <form method="get" action="{{ $link['LISTING'] ?? '#' }}" name="locationForm" id="ListingForm">
        <div class="container">
            <div class="breadcrumb-section"><!-- breadcrumb -->
                <ol class="breadcrumb">
                    <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                    @if(($maincategory ?? "")!="") 
						<li>{{ $maincategory ?? '' }} </li>
                    @endif
                    @if(($subcategory ?? "")!="") 
						<li>{{ $subcategory ?? '' }} </li>
                    @endif
                    @if(($maincategory ?? "")($subcategory ?? "")=="") 
						<li>{{ __('quickad.all_categories') }} </li>
                    @endif
                    
                    <div class="pull-right back-result"><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>{{ __('quickad.back_result') }}</a>
                    </div>
                </ol>
                <!-- breadcrumb -->
            </div>
            <div class="banner">
                <!-- banner-form -->
                <div class="banner-form banner-form-full">
                    <div class="listing-form">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="dropdown category-dropdown"><a data-toggle="dropdown" href="#"><span class="change-text">{{ __('quickad.select_category') }}</span><i class="fa fa-navicon"></i></a>
                                    {{ $cat_dropdown ?? '' }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="keywords" value="{{ $keywords ?? '' }}"
                                       placeholder="{{ __('quickad.what') }} ?" id="keywords" style="box-shadow: none !important;">
                            </div>
                            <div class="col-md-3 banner-icon"><i class="fa fa-map-marker"></i>
                                <input type="text" class="form-control location" id="searchStateCity" name="location" placeholder="{{ __('quickad.where') }} ?" >
                                <input type="hidden" name="placetype" id="searchPlaceType" value="">
                                <input type="hidden" name="placeid" id="searchPlaceId" value="">
                            </div>
                            <div class="col-md-2">
                                <input type="hidden" id="input-maincat" name="cat" value="{{ $maincat ?? '' }}"/>
                                <input type="hidden" id="input-subcat" name="subcat" value="{{ $subcat ?? '' }}"/>
                                <input type="hidden" id="input-filter" name="filter" value="{{ $filter ?? '' }}"/>
                                <input type="hidden" id="input-sort" name="sort" value="{{ $sort ?? '' }}"/>
                                <input type="hidden" id="input-order" name="order" value="{{ $order ?? '' }}"/>
                                <button data-ajax-response='map' type="submit" name="Submit" class="form-control"><i
                                            class="fa fa-search"></i> {{ __('quickad.search') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- banner-form -->
            </div>
            <div class="category-info">
                <!-- Subcategory -->
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="sub-categories">
                            @foreach($subcatlist ?? [] as $subcatlist)
                            <div class="sub-category-container col-xs-6 col-sm-4 col-md-4 col-lg-5ths">
                                <div class="sub-category">
                                    <a href="{{ data_get($subcatlist ?? [], 'link', '') }}">{{ data_get($subcatlist ?? [], 'name', '') }}</a> <span class="count">({{ data_get($subcatlist ?? [], 'adcount', '') }})</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Subcategory -->
                <div class="row recommended-ads">
                    <div class="col-sm-3 col-md-3">

                        <div class="tg-sidebartitle enable-filters-button"><h2>{{ __('quickad.advance_search') }}: <i class="fa fa-plus"></i></h2></div>
                        <div id="custom-field-block" class='sidebar-container section @if(($showcustomfield ?? "")=="0") hidden @endif'>

                            <div id="ResponseCustomFields">
                                @foreach($customfields ?? [] as $customfields)
                                    @if((data_get($customfields ?? [], 'type', ''))=="text-field")
                                    <div class="form-group">
                                        <label class="label-title">{{ data_get($customfields ?? [], 'title', '') }}</label>
                                        {{ data_get($customfields ?? [], 'textbox', '') }}
                                    </div>
                                @endif
                                    @if((data_get($customfields ?? [], 'type', ''))=="textarea")
                                    <div class="form-group">
                                        <label class="label-title">{{ data_get($customfields ?? [], 'title', '') }}</label>
                                        {{ data_get($customfields ?? [], 'textarea', '') }}
                                    </div>
                                @endif
                                    @if((data_get($customfields ?? [], 'type', ''))=="drop-down")
                                    <div class="form-group">
                                        <label class="label-title">{{ data_get($customfields ?? [], 'title', '') }}</label><br>
                                        <select class="form-control" name="custom[{{ data_get($customfields ?? [], 'id', '') }}]">
                                            <option value="" selected>{{ __('quickad.select') }} {{ data_get($customfields ?? [], 'title', '') }}</option>
                                            {{ data_get($customfields ?? [], 'selectbox', '') }}
                                        </select>
                                    </div>
                                @endif
                                    @if((data_get($customfields ?? [], 'type', ''))=="radio-buttons")
                                    <div class="form-group">
                                        <label class="label-title">{{ data_get($customfields ?? [], 'title', '') }}</label><br>
                                        {{ data_get($customfields ?? [], 'radio', '') }}
                                    </div>
                                @endif
                                    @if((data_get($customfields ?? [], 'type', ''))=="checkboxes")
                                    <div class="form-group">
                                        <label class="label-title">{{ data_get($customfields ?? [], 'title', '') }}</label><br>
                                        {{ data_get($customfields ?? [], 'checkbox', '') }}
                                    </div>
                                @endif
                                @endforeach

                                <div class="inner">
                                    <div class="form-group">
                                        <label class="label-title">{{ __('quickad.price') }}</label>
                                        <div class="range-widget">
                                            <div class="range-inputs">
                                                <input type="text" placeholder="{{ __('quickad.from') }}" name="range1" value="{{ $range1 ?? '' }}">
                                                <input type="text" placeholder="{{ __('quickad.to') }}" name="range2" value="{{ $range2 ?? '' }}">
                                            </div>
                                            <button type="submit"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="Submit" class="btn tg-btn" id="advance-search-btn" style="padding: 0 40px;">{{ __('quickad.advance_search') }}</button>
                            </div>

                        </div>


                    </div>

                    @if(($post_premium_listing ?? "")=="0")
                    <style>
                        #premium_featured{ display: none !important;}
                        #premium_urgent{ display: none !important;}
                    </style>
                    @endif
                    <div class="col-sm-9 col-md-9">
                        <div class="section allAd">
                            <!-- featured-top -->
                            <div class="featured-top" id="listing-filter">
                                <div class="tab-box ">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" id="quick-filter">
                                        <li role="presentation" @if(($filter ?? "")=="") class="active" @endif><a href="#" data-filter-type="filter" data-filter-val="">{{ __('quickad.find_ads') }}<span class="badge">{{ $totaladsfound ?? '' }}</span></a></li>
                                        <li role="presentation" id="premium_featured" @if(($filter ?? "")=="featured") class="active" @endif><a href="#" data-filter-type="filter" data-filter-val="featured">{{ __('quickad.featured') }}<span class="badge"> {{ $featuredfound ?? '' }} </span></a></li>
                                        <li role="presentation" id="premium_urgent" @if(($filter ?? "")=="urgent") class="active" @endif><a href="#" data-filter-type="filter" data-filter-val="urgent">{{ __('quickad.urgent') }}
                                        <span class="badge"> {{ $urgentfound ?? '' }} </span></a></li>
                                        <div class="dropdown pull-right">
                                            <!-- category-change -->
                                            <div class="dropdown category-dropdown">
                                                <h5>{{ __('quickad.sort_by') }}:</h5>
                                                <a id="sort-dropdown" data-toggle="dropdown" href="#"><span class="change-text">
                                                        @if(($sort ?? "")=="") {{ __('quickad.newest') }} @endif
                                                        @if(($sort ?? "")=="id") {{ __('quickad.newest') }} @endif
                                                        @if(($sort ?? "")=="title") {{ __('quickad.name') }} @endif
                                                        @if(($sort ?? "")=="date") {{ __('quickad.date') }} @endif
                                                        @if(($sort ?? "")=="price") {{ __('quickad.price') }} @endif
                                                    </span><i
                                                        class="fa fa-caret-square-o-down"></i></a>
                                                <ul class="dropdown-menu category-change">
                                                    <li><a href="#" data-filter-type="sort" data-filter-val="id" data-order="desc">{{ __('quickad.newest') }}</a>
                                                    </li>
                                                    <li><a href="#" data-filter-type="sort" data-filter-val="title" data-order="desc">{{ __('quickad.name') }}</a>
                                                    </li>
                                                    <li><a href="#" data-filter-type="sort" data-filter-val="date" data-order="desc">{{ __('quickad.date') }}</a>
                                                    </li>
                                                    <li><a href="#" data-filter-type="sort" data-filter-val="price" data-order="desc">{{ __('quickad.price') }}:
                                                        {{ __('quickad.high_to_low') }}</a></li>
                                                    <li><a href="#" data-filter-type="sort" data-filter-val="price" data-order="asc">{{ __('quickad.price') }}:
                                                        {{ __('quickad.low_to_high') }}</a></li>
                                                </ul>
                                            </div>
                                            <!-- category-change -->
                                        </div>
                                    </ul>
                                </div>
                                <div class="filter-section">
                                    <h2>
                                        @if(($filter ?? "")=="") {{ __('quickad.all_ads') }} @endif
                                        @if(($filter ?? "")=="featured") {{ __('quickad.featured_ad') }} @endif
                                        @if(($filter ?? "")=="urgent") {{ __('quickad.urgent_ads') }} @endif
                                    </h2>

                                    <div class="sorting well">
                                        <div class="btn-group pull-right">
                                            <button type="button" class="btn" id="list"><i class="fa fa-th-list fa-white icon-white"></i></button>
                                            <button type="button" class="btn" id="grid"><i class="fa fa-th fa"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- featured-top -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane fade in active" id="allad">
                                    <div class="" id="serchlist" data-listing-view="{{ $listing_view ?? '' }}">
                                        <div class="searchresult list" style="display: none;">
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
                                                    <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}"
                                                         alt="{{ data_get($item ?? [], 'product_name', '') }}"></div>
                                                <div class="item-info col-sm-12 {{ data_get($item ?? [], 'highlight_bg', '') }}">
                                                    <!-- ad-info -->
                                                    <div class="ad-info">
                                                        <h4 class="item-title">
                                                            @if((data_get($item ?? [], 'sub_image', ''))!="")
                                                            <img src="{{ data_get($item ?? [], 'sub_image', '') }}" width="24px" alt="{{ data_get($item ?? [], 'sub_title', '') }}" title="{{ data_get($item ?? [], 'sub_title', '') }}"/>
                                                            @endif
                                                            <a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
                                                        </h4>
                                                        <ul class="contact-options pull-right" id="set-favorite">
                                                            <li><a href="#" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd" class='fav_{{ data_get($item ?? [], 'id', '') }} fa fa-heart @if((data_get($item ?? [], 'favorite', ''))=="1") active @endif'></a></li>
                                                        </ul>
                                                        <ol class="breadcrumb">
                                                            <li><a href="{{ data_get($item ?? [], 'catlink', '') }}">{{ data_get($item ?? [], 'category', '') }}</a></li>
                                                            <li><a href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a></li>
                                                        </ol>
                                                        <ul class="item-details">
                                                            <li><i class="fa fa-map-marker"></i><a href="{{ data_get($item ?? [], 'citylink', '') }}">{{ data_get($item ?? [], 'city', '') }}</a></li>
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
                                        {ITEM.banner-ad-list-view}
                                        @endforeach
                                        </div>
                                        <div class="searchresult grid" style="display: none;">
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
                                                                    <img src="{{ data_get($item2 ?? [], 'sub_image', '') }}" width="16px" alt="{{ data_get($item2 ?? [], 'sub_title', '') }}" title="{{ data_get($item2 ?? [], 'sub_title', '') }}"/>
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
                                                    @if('{ITEM2.banner-ad-grid-view}'!='')
                                                <div class="col-md-4 col-sm-6 col-xs-12 mar-bot-10 clear-left-3">
                                                    {ITEM2.banner-ad-grid-view}
                                                </div>
                                                @endif
                                                @endforeach
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                        @if(($adsfound ?? "")=="0")
                                        <h4> {{ __('quickad.no_result_found') }} : {{ $pagetitle ?? '' }}.</h4><br>

                                        @endif
                                        <!-- Pagination-->
                                        <div class="pagination-container">
                                            <ul class="pagination">
                                                @foreach($pages ?? [] as $pages)
                                                @if((data_get($pages ?? [], 'current', ''))=="0")
                                                <li><a href="{{ data_get($pages ?? [], 'link', '') }}">{{ data_get($pages ?? [], 'title', '') }}</a></li>
                                                @endif
                                                @if((data_get($pages ?? [], 'current', ''))=="1")
                                                <li class="active"><a>{{ data_get($pages ?? [], 'title', '') }}</a></li>
                                                @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                        <!-- Pagination-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- container -->
    </form>
</section>
<!-- main -->

<script type="text/javascript">
    $(document).ready(function () {
        $(".current").addClass("active");
        if ($('.getParent').length > 0) {
            $('.getParent').parent().addClass('in');
        }
    });

    $('#listing-filter').on('click', '#quick-filter li a', function (e) {
        var $item = $(this).closest('a');

        var filtertype = $item.data('filter-type');
        var filterval = $item.data('filter-val');
        $('#input-' + filtertype).val(filterval);
        $('#input-order').val($item.data('order'));
        $('#ListingForm').submit();
    });

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
    var loginurl = "{{ $link['LOGIN'] ?? '#' }}?ref=listing"</script>


@include('partials.footer')