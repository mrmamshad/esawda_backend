@include('partials.header')
<style>
    #post-form table th {
        font-size: 14px;
        font-weight: 700;
        color: #f5f5f5;
        background-color: #555555;
    }
    #post-form table td{ font-size:14px; font-weight:normal}
</style>

<!-- Payment-Method-page -->
<section id="main" class="clearfix  myads-page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.current_plan') }}</li>
                <div class="pull-right back-result"><a href="{{ $link['DASHBOARD'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>{{ __('quickad.back_result') }}</a></div>
            </ol>
            <!-- breadcrumb -->
        </div>
        <!-- Main Content -->
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
                                    <li class="active"><a href="{{ $link['MEMBERSHIP'] ?? '#' }}" class="waves-effect"><i class="fa fa-shopping-bag"></i> {{ __('quickad.membership') }} </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="collapse-box">
                            <h5 class="collapse-title"> {{ __('quickad.my_ads') }} <a class="pull-right" data-toggle="collapse" href="#MyAds"><i class="fa fa-angle-down"></i></a></h5>
                            <div id="MyAds" class="panel-collapse collapse in">
                                <ul class="acc-list">
                                    <li><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-book"></i>{{ __('quickad.my_ads') }} <span class="badge">{{ $myads ?? '' }}</span> </a></li>
                                    <li><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-heart"></i>{{ __('quickad.favourite_ads') }} <span class="badge">{{ $favoriteads ?? '' }}</span> </a></li>
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
            <!-- Page-Content -->
            <div class="col-sm-9 page-content">
                <div class="my-quickad section"  id="post-form">
                    <h2>{{ __('quickad.current_plan') }}</h2>
                    <table width="100%" cellspacing="1" cellpadding="5" class="table table-striped table-hover">
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
                            <td align="right" colspan="7"><button type="button" class="btn btn-primary" onClick="window.location.href='{{ $link['MEMBERSHIP'] ?? '#' }}/changeplan'">{{ __('quickad.change_plan') }}</button></td>
                        </tr>

                    </table>


                </div>


            </div>
            <!-- # End Page-Content -->
        </div>
        <!-- row -->
    </div>
    <!-- container -->
</section>
<!-- ad-dashboard-page -->
@include('partials.footer')