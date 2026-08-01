@include('partials.header')
<div id="titlebar" class="margin-bottom-0">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.my_ads') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.my_ads') }}</li>
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
                                        <li class="active"><a href="{{ $link['MYADS'] ?? '#' }}"><i class="icon-feather-briefcase"></i> {{ __('quickad.my_ads') }} <span class="nav-tag">{{ $myads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['FAVADS'] ?? '#' }}"><i class="icon-feather-heart"></i> {{ __('quickad.favourite_ads') }} <span class="nav-tag">{{ $favoriteads ?? '' }}</span></a></li>

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
                        <h3><i class="icon-feather-briefcase"></i> {{ __('quickad.my_ads') }}</h3>
                    </div>
                    <div class="content with-padding">
                        <div class="table-responsive">
                            <table id="js-table-list" class="basic-table dashboard-box-list">
                                <tr>
                                    <th class="big-width">{{ __('quickad.ads') }}</th>
                                    <th class="small-width">{{ __('quickad.status') }}</th>
                                    <th class="small-width">{{ __('quickad.actions') }}</th>
                                </tr>
                                @if(($adsfound ?? ""))
                                @foreach($item ?? [] as $item)
                                    <tr class="ajax-item-listing @if('{{ data_get($item ?? [], 'hide', '') }}'=='1') opapcityLight @endif" data-item-id="{{ data_get($item ?? [], 'id', '') }}">
                                        <td>
                                            <div class="job-listing">
                                                <div class="job-listing-details">
                                                    <div class="job-listing-description">
                                                        <div class="job-listing-company-logo company-logo-myads">
                                                            <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="{{ data_get($item ?? [], 'product_name', '') }}">
                                                        </div>
                                                        <div>
                                                            <h3 class="job-listing-title margin-bottom-5">
                                                                <a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
                                                                <label class="label-wrap hidden-sm hidden-xs margin-zero d-inline-block">
                                                                    @if((data_get($item ?? [], 'featured', ''))=="1") <span class="badge blue"> {{ __('quickad.featured') }}</span> @endif
                                                                    @if((data_get($item ?? [], 'urgent', ''))=="1") <span class="badge yellow"> {{ __('quickad.urgent') }}</span> @endif
                                                                    @if((data_get($item ?? [], 'highlight', ''))=="1") <span class="badge red"> {{ __('quickad.highlight') }}</span> @endif
                                                                </label>
                                                            </h3>
                                                            <ol class="breadcrumb">
                                                                <li><a href="{{ data_get($item ?? [], 'catlink', '') }}"><i class="la la-tags"></i> {{ data_get($item ?? [], 'category', '') }}</a></li>
                                                                <li><a href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a></li>
                                                            </ol>
                                                            <div class="job-listing-footer font-14">
                                                                <ul>
                                                                    <li><i class="la la-map-marker"></i> {{ data_get($item ?? [], 'location', '') }}</li>
                                                                    <li><i class="la la-calendar-times-o"></i> {{ __('quickad.expiring') }}: {{ data_get($item ?? [], 'expire_date', '') }}</li>
                                                                </ul>
                                                                @if((data_get($item ?? [], 'price', ''))!="0")
                                                                <span class="table-property-price">{{ data_get($item ?? [], 'price', '') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td width="12%">
                                            @if((data_get($item ?? [], 'status', ''))=="active") <span class="badge green">{{ __('quickad.active') }}</span>@endif
                                            @if((data_get($item ?? [], 'status', ''))=="pending") <span class="badge blue">{{ __('quickad.pending') }}</span> @endif
                                            @if((data_get($item ?? [], 'status', ''))=="rejected") <span class="badge red">{{ data_get($item ?? [], 'status', '') }}</span> @endif
                                            @if((data_get($item ?? [], 'status', ''))=="expire") <span class="badge yellow">{{ __('quickad.expire') }}</span> @endif
                                            @if((data_get($item ?? [], 'hide', ''))=="1") <span class="badge red label-hidden">{{ __('quickad.hidden') }}</span> @endif
                                        </td>
                                        <td width="12%">
                                            <a href="{{ $link['EDIT-AD'] ?? '#' }}/{{ data_get($item ?? [], 'id', '') }}" class="button gray ripple-effect ico" data-tippy-placement="top" title="{{ __('quickad.edit') }}"><i class="icon-feather-edit"></i></a>

                                            <a class="button gray ripple-effect ico item-js-hide" href="#" data-ajax-action="hideItem"  data-tippy-placement="top"
                                                @if('{{ data_get($item ?? [], 'hide', '') }}'=='0') title="{{ __('quickad.hide') }}" {{ $else ?? '' }} title="{{ __('quickad.show') }}" @endif >
                                                @if('{{ data_get($item ?? [], 'hide', '') }}'=='0') <i class="fa  fa-eye-slash"></i> {{ $else ?? '' }} <i class="fa  fa-eye"></i> @endif</a>

                                            <a href="#" data-ajax-action="deleteMyAd" class="button gray ripple-effect ico item-js-delete" data-tippy-placement="top" title="{{ __('quickad.delete') }}"><i class="icon-feather-trash-2"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                {{ $else ?? '' }}
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('quickad.no_found') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
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
</div>
@include('partials.footer')
