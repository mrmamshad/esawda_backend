@include('partials.header')

<div id="page-content" style="transform: translateY(0px);">
    <div class="container">
        <ol class="breadcrumb bcstyle2">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li class="active">{{ __('quickad.profile') }}</li>
        </ol>
        <a href="{{ $link['POST-AD'] ?? '#' }}" class="postadinner"><span> <i class="fa fa-plus-circle"></i> {{ __('quickad.post_ad') }}</span></a>
        <div class="row">
            <div class="col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1">
                <section class="page-title">
                    <h1> {{ $fullname ?? '' }}</h1>
                </section>
                <!--end section-title-->
                <section>
                    <div class="subject-detail">
                        <div class="image">
                            <div class="bg-transfer">
                                <img src="{{ $site_url ?? '' }}storage/profile/{{ $userimage ?? '' }}" alt="{{ $fullname ?? '' }}">
                            </div>
                        </div>
                        <div class="description">
                            <section class="name">
                                <h2>{{ $username ?? '' }}
                                    @if(($sub_image ?? "")!="")
                                    <img src="{{ $sub_image ?? '' }}" alt="{{ $sub_title ?? '' }}" title="{{ $sub_title ?? '' }}" width="28px"/>
                                    @endif
                                </h2>
                                <p><strong>{{ $tagline ?? '' }}</strong></p>
                                <p>{{ $about ?? '' }}</p>
                            </section>
                            <!--end description-->
                            <section class="contacts">
                                <figure class="social-links"><i class="fa fa-user"></i>{{ $username ?? '' }}</figure>
                                <figure class="social-links"><i class="fa fa-phone"></i>{{ $phone ?? '' }}</figure>
                                <figure class="social-links"><a href="mailto:{{ $email ?? '' }}"><i class="fa fa-envelope"></i>{{ $email ?? '' }}</a></figure>
                                <figure class="social-links"><i class="fa fa-map-marker"></i>{{ $address ?? '' }}</figure>
                            </section>
                            <!--end contacts-->
                            <section class="social social-links">
                                <a href="#"><i class="fa fa-facebook"></i></a>
                                <a href="#"><i class="fa fa-twitter"></i></a>
                                <a href="#"><i class="fa fa-google-plus"></i></a>
                                <a href="#"><i class="fa fa-instagram"></i></a>
                                <a href="#"><i class="fa fa-linkedin"></i></a>
                                <a href="#"><i class="fa fa-youtube"></i></a>
                            </section>
                            <!--end social-->
                        </div>
                        <!--end description-->
                    </div>
                    <!--end subject-detail-->
                </section>
                <section>
                    <h2>{{ $fullname ?? '' }} {{ __('quickad.listings') }}</h2>
                    <section>
                        <form action="#" id="filterForm" method="get">
                            <div class="search-results-controls clearfix">
                                <div class="pull-left">
                                    <span id="grid" class="circle-icon cursor-point active"><i class="fa fa-th icon-white"></i></span>
                                    <span id="list" class="circle-icon cursor-point"><i class="fa fa-bars"></i></span>
                                </div>
                                <input type="hidden" name="username" value="{{ $username ?? '' }}">
                                <!--end left-->
                                <div class="pull-right">
                                    <div class="input-group inputs-underline min-width-150px">
                                        <select class="meterialselect" name="limit" onchange="this.form.submit()">
                                            <option value="6">Limit Order</option>
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
                                            <option value="">Sort by</option>
                                            <option value="title" @if(($sort ?? "")=="title") selected @endif >Name </option>
                                            <option value="price" @if(($sort ?? "")=="price") selected @endif >Price </option>
                                            <option value="date" @if(($sort ?? "")=="date") selected @endif >Date </option>
                                        </select>
                                    </div>
                                </div>
                                <!--end right-->
                            </div>
                            <!--end search-results-controls-->
                        </form>
                    </section>
                    <section>
                        <div class="" id="serchlist">
                            <div class="searchresult grid hideresult" style="display: none;">
                                <div class="row">
                                    @foreach($item ?? [] as $item)
                                    <div class="col-md-4 col-sm-4">
                                        <div class="item" data-id="{{ data_get($item ?? [], 'id', '') }}">
                                            <div class="ad-listing">
                                                <div class="description">

                                                    <a href="{{ data_get($item ?? [], 'catlink', '') }}"><div class="label label-default">{{ data_get($item ?? [], 'category', '') }}</div></a>

                                                    <h3 title="{{ data_get($item ?? [], 'product_name', '') }}">
                                                        <a href="{{ data_get($item ?? [], 'link', '') }}">{{ data_get($item ?? [], 'product_name', '') }}</a>
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
                                                    <li><i class="fa fa-th-list"></i> {{ __('quickad.sub_category') }}:
                                                        <a title="{{ data_get($item ?? [], 'sub_category', '') }}" href="{{ data_get($item ?? [], 'subcatlink', '') }}">{{ data_get($item ?? [], 'sub_category', '') }}</a>
                                                    </li>
                                                    <li><i class="fa fa-map-marker"></i> {{ __('quickad.location') }} : {{ data_get($item ?? [], 'location', '') }}</li>
                                                    <li><i class="fa fa-calendar"></i> {{ __('quickad.posted_on') }} : {{ data_get($item ?? [], 'created_at', '') }}</li>
                                                    <li><i class="fa fa-user"></i> {{ __('quickad.posted_by') }} : <a href="{{ data_get($item ?? [], 'author_link', '') }}" target="_blank">{{ data_get($item ?? [], 'username', '') }}</a></li>
                                                </ul>

                                                <div class="ad-footer-tags">
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
                                        <div class="ad-listing">
                                            <div class="image bg-transfer">

                                                <figure><a href="{{ data_get($item2 ?? [], 'catlink', '') }}"><div class="label-featured label label-default">{{ data_get($item2 ?? [], 'category', '') }}</div></a></figure>

                                                <img src="{{ $site_url ?? '' }}storage/products/thumb/{{ data_get($item2 ?? [], 'picture', '') }}" alt="{{ data_get($item2 ?? [], 'product_name', '') }}">
                                            </div>

                                            <!--end image-->

                                            <div class="description {{ data_get($item2 ?? [], 'highlight_bg', '') }}">
                                                <h3 title="{{ data_get($item2 ?? [], 'product_name', '') }}">
                                                    <a href="{{ data_get($item2 ?? [], 'link', '') }}">{{ data_get($item2 ?? [], 'product_name', '') }}</a>
                                                </h3>
                                                <ul class="icondetail">
                                                    <li><i class="fa fa-th-list"></i> {{ __('quickad.sub_category') }} :
                                                        <a title="{{ data_get($item2 ?? [], 'sub_category', '') }}" href="{{ data_get($item2 ?? [], 'subcatlink', '') }}">{{ data_get($item2 ?? [], 'sub_category', '') }}</a>
                                                    </li>
                                                    <li><i class="fa fa-map-marker"></i> {{ __('quickad.location') }} : {{ data_get($item2 ?? [], 'location', '') }}</li>
                                                    <li><i class="fa fa-calendar"></i> {{ __('quickad.posted_on') }} : {{ data_get($item2 ?? [], 'created_at', '') }}</li>
                                                    <li><i class="fa fa-user"></i> {{ __('quickad.posted_by') }} : <a href="{{ data_get($item2 ?? [], 'author_link', '') }}" target="_blank">{{ data_get($item2 ?? [], 'username', '') }}</a></li>
                                                </ul>
                                                @if((data_get($item2 ?? [], 'showtag', ''))=="1")
                                                <ul class="tags">
                                                    {{ data_get($item2 ?? [], 'tag', '') }}
                                                </ul>
                                                @endif
                                                <div class="ad-footer-tags">
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
        <!--end row-->
    </div>
    <!--end container-->
</div>

@include('partials.footer')