@include('partials.header')

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
<!-- main -->
<section id="main" class="clearfix page">
    <div class="container">
        <div class="breadcrumb-section"><!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li>{{ __('quickad.site_map') }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>
                    {{ __('quickad.back_result') }}</a></div>
                <h2 class="title">{{ __('quickad.site_map') }}</h2>
            </ol>
            <!-- breadcrumb --></div>
        <div class="section">
            <h2 class="text-center sitemap-h2">{{ __('quickad.list_cat_subcat') }}</h2>
            <hr>
            <div class="row cg-nav-wrapper cg-nav-wrapper-row-2" data-role="cg-nav-wrapper">
                @foreach($cat ?? [] as $cat)
                <div style="width:20%;float: left">
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
                <div class="item util-clearfix" data-spm="0">
                    <h3 class="big-title anchor{{ data_get($subcat ?? [], 'main_id', '') }} anchor-agricuture" data-role="anchor{{ data_get($subcat ?? [], 'main_id', '') }}-scroll">
                        <span id="anchor{{ data_get($subcat ?? [], 'main_id', '') }}" class="anchor-subsitution"></span>
                        <i class="cg-icon {{ data_get($subcat ?? [], 'icon', '') }}"></i>{{ data_get($subcat ?? [], 'main_title', '') }}
                    </h3>
                    <div class="sub-item-wrapper util-clearfix">
                        <div class="sub-item">
                            <h4 class="sub-title">
                                <a href="{{ data_get($subcat ?? [], 'catlink', '') }}">{{ data_get($subcat ?? [], 'main_title', '') }}</a><span> ({{ data_get($subcat ?? [], 'main_ads_count', '') }})</span>
                            </h4>
                            <div class="sub-item-cont-wrapper">
                                <ul class="sub-item-cont util-clearfix">
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
    <!-- container -->
</section>
<!-- main -->
@include('partials.footer')