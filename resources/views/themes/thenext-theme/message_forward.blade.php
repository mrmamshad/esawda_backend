<meta http-equiv="refresh" content="2;URL={{ $forward ?? '' }}">
@include('partials.header')
<div id="titlebar" class="gradient">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <h2>{{ $heading ?? '' }}</h2>

                <!-- Breadcrumbs -->
                <nav id="breadcrumbs" class="dark">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">Home</a></li>
                        <li>{{ $heading ?? '' }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container margin-top-50 margin-bottom-50">
    <div class="row">
        <div class="col-md-12">
            <div class="dashboard-box margin-top-0 padding-top-0 margin-bottom-50">
                <div class="headline">
                    <h3>{{ $heading ?? '' }}</h3>
                </div>
                <div class="content with-padding padding-bottom-10">
                    <h1 class="margin-bottom-30">{{ $heading ?? '' }}</h1>
                    <p>{{ $message ?? '' }}, {{ __('quickad.you_are_forward') }} {{ $forward ?? '' }} {{ __('quickad.within_second') }} <a href="{{ $forward ?? '' }}">{{ __('quickad.click_here') }}</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.footer')
