@include('partials.header')
<div id="titlebar" class="margin-bottom-0">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.favourite_ads') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.favourite_ads') }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="section gray padding-bottom-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <div class="dashboard-sidebar">
                    <div class="dashboard-sidebar-inner">
                        <div class="dashboard-nav-container">
                            <!-- Responsive Navigation Trigger -->
                            <a href="#" class="dashboard-responsive-nav-trigger">
                                <span class="hamburger hamburger--collapse" >
                                    <span class="hamburger-box">
                                        <span class="hamburger-inner"></span>
                                    </span>
                                </span>
                                <span class="trigger-title">{{ __('quickad.dash_navigation') }}</span>
                            </a>

                            <div class="dashboard-nav">
                                <div class="dashboard-nav-inner">
                                    <ul data-submenu-title="{{ __('quickad.my_classified') }}">
                                        <li><a href="{{ $link['DASHBOARD'] ?? '#' }}"><i class="icon-feather-grid"></i> {{ __('quickad.dashboard') }}</a></li>
                                        <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}"><i class="icon-feather-user"></i> {{ __('quickad.profile_public') }}</a></li>
                                        <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}"><i class="icon-feather-gift"></i> {{ __('quickad.membership') }}</a></li>
                                    </ul>

                                    <ul data-submenu-title="{{ __('quickad.my_ads') }}">
                                        <li><a href="{{ $link['MYADS'] ?? '#' }}"><i class="icon-feather-briefcase"></i> {{ __('quickad.my_ads') }} <span class="nav-tag">{{ $myads ?? '' }}</span></a></li>
                                        <li class="active"><a href="{{ $link['FAVADS'] ?? '#' }}"><i class="icon-feather-heart"></i> {{ __('quickad.favourite_ads') }} <span class="nav-tag">{{ $favoriteads ?? '' }}</span></a></li>

                                        <li><a href="{{ $link['PENDINGADS'] ?? '#' }}"><i class="icon-feather-clock"></i> {{ __('quickad.pending_ads') }} <span class="nav-tag">{{ $pendingads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['HIDDENADS'] ?? '#' }}"><i class="icon-feather-eye-off"></i> {{ __('quickad.hidden_ads') }} <span class="nav-tag">{{ $hiddenads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['EXPIREADS'] ?? '#' }}"><i class="icon-feather-alert-octagon"></i> {{ __('quickad.expire_ads') }} <span class="nav-tag">{{ $expireads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['RESUBMITADS'] ?? '#' }}"><i class="icon-feather-rotate-cw"></i> {{ __('quickad.resubmited_ads') }} <span class="nav-tag">{{ $resubmitads ?? '' }}</span></a></li>
                                    </ul>

                                    <ul data-submenu-title="{{ __('quickad.my_account') }}">
                                        @if(($wchat_on_off ?? "")=='on' || ($quickchat_ajax_on_off ?? "")=='on' || ($quickchat_socket_on_off ?? "")=='on')
                                        <li><a href="{{ $link['MESSAGE'] ?? '#' }}"><i class="icon-feather-message-circle"></i> {{ __('quickad.message') }}</a></li>
                                        @endif
                                        <li><a href="{{ $link['TRANSACTION'] ?? '#' }}"><i class="icon-feather-file-text"></i> {{ __('quickad.transaction') }}</a></li>
                                        <li><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}"><i class="icon-feather-settings"></i> {{ __('quickad.account_setting') }}</a></li>
                                        <li><a href="{{ $link['LOGOUT'] ?? '#' }}"><i class="icon-feather-log-out"></i> {{ __('quickad.logout') }}</a></li>
                                    </ul>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
                {{ $ad_inner_page_sidebar ?? '' }}
        </div>
        <div class="col-lg-9 col-md-12">
            <div class="dashboard-box margin-top-0">
                <!-- Headline -->
                <div class="headline">
                    <h3><i class="icon-feather-heart"></i> {{ __('quickad.favourite_ads') }}</h3>
                </div>
                @if(!($totalitem ?? ""))
                <div class="content with-padding text-center">
                    {{ __('quickad.no_found') }}
                </div>
                @endif
            </div>
            <div class="listings-container margin-top-30">
                @foreach($item ?? [] as $item)
                <div class="job-listing fav-listing">
                    <div class="job-listing-details">
                        <div class="job-listing-company-logo">
                            <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}">
                        </div>
                        <div class="job-listing-description">
                            <h4 class="job-listing-company">
                                <a href="{{ data_get($item ?? [], 'catlink', '') }}"><i class="la la-tags"></i> {{ data_get($item ?? [], 'category', '') }}</a>
                                -
                                <a href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a>
                            </h4>
                            <h3 class="job-listing-title"><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a></h3>
                            <p class="job-listing-text">{{ data_get($item ?? [], 'desc', '') }}</p>
                        </div>
                        <!-- <span class="job-type"><a href="{{ data_get($item ?? [], 'catlink', '') }}"><i class="la la-tags"></i> {{ data_get($item ?? [], 'category', '') }}</a></span> -->
                    </div>
                    <div class="job-listing-footer with-icon">
                        <ul>
                            <li><i class="la la-map-marker"></i> {{ data_get($item ?? [], 'location', '') }}</li>
                            @if((data_get($item ?? [], 'price', ''))!="0")
                            <li><i class="la la-credit-card"></i> {{ data_get($item ?? [], 'price', '') }}</li>
                            @endif
                            <li><i class="la la-clock-o"></i> {{ data_get($item ?? [], 'created_at', '') }}</li>
                        </ul>
                        <span class="fav-icon added set-item-fav" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-userid="{{ $user_id ?? '' }}" data-action="removeFavAd"></span>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="clearfix"></div>
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
            </div>
        </div>
    </div>
</div>
</div>
@include('partials.footer')
