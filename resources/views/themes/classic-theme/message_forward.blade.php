<meta http-equiv="refresh" content="2;URL={{ $forward ?? '' }}">
@include('partials.header')
<section id="main" class="clearfix  ad-profile-page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li class="active">{{ $heading ?? '' }}</li>
                <div class="pull-right back-result"><a href="{{ $link['DASHBOARD'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>
                        {{ __('quickad.back_result') }}</a></div>
            </ol>
            <!-- breadcrumb -->
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="payment-confirmation-page">
                    <i class="fa fa-check-circle"></i>
                    <h2 class="margin-top-30">{{ $heading ?? '' }}</h2>
                    <p>{{ $message ?? '' }}, {{ __('quickad.you_are_forward') }} {{ $forward ?? '' }} {{ __('quickad.within_second') }} <p/>
                    <a href="{{ $forward ?? '' }}" class="button margin-top-30">{{ __('quickad.click_here') }}</a>
                </div>

            </div>
        </div>
    </div>
</section>

@include('partials.footer')