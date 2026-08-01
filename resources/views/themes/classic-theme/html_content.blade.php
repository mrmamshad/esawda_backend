@include('partials.header')
<section id="main" class="clearfix page">
    <div class="container">
        <div class="breadcrumb-section"><!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                <li>{{ $name ?? '' }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>
                    {{ __('quickad.back_result') }}</a></div>
                <h2 class="title">{{ $title ?? '' }}</h2>
            </ol>
            <!-- breadcrumb --></div>
        <div class="section html-pages">{{ $html ?? '' }}</div>
        <!-- faq-page --></div>
    <!-- container --></section>
@include('partials.footer') 