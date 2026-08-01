@include('partials.header')
<!-- myads-page -->
<section id="main" class="clearfix myads-page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.resubmited_ads') }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>{{ __('quickad.back_result') }}</a></div>
            </ol>
            <!-- breadcrumb -->
        </div>
        <!-- Main Content -->
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
                                        <li><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-heart"></i>{{ __('quickad.favourite_ads') }} <span class="badge">{{ $favoriteads ?? '' }}</span> </a></li>
                                        <li><a href="{{ $link['PENDINGADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-info-circle"></i> {{ __('quickad.pending_ads') }}<span class="badge">{{ $pendingads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['HIDDENADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-eye-slash"></i> {{ __('quickad.hidden_ads') }} <span class="badge">{{ $hiddenads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['EXPIREADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-calendar-times-o"></i> {{ __('quickad.expire_ads') }} <span class="badge">{{ $expireads ?? '' }}</span></a>
                                        <li class="active"><a href="{{ $link['RESUBMITADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-briefcase"></i> {{ __('quickad.resubmited_ads') }} <span class="badge">{{ $resubmitads ?? '' }}</span></a></li>
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
                    <div class="my-quickad section">
                        <h2>{{ __('quickad.resubmited_ads') }}</h2>
                        <table id="js-table-list" class="manage-table responsive-table">
                            <tbody>
                            <tr>
                                <th><i class="fa fa-file-text"></i> {{ __('quickad.item_details') }}</th>
                                <th class="item-status"><i class="fa fa-bell"></i> {{ __('quickad.status') }}</th>
                                <th><i class="fa fa-cog"></i> {{ __('quickad.option') }}</th>
                            </tr>
                            @foreach($item ?? [] as $item)
                            <tr class='ajax-item-listing' data-item-id="{{ data_get($item ?? [], 'product_id', '') }}">
                            <td class="title-container"><img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="" style="max-height: 200px">
                                <div class="item-title">
                                    <h4>{{ data_get($item ?? [], 'product_name', '') }}
                                        <label class="label-wrap hidden-sm hidden-xs">
                                            @if((data_get($item ?? [], 'featured', ''))=="1") <div class="label featured"> {{ __('quickad.featured') }}</div> @endif
                                            @if((data_get($item ?? [], 'urgent', ''))=="1") <div class="label urgent"> {{ __('quickad.urgent') }}</div> @endif
                                            @if((data_get($item ?? [], 'highlight', ''))=="1") <div class="label highlight"> {{ __('quickad.highlight') }}</div> @endif
                                        </label>
                                    </h4>
                                    <ol class="breadcrumb">
                                        <li><a href="{{ data_get($item ?? [], 'catlink', '') }}">{{ data_get($item ?? [], 'category', '') }}</a></li>
                                        <li><a href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a></li>
                                    </ol>
                                    <ul class="item-details">
                                        <li><i class="fa fa-map-marker"></i><a href="{{ data_get($item ?? [], 'citylink', '') }}">{{ data_get($item ?? [], 'location', '') }}</a></li>
                                        <li><i class="fa fa-clock-o"></i>{{ data_get($item ?? [], 'created_at', '') }}</li>
                                    </ul>
                                    @if((data_get($item ?? [], 'price', ''))!="0") <span class="table-item-price"> {{ data_get($item ?? [], 'price', '') }} </span></div> @endif
                            </td>
                            <td class="item-status" width="12%"><span class="label label-info">{{ __('quickad.resubmited') }}</span></td>
                            <td class="action" width="12%"><a class="delete item-js-delete" href="#" data-ajax-action="deleteResumitAd"><i class="fa fa-remove"></i> {{ __('quickad.delete') }}</a></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <!-- Pagination-->
                        <div class="pagination-container">
                            <div class="mt30 clearfix">
                                <ul class="pagination pull-right">
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
                        </div>
                        <!-- Pagination-->
                    </div>
                </div>
                <!-- # End Page-Content -->
            </div>
            <!-- row -->
        </div>
        <!-- row -->
    </div>
    <!-- container -->
</section>
<!-- myads-page -->
@include('partials.footer')