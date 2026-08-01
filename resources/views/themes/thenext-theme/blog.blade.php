@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $title ?? '' }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li><a href="{{ $link['BLOG'] ?? '#' }}">{{ __('quickad.blog') }}</a></li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container margin-bottom-50">
    <div class="row">
        <div class="col-md-8 col-12">
            @if(($result_found ?? ""))
            <div class="listings-container grid-layout">
                @foreach($blog ?? [] as $blog)
                    <div class="job-listing blog-listing">
                        <div class="job-listing-details">
                            @if(($blog_banner ?? ""))
                            <div class="job-listing-company-logo">
                                <a href="{{ data_get($blog ?? [], 'link', '') }}">
                                    <img class="lazy-load" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ $site_url ?? '' }}storage/blog/{{ data_get($blog ?? [], 'image', '') }}" alt="{{ data_get($blog ?? [], 'title', '') }}">
                                </a>
                            </div>
                            @endif
                            <div class="job-listing-description">
                                <div class="blog-cat">{{ data_get($blog ?? [], 'categories', '') }}</div>
                                <h3 class="job-listing-title"><a href="{{ data_get($blog ?? [], 'link', '') }}">{{ data_get($blog ?? [], 'title', '') }}</a></h3>

                                <p class="job-listing-text margin-top-10">{{ data_get($blog ?? [], 'description', '') }}</p>
                            </div>
                        </div>
                        <div class="job-listing-footer">
                            <ul>
                                <li><img src="{{ $site_url ?? '' }}storage/profile/{{ data_get($blog ?? [], 'author_pic', '') }}"
                                         class="author-avatar"> {{ __('quickad.by') }} <a href="{{ data_get($blog ?? [], 'author_link', '') }}">{{ data_get($blog ?? [], 'author', '') }}</a></li>
                                <li><i class="la la-clock-o"></i> {{ data_get($blog ?? [], 'created_at', '') }}</li>
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>

            @if(($show_paging ?? ""))
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
            @endif
            {{ $else ?? '' }}
            <div class="blog-not-found">
                <h2><span>:</span>(</h2>
                <p>
                    {{ __('quickad.blog_not_found') }}
                </p>
            </div>
            @endif
        </div>
        <div class="col-md-4 hide-under-768px">
            <div class="blog-widget">
                <form action="{{ $link['BLOG'] ?? '#' }}">
                    <div class="input-with-icon">
                        <input class="with-border" type="text" placeholder="{{ __('quickad.search') }}..." name="s"
                               id="search-widget" value="{{ $search ?? '' }}">
                        <i class="icon-feather-search"></i>
                    </div>
                </form>
            </div>
            <div class="blog-widget">
                <h3 class="widget-title">{{ __('quickad.categories') }}</h3>
                <div class="">
                    <ul>
                        @foreach($blog_cat ?? [] as $blog_cat)
                            <li class="clearfix">
                                <a href="{{ data_get($blog_cat ?? [], 'link', '') }}">
                                    <span class="pull-left">{{ data_get($blog_cat ?? [], 'title', '') }}</span>
                                    <span class="pull-right">({{ data_get($blog_cat ?? [], 'blog', '') }})</span></a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @if(($testimonials_enable ?? "") && ($show_testimonials_blog ?? ""))
            <div class="blog-widget">
                <h3 class="widget-title">{{ __('quickad.testimonials') }}</h3>
                <div class="single-carousel">
                    @foreach($testimonials ?? [] as $testimonials)
                        <div class="single-testimonial">
                            <div class="single-inner">
                                <div class="testimonial-content">
                                    <p>{{ data_get($testimonials ?? [], 'content', '') }}</p>
                                </div>
                                <div class="testi-author-info">
                                    <div class="image">
                                        <img class="lazy-load" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ $site_url ?? '' }}storage/testimonials/{{ data_get($testimonials ?? [], 'image', '') }}" alt="{{ data_get($testimonials ?? [], 'name', '') }}">
                                    </div>
                                    <h5 class="name">{{ data_get($testimonials ?? [], 'name', '') }}</h5>
                                    <span class="designation">{{ data_get($testimonials ?? [], 'designation', '') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            <div class="blog-widget">
                <h3 class="widget-title">{{ __('quickad.tags') }}</h3>
                <div class="">
                    <div class="job-tags">
                        {{ $all_tags ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.footer')