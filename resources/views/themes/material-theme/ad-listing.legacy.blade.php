@include('partials.header')

<div id="page-content">
    <div class="container">
        <ol class="breadcrumb bcstyle2">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li class="active">{{ $maincategory ?? '' }}{{ $subcategory ?? '' }}
                @if(($maincategory ?? "")($subcategory ?? "")=="") {{ __('quickad.all_categories') }} @endif</li>
        </ol>
        <a href="{{ $link['POST-AD'] ?? '#' }}" class="postadinner"><span> <i class="fa fa-plus-circle"></i> {{ __('quickad.post_ad') }}</span></a>

        <form method="get" name="locationForm" id="LocationForm">
            <div class="row">
                <div class="col-md-3 col-sm-3">
                    <aside class="sidebar">
                        <section><h2>{{ __('quickad.search_filter') }}</h2>

                            <div class="input-field">
                                <label>{{ __('quickad.what') }} ?</label>
                                <input type="text" name="keywords" value="{{ $keywords ?? '' }}">
                            </div>
                            <!--end input-field-->
                            <div class="form-group input-field tg-inputwithicon" id="country-popup">
                                <label for="inputStateCity">{{ __('quickad.where') }} ?</label>
                                <i class="fa fa-close" id="clear-city" style="display: none;color: #323232;"></i>
                                <input type="text" id="inputStateCity" name="location" autocomplete="off">
                                <div id="searchDisplay"></div>
                                <input type="hidden" name="placetype" id="searchPlaceType" value="">
                                <input type="hidden" name="placeid" id="searchPlaceId" value="">
                            </div>


                            <div>
                                <select name="cat" class="meterialselect">
                                    <option value="">{{ __('quickad.all_categories') }}</option>
                                    @foreach($category ?? [] as $category)
                                    <option value="{{ data_get($category ?? [], 'id', '') }}" {{ data_get($category ?? [], 'selected', '') }}>{{ data_get($category ?? [], 'name', '') }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!--end input-field-->
                            <div class="form-group">
                                <label>{{ __('quickad.price') }}</label>
                                <div class="inner">
                                    <div class="range-widget">
                                        <div class="range-inputs">
                                            <input type="text" name="range1" placeholder="{{ __('quickad.from') }}" value="{{ $range1 ?? '' }}">
                                            <input type="text" name="range2" placeholder="{{ __('quickad.to') }}" value="{{ $range2 ?? '' }}">
                                        </div>
                                        <!--<button type="submit"  name="Submit"><i class="fa fa-search"></i></button>-->
                                    </div>
                                </div>
                                <!--end price-slider-->
                            </div>
                            <!--end input-field-->


                            <div class="input-field">
                                <button type="submit" name="Submit" class="btn btn-primary pull-right">{{ __('quickad.search_now') }}<i class="fa fa-search"></i></button>
                            </div>
                            <!--end input-field-->
                        </section>
                    </aside>
                    <!--end sidebar-->
                </div>
                <!--end col-md-3-->
                <div class="col-md-9 col-sm-9">
                    <section>
                        <h2>{{ __('quickad.my_listings') }}</h2>
                        <section>
                            <form action="#" id="filterForm" method="get">
                                <div class="search-results-controls clearfix">
                                    <div class="pull-left">
                                        <span id="grid" class="circle-icon cursor-point active"><i class="fa fa-th icon-white"></i></span>
                                        <span id="list" class="circle-icon cursor-point"><i class="fa fa-bars"></i></span>
                                    </div>
                                    <input type="hidden" name="subcat" value="{{ $subcat ?? '' }}">
                                    <!--end left-->
                                    <div class="pull-right">
                                        <div class="input-group inputs-underline min-width-150px">
                                            <select class="meterialselect" name="limit" onchange="this.form.submit()">
                                                <option value="6">{{ __('quickad.limit_order') }}</option>
                                                <option value="10" @if(($limit ?? "")=="10") selected @endif >10</option>
                                                <option value="15" @if(($limit ?? "")=="15") selected @endif >15</option>
                                                <option value="20" @if(($limit ?? "")=="20") selected @endif >20</option>
                                                <option value="25" @if(($limit ?? "")=="25") selected @endif >25</option>
                                                <option value="30" @if(($limit ?? "")=="30") selected @endif >30</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!--end right-->
                                    <div class="pull-right mar-right-20">
                                        <div class="input-group inputs-underline min-width-150px">
                                            <select class="meterialselect" name="sort" onchange="this.form.submit()">
                                                <option value="">{{ __('quickad.sort_by') }}</option>
                                                <option value="title" @if(($sort ?? "")=="title") selected @endif >{{ __('quickad.name') }} </option>
                                                <option value="price" @if(($sort ?? "")=="price") selected @endif >{{ __('quickad.price') }} </option>
                                                <option value="date" @if(($sort ?? "")=="date") selected @endif >{{ __('quickad.date') }} </option>
                                            </select>
                                        </div>
                                    </div>
                                    @if(($post_premium_listing ?? "")=="0")
                                    <style>
                                        #premium_filter{ display: none !important;}
                                    </style>
                                    @endif
                                    <!--end right-->
                                    <div class="pull-right mar-right-20" id="premium_filter">
                                        <div class="input-group inputs-underline min-width-150px">
                                            <select class="meterialselect" name="filter" onchange="this.form.submit()">
                                                <option value="">{{ __('quickad.premium_ads') }}</option>
                                                <option value="free" @if(($filter ?? "")=="free") selected @endif >{{ __('quickad.free_ads') }}</option>
                                                <option value="urgent" @if(($filter ?? "")=="urgent") selected @endif >{{ __('quickad.urgent_ads') }}</option>
                                                <option value="featured" @if(($filter ?? "")=="featured") selected @endif >{{ __('quickad.featured_ads') }}</option>
                                                <option value="highlight" @if(($filter ?? "")=="highlight") selected @endif >{{ __('quickad.highlight_ads') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!--end right-->
                                </div>
                                <!--end search-results-controls-->
                            </form>
                        </section>
                        <section>
                            <!-- Subcategory -->
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <div class="tg-applyedfilters">
                                        <ul>
                                            @foreach($subcatlist ?? [] as $subcatlist)
                                                <li class="alert alert-dismissable fade in">
                                                    <a href="{{ data_get($subcatlist ?? [], 'link', '') }}"><span class="count"> {{ data_get($subcatlist ?? [], 'name', '') }} ({{ data_get($subcatlist ?? [], 'adcount', '') }})</span></a>
                                                </li>
                                            @endforeach

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- Subcategory -->
                        </section>
                        <section>
                            <div class="" id="serchlist">
                                <div class="searchresult grid hideresult" style="display: none;">
                                    <div class="row">
                                        @foreach($item ?? [] as $item)
                                        <div class="col-md-4 col-sm-4">
                                            <div class="item" data-id="{{ data_get($item ?? [], 'id', '') }}">
                                                <div class="premium">
                                                    @if((data_get($item ?? [], 'featured', ''))=="1") <span class="listing-box-premium featured">{{ __('quickad.featured') }}</span> @endif
                                                    @if((data_get($item ?? [], 'urgent', ''))=="1") <span class="listing-box-premium urgent">{{ __('quickad.urgent') }}</span> @endif
                                                    @if((data_get($item ?? [], 'highlight', ''))=="1") <span class="listing-box-premium highlight">{{ __('quickad.highlight') }}</span> @endif

                                                </div>
                                                <div class="ad-listing">
                                                    <div class="description">

                                                        <a href="{{ data_get($item ?? [], 'catlink', '') }}"><div class="label label-default">{{ data_get($item ?? [], 'category', '') }}</div></a>

                                                        <h3 title="{{ data_get($item ?? [], 'product_name', '') }}">
                                                            <a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
                                                            @if((data_get($item ?? [], 'sub_image', ''))!="")
                                                            <img src="{{ data_get($item ?? [], 'sub_image', '') }}" width="24px" alt="{{ data_get($item ?? [], 'sub_title', '') }}" title="{{ data_get($item ?? [], 'sub_title', '') }}"/>
                                                            @endif
                                                        </h3>
                                                        <h4>{{ data_get($item ?? [], 'location', '') }}</h4>
                                                    </div>
                                                    <!--end description-->
                                                    <div class="image bg-transfer">
                                                        <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}">
                                                    </div>
                                                    <!--end image-->
                                                </div>
                                                <div class="additional-info {{ data_get($item ?? [], 'highlight_bg', '') }}">
                                                    <ul class="icondetail">
                                                        <li><i class="fa fa-th-list"></i> {{ __('quickad.sub_category') }} :
                                                            <a title="{{ data_get($item ?? [], 'sub_category', '') }}" href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a>
                                                        </li>
                                                        <li><i class="fa fa-map-marker"></i> {{ __('quickad.location') }} : {{ data_get($item ?? [], 'city', '') }}, {{ data_get($item ?? [], 'country', '') }}</li>
                                                        <li><i class="fa fa-calendar"></i> {{ __('quickad.posted_on') }} : {{ data_get($item ?? [], 'created_at', '') }}</li>
                                                        <li><i class="fa fa-user"></i> {{ __('quickad.posted_by') }} : <a href="{{ data_get($item ?? [], 'author_link', '') }}" target="_blank">{{ data_get($item ?? [], 'username', '') }}</a></li>
                                                    </ul>

                                                    <div class="ad-footer-tags">
                                                        <div class="add-to-fav">
                                                            <a href="#" data-toggle="tooltip" data-placement="top" data-original-title='@if((data_get($item ?? [], 'favorite', ''))=="1") {{ __('quickad.remove_favourite') }} @endif
                                                            @if((data_get($item ?? [], 'favorite', ''))!="1") {{ __('quickad.add_favourite') }} @endif' data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd" class="fav_{{ data_get($item ?? [], 'id', '') }}">
                                                            @if((data_get($item ?? [], 'favorite', ''))=="1") <i class="fa fa-heart"></i> @endif
                                                            @if((data_get($item ?? [], 'favorite', ''))!="1") <i class="fa fa-heart-o"></i> @endif
                                                            </a>
                                                        </div>
                                                        @if((data_get($item ?? [], 'price', ''))!="0") <div class="price-tag">{{ data_get($item ?? [], 'price', '') }}</div> @endif
                                                    </div>
                                                    <!--end controls-more-->
                                                </div>
                                                <!--end additional-info-->
                                            </div>
                                            <!--end item-->
                                        </div>
                                        <!--<end col-md-4-->
                                        @endforeach
                                    </div>
                                    <!--end row-->
                                </div>
                                <div class="searchresult list hideresult" style="display: none;">
                                    <div class="row">
                                        @foreach($item2 ?? [] as $item2)
                                        <div class="item item-row" data-id="{{ data_get($item2 ?? [], 'id', '') }}">
                                            <div class="premium">
                                                @if((data_get($item2 ?? [], 'featured', ''))=="1") <span class="listing-box-premium featured">{{ __('quickad.featured') }}</span> @endif
                                                @if((data_get($item2 ?? [], 'urgent', ''))=="1") <span class="listing-box-premium urgent">{{ __('quickad.urgent') }}</span> @endif
                                                @if((data_get($item2 ?? [], 'highlight', ''))=="1") <span class="listing-box-premium highlight">{{ __('quickad.highlight') }}</span> @endif

                                            </div>
                                            <div class="ad-listing">
                                                <div class="image bg-transfer">

                                                    <figure><a href="{{ data_get($item2 ?? [], 'catlink', '') }}"><div class="label-featured label label-default">{{ data_get($item2 ?? [], 'category', '') }}</div></a></figure>

                                                    <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item2 ?? [], 'picture', '') }}" alt="{{ data_get($item2 ?? [], 'product_name', '') }}">
                                                </div>

                                                <!--end image-->

                                                <div class="description {{ data_get($item2 ?? [], 'highlight_bg', '') }}">
                                                    <h3 title="{{ data_get($item2 ?? [], 'product_name', '') }}">
                                                        <a href="{{ data_get($item2 ?? [], 'link', '') }}">{{ data_get($item2 ?? [], 'product_name', '') }}</a>
                                                        @if((data_get($item2 ?? [], 'sub_image', ''))!="")
                                                        <img src="{{ data_get($item2 ?? [], 'sub_image', '') }}" width="24px" alt="{{ data_get($item2 ?? [], 'sub_title', '') }}" title="{{ data_get($item2 ?? [], 'sub_title', '') }}"/>
                                                        @endif
                                                    </h3>
                                                    <ul class="icondetail">
                                                        <li><i class="fa fa-th-list"></i> {{ __('quickad.sub_category') }} :
                                                            <a title="{{ data_get($item2 ?? [], 'sub_category', '') }}" href="{{ data_get($item2 ?? [], 'subcatlink', '') }}">{{ data_get($item2 ?? [], 'sub_category', '') }}</a>
                                                        </li>
                                                        <li><i class="fa fa-map-marker"></i> {{ __('quickad.location') }} : {{ data_get($item2 ?? [], 'city', '') }}, {{ data_get($item2 ?? [], 'country', '') }}</li>
                                                        <li><i class="fa fa-calendar"></i> {{ __('quickad.posted_on') }} : {{ data_get($item2 ?? [], 'created_at', '') }}</li>
                                                        <li><i class="fa fa-user"></i> {{ __('quickad.posted_by') }} : <a href="{{ data_get($item2 ?? [], 'author_link', '') }}" target="_blank">{{ data_get($item2 ?? [], 'username', '') }}</a></li>
                                                    </ul>
                                                    @if((data_get($item2 ?? [], 'showtag', ''))=="1")
                                                    <ul class="tags">
                                                        {{ data_get($item2 ?? [], 'tag', '') }}
                                                    </ul>
                                                    @endif
                                                    <div class="ad-footer-tags">
                                                        <div class="add-to-fav">
                                                            <a href="#" data-toggle="tooltip" data-placement="top" data-original-title='@if((data_get($item2 ?? [], 'favorite', ''))=="1") {{ __('quickad.remove_favourite') }} @endif
                                                            @if((data_get($item2 ?? [], 'favorite', ''))!="1") {{ __('quickad.add_favourite') }} @endif' data-item-id="{{ data_get($item2 ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="setFavAd" class='fav_{{ data_get($item2 ?? [], 'id', '') }}'>
                                                            @if((data_get($item2 ?? [], 'favorite', ''))=="1") <i class="fa fa-heart"></i> @endif
                                                            @if((data_get($item2 ?? [], 'favorite', ''))!="1") <i class="fa fa-heart-o"></i> @endif
                                                            </a>
                                                        </div>
                                                        @if((data_get($item2 ?? [], 'price', ''))!="0") <div class="price-tag">{{ data_get($item2 ?? [], 'price', '') }}</div> @endif
                                                    </div>
                                                </div>
                                                <!--end description-->

                                            </div>

                                        </div>
                                        <!--end item.row-->
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </section>

                        @if(($adsfound ?? "")=="0")
                        <section><h4>:( No ads found.</h4></section>
                        @endif

                        <section>
                            <div class="center">
                                <ul class="pagination center">
                                    @foreach($pages ?? [] as $pages)
                                    @if((data_get($pages ?? [], 'current', ''))=="0") <li><a href="{{ data_get($pages ?? [], 'link', '') }}">{{ data_get($pages ?? [], 'title', '') }}</a> </li>@endif
                                    @if((data_get($pages ?? [], 'current', ''))=="1") <li class="active"> <a>{{ data_get($pages ?? [], 'title', '') }}</a> </li>@endif
                                    @endforeach
                                </ul>
                            </div>
                        </section>

                    </section>
                </div>
                <!--end col-md-9-->
            </div>
        </form>
        <!--end row-->
    </div>
    <!--end container-->
</div>
<!--end page-content-->
<script type="text/javascript">
    $(document).ready(function () {
        $(".current").addClass("active");
        if ($('.getParent').length > 0) {
            $('.getParent').parent().addClass('in');
        }
    });


</script>

@include('partials.footer')