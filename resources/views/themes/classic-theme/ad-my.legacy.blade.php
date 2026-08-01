@include('partials.header')
<link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/datatables/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/datatables/responsive.dataTables.min.css">
<!-- myads-page -->
<section id="main" class="clearfix myads-page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.my_ads') }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>{{ __('quickad.back_result') }}</a>
                </div>
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
                                        <li class="active"><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-book"></i>{{ __('quickad.my_ads') }} <span class="badge">{{ $myads ?? '' }}</span> </a></li>
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
                    <div class="section banner">
                        <!-- banner-form -->
                        <div class="banner-form">
                            <form method="get" action="#" name="locationForm" id="ListingForm">
                                <!-- category-change -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="dropdown category-dropdown"><a data-toggle="dropdown" href="#"><span class="change-text">{{ __('quickad.select_category') }}</span><i class="fa fa-navicon"></i></a>{{ $cat_dropdown ?? '' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="keywords" value="{{ $keywords ?? '' }}" placeholder="{{ __('quickad.what') }} ?" style="padding: 0px;">
                                    </div>
                                    <div class="col-md-3 banner-icon"><i class="fa fa-map-marker"></i>
                                        <input type="text" class="form-control location" id="searchStateCity" name="location" placeholder="{{ __('quickad.where') }} ?" >
                                        <input type="hidden" name="placetype" id="searchPlaceType" value="">
                                        <input type="hidden" name="placeid" id="searchPlaceId" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="hidden" id="input-maincat" name="cat" value="{{ $maincat ?? '' }}"/>
                                        <input type="hidden" id="input-subcat" name="subcat" value="{{ $subcat ?? '' }}"/>
                                        <input type="hidden" id="input-sort" name="sort" value="{{ $sort ?? '' }}"/>
                                        <input type="hidden" id="input-order" name="order" value="{{ $order ?? '' }}"/>
                                        <input type="hidden" id="input-subcat" name="username" value="{{ $username ?? '' }}"/>
                                        <button data-ajax-response='map' type="submit" name="Submit" class="form-control"><i class="fa fa-search"></i> {{ __('quickad.search') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- banner-form -->
                    </div>

                    <div class="my-quickad section">
                        <h2>{{ __('quickad.my_ads') }}</h2>
                        <table id="js-table-list" class="manage-table responsive-table">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-file-text"></i> {{ __('quickad.item_details') }}</th>
                                    <th class="item-status"><i class="fa fa-bell"></i> {{ __('quickad.status') }}</th>
                                    <th><i class="fa fa-cog"></i> {{ __('quickad.option') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item ?? [] as $item)

                                <tr class='ajax-item-listing @if((data_get($item ?? [], 'status', ''))=="hide") opapcityLight @endif' data-item-id="{{ data_get($item ?? [], 'id', '') }}">
                                    <td class="title-container">
                                        <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="" style="max-height: 200px">
                                        <div class="item-title">
                                            <h4><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
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
                                                <li><i class="fa fa-calendar-times-o"></i>{{ __('quickad.expiry_date') }}: {{ data_get($item ?? [], 'expire_date', '') }}</li>
                                            </ul>
                                            @if((data_get($item ?? [], 'price', ''))!="0") <span class="table-item-price"> {{ data_get($item ?? [], 'price', '') }} </span> @endif
                                        </div>
                                    </td>
                                    <td class="item-status" width="12%">
                                        @if((data_get($item ?? [], 'status', ''))=="active") <span class="label label-success">{{ data_get($item ?? [], 'status', '') }}</span>@endif
                                        @if((data_get($item ?? [], 'status', ''))=="pending") <span class="label label-warning">{{ data_get($item ?? [], 'status', '') }}</span> @endif
                                        @if((data_get($item ?? [], 'status', ''))=="rejected") <span class="label label-danger">{{ data_get($item ?? [], 'status', '') }}</span> @endif
                                        @if((data_get($item ?? [], 'status', ''))=="expire") <span class="label label-danger">{{ data_get($item ?? [], 'status', '') }}</span> @endif
                                        @if((data_get($item ?? [], 'hide', ''))=="1") <span class="label label-info label-hidden">{{ __('quickad.hidden') }}</span>@endif
                                    </td>
                                    <td class="action" width="12%">
                                        <a href="{{ $link['EDIT-AD'] ?? '#' }}/{{ data_get($item ?? [], 'id', '') }}"><i class="fa fa-pencil"></i> {{ __('quickad.edit') }}</a>
                                        <a class="item-js-hide" href="#" data-ajax-action="hideItem">
                                            @if((data_get($item ?? [], 'hide', ''))=="0") <i class="fa  fa-eye-slash"></i> {{ __('quickad.hide') }} @endif
                                            @if((data_get($item ?? [], 'hide', ''))=="1") <i class="fa  fa-eye"></i> {{ __('quickad.show') }} @endif</a>
                                        <a class="delete item-js-delete" href="#" data-ajax-action="deleteMyAd"><i class="fa fa-remove"></i> {{ __('quickad.delete') }}</a>
                                    </td>
                                </tr>
                                @endforeach



                                <tr>
                                    <td colspan="3" class="pad-zero">
                                        @if(($adsfound ?? "")=="0")
                                        <h4>{{ __('quickad.no_result_found') }}</h4>
                                        @endif
                                        <!-- Pagination-->
                                        <div class="pagination-container mar-zero">
                                            <ul class="pagination">
                                                @foreach($pages ?? [] as $pages)@if((data_get($pages ?? [], 'current', ''))=="0")
                                                    <li><a href="{{ data_get($pages ?? [], 'link', '') }}">{{ data_get($pages ?? [], 'title', '') }}</a></li>
                                                @endifIF("{{ data_get($pages ?? [], 'current', '') }}"=="1"){
                                                    <li class="active"><a>{{ data_get($pages ?? [], 'title', '') }}</a></li>
                                                @endif@endforeach
                                            </ul>
                                        </div>
                                        <!-- Pagination-->
                                    </td>
                                </tr>



                            </tbody>



                        </table>

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

<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/datatables/dataTables.responsive.min.js"></script>

<script>
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