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

                <div class="notify-box margin-top-15" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                   <span class="font-weight-600">{{ $adsfound ?? '' }} {{ __('quickad.ads_found') }}</span>

                    <div style="display:flex; align-items:center; gap:12px;">
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
                        <!-- View Toggle -->
                        <div class="view-toggle-btns" style="display:flex; gap:4px;">
                            <button type="button" id="btn-grid-view" class="view-toggle-btn active" title="Grid View" style="background:#8b0000;color:#fff;border:none;border-radius:6px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
                            </button>
                            <button type="button" id="btn-list-view" class="view-toggle-btn" title="List View" style="background:#f0f0f0;color:#555;border:none;border-radius:6px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><rect x="1" y="2" width="14" height="2.5" rx="1"/><rect x="1" y="6.75" width="14" height="2.5" rx="1"/><rect x="1" y="11.5" width="14" height="2.5" rx="1"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- GRID VIEW -->
                <div id="listings-grid-view" class="listings-container grid-layout margin-top-35">
                    <div class="row" style="width:100%;">
                    @foreach($item ?? [] as $item)
                        <div class="col-xl-3 col-lg-4 col-sm-6 col-12">
                            <div class='feat_property @if((data_get($item ?? [], 'highlight', ''))=="1") highlight @endif'>
                                <div class="thumb">
                                    <img class="img-whp lazy-load" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" data-original="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}">
                                    <div class="thmb_cntnt">
                                        <ul class="tag mb0">
                                            @if((data_get($item ?? [], 'featured', ''))=="1") <li class="list-inline-item featured"><a href="#">{{ __('quickad.featured') }}</a></li> @endif
                                            @if((data_get($item ?? [], 'urgent', ''))=="1") <li class="list-inline-item urgent"><a href="#">{{ __('quickad.urgent') }}</a></li> @endif
                                            @if((data_get($item ?? [], 'condition', ''))!="")
                                                <li class="list-inline-item condition-badge"><a href="#">{{ data_get($item ?? [], 'condition', '') }}</a></li>
                                            @endif
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
                                        <p><i class="la la-map-marker"></i> {{ data_get($item ?? [], 'city', '') }}@if(data_get($item ?? [], 'state', '')), {{ data_get($item ?? [], 'state', '') }}@endif</p>
                                    </div>
                                    <div class="listing-footer">
                                        <a class="author-link" href="{{ $link['PROFILE'] ?? '#' }}/{{ data_get($item ?? [], 'username', '') }}"><i class="fa fa-user" aria-hidden="true"></i> {{ data_get($item ?? [], 'username', '') }}</a>
                                        <span class="fav-icon set-item-fav @if(data_get($item ?? [], 'favorite', '')) added @endif" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd" style="cursor:pointer;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>

                <!-- LIST VIEW -->
                <div id="listings-list-view" class="listings-container margin-top-35" style="display:none;">
                    @foreach($item ?? [] as $item)
                    <div class="job-listing @if((data_get($item ?? [], 'highlight', ''))=="1") highlight @endif">
                        <div class="job-listing-details">
                            <div class="job-listing-company-logo">
                                <img class="lazy-load" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC" data-original="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}">
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
                            <span class="fav-icon set-item-fav @if(data_get($item ?? [], 'favorite', '')) added @endif" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd"></span>
                        </div>
                    </div>
                    {ITEM.banner-ad-list-view}
                    @endforeach
                </div>

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

    // Grid / List View Toggle
    (function() {
        var savedView = localStorage.getItem('esawda_listing_view') || 'grid';

        function setView(view) {
            if (view === 'list') {
                $('#listings-grid-view').hide();
                $('#listings-list-view').show();
                $('#btn-grid-view').css({ background: '#f0f0f0', color: '#555' });
                $('#btn-list-view').css({ background: '#8b0000', color: '#fff' });
            } else {
                $('#listings-list-view').hide();
                $('#listings-grid-view').show();
                $('#btn-grid-view').css({ background: '#8b0000', color: '#fff' });
                $('#btn-list-view').css({ background: '#f0f0f0', color: '#555' });
            }
            localStorage.setItem('esawda_listing_view', view);
        }

        setView(savedView);

        $('#btn-grid-view').on('click', function() { setView('grid'); });
        $('#btn-list-view').on('click', function() { setView('list'); });
    })();

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
