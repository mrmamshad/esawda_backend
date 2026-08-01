@include('partials.header')

<div id="page-content">
    <div class="container">
        <ul class="breadcrumb bcstyle2">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li class="active"><a>{{ __('quickad.expire_ads') }}</a></li>
        </ul>
        <a href="{{ $link['POST-AD'] ?? '#' }}" class="postadinner"><span> <i class="fa fa-plus-circle"></i> {{ __('quickad.post_ad') }}</span></a>
        <!--end breadcrumb-->
        <section class="page-title center"><h1>{{ __('quickad.expire_ads') }}</h1></section>
        <!--end page-title-->
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
                                            <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}" class="waves-effect"><i
                                                            class="fa fa-shopping-bag"></i> {{ __('quickad.membership') }} </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="collapse-box"><h5 class="collapse-title"> {{ __('quickad.my_ads') }} <a class="pull-right" data-toggle="collapse" href="#MyAds"><i class="fa fa-angle-down"></i></a></h5>

                                    <div id="MyAds" class="panel-collapse collapse in">
                                        <ul class="acc-list">
                                            <li><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-book"></i> {{ __('quickad.my_ads') }} <span class="badge">{{ $myads ?? '' }}</span> </a></li>
                                            <li><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-heart"></i> {{ __('quickad.favourite_ads') }} <span class="badge">{{ $favoriteads ?? '' }}</span> </a></li>
                                            <li><a href="{{ $link['PENDINGADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.pending_approval') }} <span class="badge">{{ $pendingads ?? '' }}</span></a></li>
                                            <li><a href="{{ $link['HIDDENADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.hidden_ads') }} <span class="badge">{{ $hiddenads ?? '' }}</span></a></li>
                                            <li class="active"><a href="{{ $link['EXPIREADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-calendar-times-o"></i> {{ __('quickad.expire_ads') }} <span class="badge">{{ $totalitem ?? '' }}</span></a></li>
                                            <li><a href="{{ $link['RESUBMITADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.resubmited_ads') }} <span class="badge">{{ $resubmitads ?? '' }}</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="collapse-box">
                                    <h5 class="collapse-title no-border"> {{ __('quickad.my_account') }} <a class="pull-right" data-toggle="collapse" href="#account"><i class="fa fa-angle-down"></i></a></h5>
                                    <div id="account" class="panel-collapse collapse in">
                                        <ul class="acc-list">
                                            <li><a href="{{ $link['TRANSACTION'] ?? '#' }}" class="waves-effect"><i class="fa fa-money"></i> {{ __('quickad.transaction') }}</a></li>
                                            <li><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}" class="waves-effect"><i class="fa fa-cog"></i> {{ __('quickad.account_setting') }} </a></li>
                                            <li><a href="{{ $link['LOGOUT'] ?? '#' }}" class="waves-effect"><i class="fa fa-unlock"></i> {{ __('quickad.logout') }} </a></li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
                <div class="col-sm-9 page-content">
                    <div class="inner-box"><h1>{{ __('quickad.expire_ads') }} </h1>

                        <div class="table-responsive">
                            <div class="table-action">
                                <div class="table-search pull-right col-xs-12">
                                    <div class="form-group">
                                        <div class="col-xs-7 control-label text-right"> &nbsp; </div>
                                        <div class="col-xs-5 searchpan">
                                            <form method="post">
                                                <div class="input-field">
                                                    <label for="filter" class="active"></label>
                                                    <input type="text" class="live-search-box" id="filter" name="filter" placeholder="Press enter for search" value="">
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="success-msg hideresult" id="successMsg"></span> <span class="error-msg hideresult" id="errorMsg"></span>
                            <table id="js-table-list" class="table table-striped table-bordered add-manage-table table demo footable-loaded footable" data-filter="#filter" data-filter-text-only="true">
                                <thead>
                                <tr>
                                    <th> {{ __('quickad.photo') }}</th>
                                    <th data-sort-ignore="true"> {{ __('quickad.ads_details') }}</th>
                                    <th data-type="numeric"> {{ __('quickad.status') }}</th>
                                    <th> {{ __('quickad.option') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($item ?? [] as $item)
                                <tr class="ajax-item-listing" data-item-id="{{ data_get($item ?? [], 'id', '') }}" data-search-term="{{ data_get($item ?? [], 'product_name', '') }}">
                                    <td class="add-img-td  width-14-per">
                                        <a href="{{ data_get($item ?? [], 'link', '') }}">
                                            <img class="thumbnail  img-responsive" src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item ?? [], 'picture', '') }}" alt="img">
                                        </a>
                                    </td>
                                    <td class="ads-details-td width-58-per">
                                        <div>
                                            <p><strong><a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a></strong>
                                                @if((data_get($item ?? [], 'featured', ''))=="1") <span class="label featured">{{ __('quickad.featured') }}</span> @endif
                                                @if((data_get($item ?? [], 'urgent', ''))=="1") <span class="label urgent">{{ __('quickad.urgent') }}</span> @endif
                                                @if((data_get($item ?? [], 'highlight', ''))=="1") <span class="label highlight"> {{ __('quickad.highlight') }}</span> @endif
                                            </p>
                                            <p><strong> {LANG_POSTED-ON} </strong>: {{ data_get($item ?? [], 'created_at', '') }} </p>
                                            <p><strong>{LANG_LOCATED-IN}:</strong> {{ data_get($item ?? [], 'location', '') }} </p>
                                        </div>
                                    </td>
                                    <td class="price-td width-16-per">
                                        @if((data_get($item ?? [], 'price', ''))!="0") <div><strong>{{ data_get($item ?? [], 'price', '') }}</strong></div>@endif
                                    </td>
                                    <td class="action-td width-10-per">
                                        <div><p class="opacity1">
                                            <a class="btn btn-info btn-rounded btn-xs" href="{{ $link['EDIT-AD'] ?? '#' }}/{{ data_get($item ?? [], 'id', '') }}" data-ajax-action="deleteMyAd"><i class=" fa fa-pencil"></i> {{ __('quickad.renew') }}</a>
                                        </p><p class="opacity1">
                                           <a class="btn btn-danger btn-rounded btn-xs item-js-delete" href="#" data-ajax-action="deleteMyAd"><i class=" fa fa-trash-o"></i> {{ __('quickad.delete') }}</a>
                                        </p></div>
                                    </td>
                                </tr>
                                @endforeach
                                <tr id="norecord" @if(($totalitem ?? "")!="0") style="display: none;" @endif>
                                    <td colspan="5">{{ __('quickad.no_result_found') }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- Pagination -->
                            <div class="mt30 clearfix">
                                <ul class="pagination pull-right">
                                    @foreach($pages ?? [] as $pages)
                                    @if((data_get($pages ?? [], 'current', ''))=="0") <li><a href="{{ data_get($pages ?? [], 'link', '') }}">{{ data_get($pages ?? [], 'title', '') }}</a> </li>@endif
                                    @if((data_get($pages ?? [], 'current', ''))=="1") <li class="active"> <a>{{ data_get($pages ?? [], 'title', '') }}</a> </li>@endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    <!--end container-->
</div> <!--end page-content-->
@include('partials.footer')