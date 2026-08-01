@include('partials.header')
<!-- myads-page -->
<section id="main" class="clearfix myads-page">
    <div class="container">

        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.favourite_ads') }}</li>
                <div class="pull-right back-result">
                    <a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i> {{ __('quickad.back_result') }}
                    </a>
                </div>
            </ol>
            <!-- breadcrumb -->
        </div>
        <!-- banner -->
        <div class="ads-info">
            <div class="row">
                <!-- Page-Sidebar -->
                <aside class="col-sm-3 hidden-xs hidden-sm">
                    <div class="section">
                        <div class="user-panel-sidebar">
                            <div class="collapse-box">
                                <h5 class="collapse-title no-border">{{ __('quickad.my_classified') }} <a class="pull-right" data-toggle="collapse" href="#MyClassified"><i class="fa fa-angle-down"></i></a></h5>
                                <div id="MyClassified" class="panel-collapse collapse in">
                                    <ul class="acc-list">
                                        <li><a href="{{ $link['DASHBOARD'] ?? '#' }}" class="waves-effect"><i class="fa fa-home"></i>{{ __('quickad.dashboard') }} </a></li>
                                        <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}" class="waves-effect"><i class="fa fa-user"></i> {{ __('quickad.profile_public') }}</a></li>
                                        <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="waves-effect"><i class="fa fa-pencil"></i>{{ __('quickad.post_ad') }}</a></li>
                                        <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}" class="waves-effect"><i class="fa fa-shopping-bag"></i> {{ __('quickad.membership') }} </a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="collapse-box">
                                <h5 class="collapse-title"> {{ __('quickad.my_ads') }} <a class="pull-right" data-toggle="collapse" href="#MyAds"><i class="fa fa-angle-down"></i></a></h5>
                                <div id="MyAds" class="panel-collapse collapse in">
                                    <ul class="acc-list">
                                        <li><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-book"></i>{{ __('quickad.my_ads') }} <span class="badge">{{ $myads ?? '' }}</span> </a></li>
                                        <li class="active"><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-heart"></i>{{ __('quickad.favourite_ads') }} <span class="badge">{{ $favoriteads ?? '' }}</span> </a></li>
                                        <li><a href="{{ $link['PENDINGADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-info-circle"></i> {{ __('quickad.pending_ads') }}<span class="badge">{{ $pendingads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['HIDDENADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-eye-slash"></i> {{ __('quickad.hidden_ads') }} <span class="badge">{{ $hiddenads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['EXPIREADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-calendar-times-o"></i> {{ __('quickad.expire_ads') }} <span class="badge">{{ $expireads ?? '' }}</span></a>
                                        <li><a href="{{ $link['RESUBMITADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-briefcase"></i> {{ __('quickad.resubmited_ads') }} <span class="badge">{{ $resubmitads ?? '' }}</span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="collapse-box">
                                <h5 class="collapse-title no-border"> {{ __('quickad.my_account') }} <a class="pull-right" data-toggle="collapse" href="#account"><i class="fa fa-angle-down"></i></a></h5>
                                <div id="account" class="panel-collapse collapse in">
                                    <ul class="acc-list">
                                        <li><a href="{{ $link['TRANSACTION'] ?? '#' }}" class="waves-effect"><i class="fa fa-money"></i> {{ __('quickad.transaction') }}</a></li>
                                        <li><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}" class="waves-effect"><i class="fa fa-cog"></i> {{ __('quickad.account_setting') }} </a></li>
                                        <li><a href="{{ $link['LOGOUT'] ?? '#' }}" class="waves-effect"><i class="fa fa-unlock"></i>{{ __('quickad.logout') }} </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
                <!-- # End Page-Sidebar -->
                <!-- my-quickad -->
                <div class="col-sm-9">
                    <div class="my-quickad section">
                        <div class="row">
                            <div class="col-md-5">
                                <h2>{{ __('quickad.favourite_ads') }}</h2>
                            </div>
                            <div class="col-md-7">
                            </div>
                        </div>
                        <div  id="serchlist">
                            @foreach($item ?? [] as $item)<!-- quick-item -->
                            <div class='quick-item row @if((data_get($item ?? [], 'highlight', ''))=="1") highlight @endif' ><!-- item-image -->
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
                                    <div class="item-info col-sm-12"><!-- ad-info -->
                                        <div class="ad-info">
                                            <h4 class="item-title"><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
                                            </h4>
                                            <ul class="contact-options pull-right" id="set-favorite">
                                                <li><a href="#" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}"
                                                       data-action="removeFavAd" class="fav_{{ data_get($item ?? [], 'id', '') }} fa fa-heart @if("
                                                    {{ data_get($item ?? [], 'favorite', '') }}"=="1") active @endif"></a></li>
                                            </ul>
                                            <ol class="breadcrumb">
                                                <li><a href="{{ data_get($item ?? [], 'catlink', '') }}">{{ data_get($item ?? [], 'category', '') }}</a></li>
                                                <li><a href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a></li>
                                            </ol>
                                            <ul class="item-details">
                                                <li><i class="fa fa-map-marker"></i><a href="{{ data_get($item ?? [], 'citylink', '') }}">{{ data_get($item ?? [], 'city', '') }}</a>
                                                </li>
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



                    <div class="clearfix"></div>

                    @if(($totalitem ?? "")=="0")
                    <div class="alert alert-info">
                        <a href="#" class="alert-link">{{ __('quickad.no_ad_favourite') }}</a>.
                    </div>
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
            <!-- my-quickad -->
        </div>
        <!-- row -->
    </div>
    <!-- row -->
    </div><!-- container -->
</section>
<!-- myads-page -->
<script>
    var loginurl = "{{ $link['LOGIN'] ?? '#' }}?ref=favourite-ads.php";
</script>
@include('partials.footer')


