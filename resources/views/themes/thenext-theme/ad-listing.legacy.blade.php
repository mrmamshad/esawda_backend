@include('partials.header')
<form method="get" action="{{ $link['LISTING'] ?? '#' }}" name="locationForm" id="ListingForm">
    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>{{ __('quickad.we_found') }} {{ $adsfound ?? '' }} {{ __('quickad.ads_listings') }}</h2>
                    <!-- Breadcrumbs -->
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                            @if(($maincategory ?? "")!="") 
                                <li>{{ $maincategory ?? '' }}</li>
                            @endif
                            @if(($subcategory ?? "")!="") 
                                <li>{{ $subcategory ?? '' }}</li>
                            @endif
                            @if(($maincategory ?? "")($subcategory ?? "")=="") 
                                <li>{{ __('quickad.all_categories') }}</li>
                            @endif
                        </ul>
                    </nav>
                    <div class="intro-banner-search-form listing-page margin-top-30">
                        <!-- Search Field -->
                        <div class="intro-search-field">
                            <div class="dropdown category-dropdown">
                                <a data-toggle="dropdown" href="#">
                                    <span class="change-text">{{ __('quickad.select') }} {{ __('quickad.category') }}</span><i class="fa fa-navicon"></i>
                                </a>
                                {{ $cat_dropdown ?? '' }}
                            </div>
                        </div>
                        <div class="intro-search-field">
                            <input id="keywords" type="text" name="keywords" placeholder="{{ __('quickad.what') }} ?" value="{{ $keywords ?? '' }}">
                        </div>
                        <div class="intro-search-field with-autocomplete live-location-search">
                            <div class="input-with-icon">
                                <input type="text" id="searchStateCity" name="location" placeholder="{{ __('quickad.where') }}">
                                <i class="la la-map-marker"></i>
                                <div data-option="{{ $auto_detect_location ?? '' }}" class="loc-tracking"><i class="fa fa-crosshairs"></i></div>
                                <input type="hidden" name="placetype" id="searchPlaceType" value="">
                                <input type="hidden" name="placeid" id="searchPlaceId" value="">
                                <input type="hidden" id="input-maincat" name="cat" value="{{ $maincat ?? '' }}"/>
                                <input type="hidden" id="input-subcat" name="subcat" value="{{ $subcat ?? '' }}"/>
                                <input type="hidden" id="input-filter" name="filter" value="{{ $filter ?? '' }}"/>
                                <input type="hidden" id="input-sort" name="sort" value="{{ $sort ?? '' }}"/>
                                <input type="hidden" id="input-order" name="order" value="{{ $order ?? '' }}"/>
                            </div>
                        </div>
                        <div class="intro-search-button">
                            <button class="button ripple-effect">{{ __('quickad.search') }}</button>
                        </div>
                    </div>
                    <div class="hide-under-768px margin-top-20">
                        <ul class="categories-list">
                            @foreach($subcatlist ?? [] as $subcatlist)
                            <li>
                                <a href="{{ data_get($subcatlist ?? [], 'link', '') }}">{{ data_get($subcatlist ?? [], 'name', '') }} <span class="count">({{ data_get($subcatlist ?? [], 'adcount', '') }})</span></a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="ubm_wrapper" data-cat="{{ $maincat ?? '' }}" data-subcat="{{ $subcat ?? '' }}">{{ $ad_search_page_below_category ?? '' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-lg-4">
                <div class="filter-button-container">
                    <button type="button" class="enable-filters-button">
                        <i class="enable-filters-button-icon"></i>
                        <span class="show-text">{{ __('quickad.advance_search') }}</span>
                        <span class="hide-text">{{ __('quickad.advance_search') }}</span>
                    </button>
                </div>
                <div class="sidebar-container search-sidebar">
                    @foreach($customfields ?? [] as $customfields)
                        @if((data_get($customfields ?? [], 'type', ''))=="text-field")
                        <div class="sidebar-widget">
                            <h3 class="label-title">{{ data_get($customfields ?? [], 'title', '') }}</h3>
                            {{ data_get($customfields ?? [], 'textbox', '') }}
                        </div>
                    @endif
                        @if((data_get($customfields ?? [], 'type', ''))=="textarea")
                        <div class="sidebar-widget">
                            <h3 class="label-title">{{ data_get($customfields ?? [], 'title', '') }}</h3>
                            {{ data_get($customfields ?? [], 'textarea', '') }}
                        </div>
                    @endif
                        @if((data_get($customfields ?? [], 'type', ''))=="drop-down")
                        <div class="sidebar-widget">
                            <h3 class="label-title">{{ data_get($customfields ?? [], 'title', '') }}</h3>
                            <select class="selectpicker with-border" name="custom[{{ data_get($customfields ?? [], 'id', '') }}]">
                                <option value="" selected>{{ __('quickad.select') }} {{ data_get($customfields ?? [], 'title', '') }}</option>
                                {{ data_get($customfields ?? [], 'selectbox', '') }}
                            </select>
                        </div>
                    @endif
                        @if((data_get($customfields ?? [], 'type', ''))=="radio-buttons")
                        <div class="sidebar-widget">
                            <h3 class="label-title">{{ data_get($customfields ?? [], 'title', '') }}</h3>
                            {{ data_get($customfields ?? [], 'radio', '') }}
                        </div>
                    @endif
                        @if((data_get($customfields ?? [], 'type', ''))=="checkboxes")
                        <div class="sidebar-widget">
                            <h3 class="label-title">{{ data_get($customfields ?? [], 'title', '') }}</h3>
                            {{ data_get($customfields ?? [], 'checkbox', '') }}
                        </div>
                    @endif
                    @endforeach
                    <div class="sidebar-widget">
                        <h3>{{ __('quickad.price') }}</h3>
                        <div class="range-widget">
                            <div class="range-inputs">
                                <input type="text" placeholder="{{ __('quickad.from') }}" name="range1" value="{{ $range1 ?? '' }}">
                                <input type="text" placeholder="{{ __('quickad.to') }}" name="range2" value="{{ $range2 ?? '' }}">
                            </div>
                            <button type="submit" class="button"><i class="icon-feather-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="ubm_wrapper" data-cat="{{ $maincat ?? '' }}" data-subcat="{{ $subcat ?? '' }}">{{ $ad_search_page_sidebar ?? '' }}</div>
            </div>
            <div class="col-xl-9 col-lg-8">

                <h3 class="page-title">{{ __('quickad.search_results') }}</h3>

                <div class="notify-box margin-top-15">
                   <span class="font-weight-600">{{ $adsfound ?? '' }} {{ __('quickad.ads_found') }}</span>

                    <div class="sort-by">
                        <span>{{ __('quickad.sort_by') }}</span>
                        <select class="selectpicker hide-tick" id="sort-filter">
                            <option data-filter-type="sort" data-filter-val="id" data-order="desc">{{ __('quickad.newest') }}</option>
                            <option data-filter-type="sort" data-filter-val="title" data-order="desc">{{ __('quickad.name') }}</option>
                            <option data-filter-type="sort" data-filter-val="date" data-order="desc">{{ __('quickad.date') }}</option>
                            <option data-filter-type="sort" data-filter-val="price" data-order="desc">{{ __('quickad.price') }} ({{ __('quickad.high_to_low') }})</option>
                            <option data-filter-type="sort" data-filter-val="price" data-order="asc">{{ __('quickad.price') }} ({{ __('quickad.low_to_high') }})</option>
                        </select>
                    </div>
                </div>

                <div class="listings-container margin-top-35">
                    @foreach($item ?? [] as $item)
                    <div class="job-listing @if({{ data_get($item ?? [], 'highlight', '') }}) highlight @endif">
                        <div class="job-listing-details">
                            <div class="job-listing-company-logo">
                                <img class="lazy-load" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}">
                            </div>
                            <div class="job-listing-description">
                                <h3 class="job-listing-title"><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
                                    @if((data_get($item ?? [], 'featured', ''))=="1") <div class="badge blue"> {{ __('quickad.featured') }}</div> @endif
                                    @if((data_get($item ?? [], 'urgent', ''))=="1") <div class="badge yellow"> {{ __('quickad.urgent') }}</div> @endif
                                </h3>
                                <ol class="breadcrumb">
                                    <li><a href="{{ data_get($item ?? [], 'catlink', '') }}"><i class="la la-tags"></i> {{ data_get($item ?? [], 'category', '') }}</a></li>
                                    <li><a href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a></li>
                                </ol>
                                <ul class="prop_details mb0">
                                    {{ data_get($item ?? [], 'cf_tpl', '') }}
                                </ul>
                            </div>
                        </div>
                        <div class="job-listing-footer with-icon">
                            <ul>
                                <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ data_get($item ?? [], 'username', '') }}"><i class="la la-user"></i> {{ data_get($item ?? [], 'username', '') }}</a></li>
                                <li><a href="{{ data_get($item ?? [], 'citylink', '') }}"><i class="la la-map-marker"></i> {{ data_get($item ?? [], 'city', '') }}, {{ data_get($item ?? [], 'state', '') }}</a></li>
                                @if((data_get($item ?? [], 'price', ''))!="0")
                                <li><i class="la la-credit-card"></i> {{ data_get($item ?? [], 'price', '') }}</li>
                                @endif
                                <li><i class="la la-clock-o"></i> {{ data_get($item ?? [], 'created_at', '') }}</li>
                            </ul>
                            <span class="fav-icon set-item-fav @if('{{ data_get($item ?? [], 'favorite', '') }}') added @endif" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd"></span>
                        </div>
                    </div>
                    {ITEM.banner-ad-list-view}
                    @endforeach
                    <div class="clearfix"></div>
                    @if(($adsfound ?? "")!="0")
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Pagination -->
                            <div class="pagination-container margin-top-20 margin-bottom-60">
                                <nav class="pagination">
                                    <ul>
                                        @foreach($pages ?? [] as $pages)
                                        @if((data_get($pages ?? [], 'current', ''))=="0")
                                        <li><a href="{{ data_get($pages ?? [], 'link', '') }}">{{ data_get($pages ?? [], 'title', '') }}</a></li>
                                        {{ $else ?? '' }}
                                        <li><a href="#" class="current-page">{{ data_get($pages ?? [], 'title', '') }}</a></li>
                                        @endif
                                        @endforeach
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</form>
<script type="text/javascript">

    $('#sort-filter').on('change', function (e) {
        var $item = $(this).find(':selected');

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
</script>
@include('partials.footer')
