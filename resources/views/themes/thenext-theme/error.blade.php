@include('partials.header')
<div id="titlebar" class="gradient">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <h2>{{ $message ?? '' }}</h2>

                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ $message ?? '' }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>

<section id="main" class="clearfix text-center margin-top-50 margin-bottom-50">
    <div class="container">
        <div class="row">
            <div class="col-sm-10 margin-0-auto">
                <div class="found-section section">
                    <h1 class="margin-bottom-20">{{ $message ?? '' }}</h1>
                    @if(($content ?? "") == "")
                    <p>{{ __('quickad.not_find_page') }}.</p>
                    {{ $else ?? '' }}
                    <p>{{ $content ?? '' }}</p>
                    @endif

                    <a href="{{ $link['INDEX'] ?? '#' }}" class="button ripple-effect">{{ __('quickad.go_home') }}</a></div>
            </div>
        </div>
    </div>
    <!-- container -->
</section>
<!-- main -->
@include('partials.footer')
