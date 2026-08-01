@include('partials.header')
<div id="titlebar" class="margin-bottom-0">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.current_plan') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.membership') }}</li>
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
                                        <li class="active"><a href="{{ $link['MEMBERSHIP'] ?? '#' }}"><i class="icon-feather-gift"></i> {{ __('quickad.membership') }}</a></li>
                                    </ul>

                                    <ul data-submenu-title="{{ __('quickad.my_ads') }}">
                                        <li><a href="{{ $link['MYADS'] ?? '#' }}"><i class="icon-feather-briefcase"></i> {{ __('quickad.my_ads') }} <span class="nav-tag">{{ $myads ?? '' }}</span></a></li>
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
            </div>
            <div class="col-lg-9 col-md-12">
                <div class="dashboard-box margin-top-0">
                    <!-- Headline -->
                    <div class="headline">
                        <h3><i class="icon-feather-gift"></i> {{ __('quickad.current_plan') }}</h3>
                    </div>
                    <div class="content with-padding">
                        <div class="table-responsive">
                            <table id="js-table-list" class="basic-table dashboard-box-list">
                                <tr>
                                    <th>{{ __('quickad.membership') }}</th>
                                    <th>{{ __('quickad.payment_mode') }}</th>
                                    <th>{{ __('quickad.start_date') }}</th>
                                    <th>{{ __('quickad.expiry_date') }}</th>
                                    @if(($show_cancel_button ?? "")=="1") <th>{{ __('quickad.cancel') }}</th>@endif
                                </tr>
                                <tr>
                                    <td>{{ $upgrade_title ?? '' }}</td>
                                    <td>
                                        @if(($payment_mode ?? "")=="one_time") {{ __('quickad.one_time') }} {{ $else ?? '' }} {{ __('quickad.recurring') }} @endif
                                    </td>
                                    <td>{{ $upgrade_start_date ?? '' }}</td>
                                    <td>{{ $upgrade_expiry_date ?? '' }}</td>
                                    @if(($show_cancel_button ?? "")=="1")
                                    <td><a href="{{ $link['MEMBERSHIP'] ?? '#' }}/?action=cancel_auto_renew"><i class="fa fa-remove"></i> {{ __('quickad.cancel') }}</a></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td align="right" colspan="7"><button type="button" class="button" onClick="window.location.href='{{ $link['MEMBERSHIP'] ?? '#' }}/changeplan'">{{ __('quickad.change_plan') }}</button></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
