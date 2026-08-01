@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.testimonials') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li><a href="{{ $link['TESTIMONIALS'] ?? '#' }}">{{ __('quickad.testimonials') }}</a></li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container margin-bottom-50">
    <div class="row">
        @foreach($testimonials ?? [] as $testimonials)
            <div class="col-md-4">
                <div class="single-testimonial">
                    <div class="single-inner">
                        <div class="testimonial-content">
                            <p>{{ data_get($testimonials ?? [], 'content', '') }}</p>
                        </div>
                        <div class="testi-author-info">
                            <div class="image"><img src="{{ $site_url ?? '' }}storage/testimonials/{{ data_get($testimonials ?? [], 'image', '') }}"
                                                    alt="{{ data_get($testimonials ?? [], 'name', '') }}"></div>
                            <h5 class="name">{{ data_get($testimonials ?? [], 'name', '') }}</h5>
                            <span class="designation">{{ data_get($testimonials ?? [], 'designation', '') }}</span>
                        </div>
                    </div>
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
</div>
@include('partials.footer')