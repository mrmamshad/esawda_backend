@include('partials.header')
<div id="page-content">
    <div class="container">
        <ul class="breadcrumb bcstyle2">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li class="active"><a>{{ __('quickad.favourite_ads') }}</a></li>
        </ul>
        <a href="{{ $link['POST-AD'] ?? '#' }}" class="postadinner"><span> <i class="fa fa-plus-circle"></i> {{ __('quickad.post_ad') }}</span></a>
        <!--end breadcrumb-->

        <section>
            <div class="row mt40">
                <div class="col-sm-3 page-sidebar">
                    <aside>
                        <div class="inner-box">
                            <div class="user-panel-sidebar">
                                <div class="collapse-box">
                                    <h5 class="collapse-title no-border"> {{ __('quickad.my_classified') }} <a class="pull-right" data-toggle="collapse" href="#MyClassified"><i class="fa fa-angle-down"></i></a></h5>
                                    <div id="MyClassified" class="panel-collapse collapse in">
                                        <ul class="acc-list">
                                            <li><a href="{{ $link['DASHBOARD'] ?? '#' }}" class="waves-effect"><i class="fa fa-home"></i> {{ __('quickad.dashboard') }} </a></li>
                                            <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}" class="waves-effect"><i class="fa fa-user"></i> {{ __('quickad.profile_public') }}</a></li>
                                            <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="waves-effect"><i class="fa fa-pencil"></i> {{ __('quickad.post_ad') }}</a></li>
                                            <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}" class="waves-effect"><i class="fa fa-shopping-bag"></i> {{ __('quickad.membership') }} </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="collapse-box"><h5 class="collapse-title"> {{ __('quickad.my_ads') }} <a class="pull-right" data-toggle="collapse" href="#MyAds"><i class="fa fa-angle-down"></i></a></h5>

                                    <div id="MyAds" class="panel-collapse collapse in">
                                        <ul class="acc-list">
                                            <li><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-book"></i> {{ __('quickad.my_ads') }} <span class="badge">{{ $myads ?? '' }}</span> </a></li>
                                            <li class="active"><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-heart"></i> {{ __('quickad.favourite_ads') }} <span class="badge" id="favCount">{{ $favoriteads ?? '' }}</span> </a></li>
                                            <li><a href="{{ $link['PENDINGADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.pending_ads') }} <span class="badge">{{ $pendingads ?? '' }}</span></a></li>
                                            <li><a href="{{ $link['HIDDENADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.hidden_ads') }} <span class="badge">{{ $hiddenads ?? '' }}</span></a></li>
                                            <li><a href="{{ $link['EXPIREADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-calendar-times-o"></i> {{ __('quickad.expire_ads') }} <span class="badge">{{ $expireads ?? '' }}</span></a>
                                            <li><a href="{{ $link['RESUBMITADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.resubmited_ads') }} <span class="badge">{{ $resubmitads ?? '' }}</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="collapse-box">
                                    <h5 class="collapse-title no-border"> {{ __('quickad.my_account') }} <a class="pull-right" data-toggle="collapse" href="#account"><i class="fa fa-angle-down"></i></a></h5>
                                    <div id="account" class="panel-collapse collapse in">
                                        <ul class="acc-list">
                                            <li><a href="{{ $link['TRANSACTION'] ?? '#' }}" class="waves-effect"><i class="fa fa-money"></i> {{ __('quickad.transaction') }}</a></li>
                                            <li class="active"><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}" class="waves-effect"><i class="fa fa-cog"></i> {{ __('quickad.account_setting') }} </a></li>
                                            <li><a href="{{ $link['LOGOUT'] ?? '#' }}" class="waves-effect"><i class="fa fa-unlock"></i> {{ __('quickad.logout') }} </a></li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>

                <div class="col-md-9 col-sm-9">
                    <section>
                        <section class="page-title"><h1>{{ __('quickad.my_favourite_ads') }}</h1></section>

                        <section>
                            <div>
                                <div class="searchresult list hideresult" style="display: block;">
                                    <div class="row" id="serchlist">
                                        @foreach($item ?? [] as $item)
                                        <div class="quick-item item item-row ajax-item-listing" data-id="{{ data_get($item ?? [], 'id', '') }}">
                                            <div class="premium">
                                                @if((data_get($item ?? [], 'featured', ''))=="1") <span class="listing-box-premium featured">{{ __('quickad.featured') }}</span> @endif
                                                @if((data_get($item ?? [], 'urgent', ''))=="1") <span class="listing-box-premium urgent">{{ __('quickad.urgent') }}</span>@endif
                                                @if((data_get($item ?? [], 'highlight', ''))=="1") <span class="listing-box-premium highlight">{{ __('quickad.highlight') }}</span> @endif

                                            </div>
                                            <div class="ad-listing">
                                                <div class="image bg-transfer">

                                                    <figure><a href="{{ data_get($item ?? [], 'catlink', '') }}"><div class="label-featured label label-default">{{ data_get($item ?? [], 'category', '') }}</div></a></figure>

                                                    <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}">
                                                </div>

                                                <!--end image-->

                                                <div class="description">
                                                    <h3 title="{{ data_get($item ?? [], 'product_name', '') }}">
                                                        <a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
                                                    </h3>
                                                    <ul class="icondetail">
                                                        <li><i class="fa fa-th-list"></i> {{ __('quickad.sub_category') }} :
                                                            <a title="{{ data_get($item ?? [], 'sub_category', '') }}" href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a>
                                                        </li>
                                                        <li><i class="fa fa-map-marker"></i> {{ __('quickad.location') }} : {{ data_get($item ?? [], 'city', '') }}, {{ data_get($item ?? [], 'country', '') }}</li>
                                                        <li><i class="fa fa-calendar"></i> {{ __('quickad.posted_on') }} : {{ data_get($item ?? [], 'created_at', '') }}</li>
                                                        <li><i class="fa fa-user"></i> {{ __('quickad.posted_by') }} : <a href="{{ data_get($item ?? [], 'author_link', '') }}" target="_blank">{{ data_get($item ?? [], 'username', '') }}</a></li>
                                                    </ul>
                                                    @if((data_get($item ?? [], 'showtag', ''))=="1")
                                                    <ul class="tags">
                                                        {{ data_get($item ?? [], 'tag', '') }}
                                                    </ul>
                                                    @endif
                                                    <div class="ad-footer-tags">
                                                        <div class="add-to-fav">
                                                            <a href="#" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('quickad.remove_favourite') }}" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="removeFavAd" class="fav_{{ data_get($item ?? [], 'id', '') }}">
                                                                <i class="fa fa-heart"></i>
                                                            </a>
                                                        </div>

                                                        @if((data_get($item ?? [], 'price', ''))!="0") <div class="price-tag">{{ data_get($item ?? [], 'price', '') }}</div>@endif
                                                    </div>
                                                </div>
                                                <!--end description-->

                                            </div>

                                        </div>
                                        <!--end ITEM.row-->
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </section>
                        @if(($totalitem ?? "")=="0")
                        <div class="alert alert-info">
                            <a href="#" class="alert-link">{{ __('quickad.no_ad_favourite') }}</a>.
                        </div>
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
            </div>
        </section>

    </div>
    <!--end container-->
</div>
<script>
    var loginurl = "{{ $link['LOGIN'] ?? '#' }}?ref=favourite-ads.php";
</script>
@include('partials.footer')


