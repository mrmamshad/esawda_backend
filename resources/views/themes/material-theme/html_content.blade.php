@include('partials.header')



<section id="main" class="clearfix page">
    <div class="container">
        <div class="breadcrumb-section">
            <ul class="breadcrumb bcstyle2">
                <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                <li class="active"><a>{{ $name ?? '' }}</a></li>
            </ul>
            <a href="{{ $link['POST-AD'] ?? '#' }}" class="postadinner"><span> <i class="fa fa-plus-circle"></i> {{ __('quickad.post_ad') }}</span></a>
            <!--end breadcrumb-->
            <section class="page-title center"><h1>{{ $title ?? '' }}</h1></section>
        </div>
        <div class="section html-pages">
            {{ $html ?? '' }}
        </div><!-- faq-page -->
    </div><!-- container -->
</section>
@include('partials.footer')

					
