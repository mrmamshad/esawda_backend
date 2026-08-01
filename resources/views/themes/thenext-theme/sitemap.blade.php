@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.site_map') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.site_map') }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
    <div class="container margin-bottom-50">
        <div class="section">
            <h2 class="text-center sitemap-h2">{{ __('quickad.list_cat_subcat') }}</h2>
            <hr>
            <div class="row cg-nav-wrapper cg-nav-wrapper-row-2" data-role="cg-nav-wrapper">
                @foreach($cat ?? [] as $cat)
                <div style="">
                    <div class="anchor-wrap anchor{{ data_get($cat ?? [], 'main_id', '') }}-wrap" data-role="anchor{{ data_get($cat ?? [], 'main_id', '') }}">
                        <a class="anchor{{ data_get($cat ?? [], 'main_id', '') }} jumper" data-role="cont" href="#anchor{{ data_get($cat ?? [], 'main_id', '') }}">
                            <i class="caticon {{ data_get($cat ?? [], 'icon', '') }}"></i>
                        <span class="desc">
                            {{ data_get($cat ?? [], 'main_title', '') }}
                        </span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="cg-main">
                @foreach($subcat ?? [] as $subcat)
                <div class="item clearfix" data-spm="0">
                    <h3 class="big-title anchor{{ data_get($subcat ?? [], 'main_id', '') }} anchor-agricuture" data-role="anchor{{ data_get($subcat ?? [], 'main_id', '') }}-scroll">
                        <span id="anchor{{ data_get($subcat ?? [], 'main_id', '') }}" class="anchor-subsitution"></span>
                        <i class="cg-icon {{ data_get($subcat ?? [], 'icon', '') }}"></i>{{ data_get($subcat ?? [], 'main_title', '') }}
                    </h3>
                    <div class="sub-item-wrapper clearfix">
                        <div class="sub-item">
                            <h4 class="sub-title">
                                <a href="{{ data_get($subcat ?? [], 'catlink', '') }}">{{ data_get($subcat ?? [], 'main_title', '') }}</a><span> ({{ data_get($subcat ?? [], 'main_ads_count', '') }})</span>
                            </h4>
                            <div class="sub-item-cont-wrapper">
                                <ul class="sub-item-cont clearfix">
                                    {{ data_get($subcat ?? [], 'sub_title', '') }}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
<script>
    $(document).ready(function() {
        $(".jumper").on("click", function( e ) {

            e.preventDefault();

            $("body, html").animate({
                scrollTop: $( $(this).attr('href') ).offset().top
            }, 600);

        });
    });
</script>
@include('partials.footer')
