@include('partials.header')
<form method="get" action="{{ $link['LISTING'] ?? '#' }}" name="locationForm" id="ListingForm">
    <div id="titlebar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="agent agent-page long-content">
                        <div class="agent-avatar">
                            <img class="lazy-load" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ $site_url ?? '' }}storage/profile/{{ $userimage ?? '' }}" alt="{{ $fullname ?? '' }}">
                        </div>

                        <div class="agent-content">
                            <div class="agent-name">
                                <h4>{{ $fullname ?? '' }}
                                    @if(($sub_image ?? "")!="")
                                    <img src="{{ $sub_image ?? '' }}" alt="{{ $sub_title ?? '' }}" title="{{ $sub_title ?? '' }}" width="28px"/>
                                    @endif
                                </h4>
                                <span>@{{ $username ?? '' }}</span>
                            </div>
                            <div class="agent-about">{{ $about ?? '' }}</div>
                            <ul class="agent-contact-details">
                                @if(($address ?? "")!="")
                                    <li><i class="icon-feather-map-pin"></i><a href="https://maps.google.com/?q={{ $address ?? '' }}" target="_blank" rel="nofollow">{{ $address ?? '' }}</a></li>
                                @endif
                                @if(($phone ?? "")!="")
                                    <li class="tg-btnphone"><i class="icon-feather-phone-call"></i><span data-last="{{ $phone ?? '' }}"><a href="tel:{{ $phone ?? '' }}" rel="nofollow"><em>{{ __('quickad.show_phone_no') }}</em></a></span>
                                        @if(($phone_verify ?? "")=="1")
                                        <img src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/images/verified-badge.png" data-tippy-placement="top" title="{{ __('quickad.verified') }}" width="16px"/>
                                        @endif
                                    </li>
                                @endif
                                @if(($email ?? "")!="")
                                    <li class="tg-btnphone"><i class="icon-feather-mail"></i><span data-last="{{ $email ?? '' }}"><a href="mailto:{{ $email ?? '' }}" rel="nofollow"><em>{{ __('quickad.show_email') }}</em></a></span></li>
                                @endif
                                @if(($website ?? "")!="")
                                    <li><i class="icon-feather-globe"></i><a href="{{ $website ?? '' }}" target="_blank" rel="nofollow">{{ $website ?? '' }}</a></li>
                                @endif
                            </ul>

                            <div class="freelancer-socials margin-top-25">
                                <ul>
                                    @if(($facebook ?? "")!="")  <li><a href="{{ $facebook ?? '' }}" target="_blank" class="facebook"><i class="la la-facebook"></i></a></li> @endif
                                    @if(($twitter ?? "")!="")  <li><a href="{{ $twitter ?? '' }}" target="_blank" class="twitter"><i class="la la-twitter"></i></a></li>@endif
                                    @if(($gplus ?? "")!="")  <li><a href="{{ $gplus ?? '' }}" target="_blank" class="google"><i class="la la-pinterest"></i></a></li>@endif
                                    @if(($linkedin ?? "")!="")  <li><a href="{{ $linkedin ?? '' }}" target="_blank" class="linkden"><i class="la la-linkedin"></i></a></li>@endif
                                    @if(($instagram ?? "")!="")  <li><a href="{{ $instagram ?? '' }}" target="_blank" class="instagram"><i class="la la-instagram"></i></a></li>@endif
                                    @if(($youtube ?? "")!="")  <li><a href="{{ $youtube ?? '' }}" target="_blank" class="youtube"><i class="la la-youtube"></i></a></li>@endif
                                </ul>
                                <!--end social-->
                            </div>

                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div>
                <div class="col-md-12">
                    {{ $ad_profile_page_below_user_section ?? '' }}
                </div>
                <div class="col-md-12">
                    <h2>{{ __('quickad.we_found') }} {{ $adsfound ?? '' }} {{ __('quickad.ads_listings') }}</h2>
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
                            <input id="keywords" type="text" name="keywords" placeholder="{{ __('quickad.what_look_for') }}" value="{{ $keywords ?? '' }}">
                        </div>
                        <div class="intro-search-field with-autocomplete">
                            <div class="input-with-icon">
                                <input type="text" id="searchStateCity" name="location" placeholder="{{ __('quickad.where') }}">
                                <i class="la la-map-marker"></i>
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
                </div>
            </div>
        </div>
    </div>
    <a class="popup-with-zoom-anim hidden" href="#citiesModal" id="change-city">city</a>
    <div class="zoom-anim-dialog mfp-hide popup-dialog big-dialog" id="citiesModal">
        <div class="popup-tab-content padding-0">
            <div class="quick-states" id="country-popup" data-country-id="{{ $default_country_id ?? '' }}" style="display: block;">
            <div id="regionSearchBox" class="title clr">
                <div class="clr">
                    <div class="locationrequest smallBox br5 col-sm-4">
                        <div class="rel input-container">
                            <div class="input-with-icon">
                                <input id="inputStateCity" class="with-border" type="text" placeholder="{{ __('quickad.type_your_city') }}">
                                <i class="la la-map-marker"></i>
                            </div>
                            <div id="searchDisplay"></div>
                            <div class="suggest bottom abs small br3 error hidden"><span
                                        class="target abs icon"></span>

                                <p></p>
                            </div>
                        </div>
                        <div id="lastUsedCities" class="last-used binded" style="display: none;">{{ __('quickad.last_visited') }}
                            <ul id="last-locations-ul">
                            </ul>
                        </div>
                    </div>
                    @if(($country_type ?? "")=="multi")
                    <span style="line-height: 30px;">
                        <span class="flag flag-{{ $user_country ?? '' }}"></span> <a href="#countryModal" class="popup-with-zoom-anim">{{ __('quickad.change_country') }}</a>
                    </span>
                    @endif
                </div>
            </div>
            <div class="popular-cities clr">
                <p>{{ __('quickad.popular_cities') }}</p>

                <div class="list row">

                    <ul class="col-lg-12 col-md-12 popularcity">
                        @foreach($popularcity ?? [] as $popularcity)
                        {{ data_get($popularcity ?? [], 'tpl', '') }}
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="viewport">
                <div class="full" id="getCities">
                    <div class="col-sm-12 col-md-12 loader" style="display: none"></div>
                    <div id="results" class="animate-bottom">
                        <ul class="column cities">
                            @foreach($statelist ?? [] as $statelist)
                            {{ data_get($statelist ?? [], 'tpl', '') }}
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="table full subregionslinks hidden" id="subregionslinks"></div>
            </div>
        </div>
        </div>
    </div>
    <div class="container margin-bottom-60">
        <div class="row">
            @if(($ad_profile_page_left_sidebar_status ?? "")=="1")
            <div class="hidden-xs hidden-sm col-md-2 text-center">
                <div class="advertisement" id="quickad-left">{{ $ad_profile_page_left_sidebar ?? '' }}</div>
            </div>
            @endif
            @if(($ad_profile_page_left_sidebar_status ?? "")=="1")
            <div class="col-md-8">
            {{ $else ?? '' }}
            <div class="col-md-12">
                @endif

                <h3 class="page-title">{{ __('quickad.search_results') }}</h3>

                <div class="notify-box margin-top-15">
                   <span class="font-weight-600">{{ $adsfound ?? '' }} {{ __('quickad.ads_found') }}</span>

                    <div class="sort-by">
                        <span>{{ __('quickad.sort_by') }}</span>
                        <select class="selectpicker hide-tick" id="sort-filter">
                            <option data-filter-type="sort" data-filter-val="id" data-order="desc">{{ __('quickad.newest') }}</option>
                            <option data-filter-type="sort" data-filter-val="title" data-order="desc">{{ __('quickad.name') }}</option>
                            <option data-filter-type="sort" data-filter-val="date" data-order="desc">{{ __('quickad.date') }}</option>
                            <option data-filter-type="sort" data-filter-val="price" data-order="desc">{{ __('quickad.salary') }} ({{ __('quickad.high_to_low') }})</option>
                            <option data-filter-type="sort" data-filter-val="price" data-order="asc">{{ __('quickad.salary') }} ({{ __('quickad.low_to_high') }})</option>
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
                            <div class="job-listing-footer">
                                <ul>
                                    <li><i class="la la-user"></i> <a href="{{ $link['PROFILE'] ?? '#' }}/{{ data_get($item ?? [], 'username', '') }}">{{ data_get($item ?? [], 'username', '') }}</a></li>
                                    <li><i class="la la-map-marker"></i> {{ data_get($item ?? [], 'location', '') }}</li>
                                    @if((data_get($item ?? [], 'price', ''))!="0")
                                    <li><i class="la la-credit-card"></i> {{ data_get($item ?? [], 'price', '') }}</li>
                                    @endif
                                    <li><i class="la la-clock-o"></i> {{ data_get($item ?? [], 'created_at', '') }}</li>
                                </ul>
                            </div>
                        </div>
                    @endforeach
                    <div class="clearfix"></div>
                    @if(($adsfound ?? "")!="0")
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Pagination -->
                            <div class="pagination-container margin-top-20">
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
             @if(($ad_profile_page_right_sidebar_status ?? "")=="1")
            <div class="hidden-xs hidden-sm col-md-2 text-center">
                <div class="advertisement" id="quickad-right">{{ $ad_profile_page_right_sidebar ?? '' }}</div>
            </div>
            @endif
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
