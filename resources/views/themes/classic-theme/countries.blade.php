@include('partials.header')
<section id="main" class="clearfix page">
    <div class="container">
        <div class="breadcrumb-section"><!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li>{{ __('quickad.countries') }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>
                    {{ __('quickad.back_result') }}</a></div>
                <h2 class="title">{{ __('quickad.country') }}</h2>
            </ol>
            <!-- breadcrumb --></div>
        <div class="faq-page section crl" id="getCountry">
            <div class="row">@foreach($countrylist ?? [] as $countrylist){{ data_get($countrylist ?? [], 'tpl', '') }}@endforeach</div>
        </div>
    </div>
    <!-- container --></section>
<script>
    $('#getCountry').on('click', 'ul li a', function (e) {
        e.stopPropagation();
        e.preventDefault();

        localStorage.Quick_placeText = "";
        localStorage.Quick_PlaceId = "";
        localStorage.Quick_PlaceType = "";
        var url = $(this).attr('href');
        window.location.href = url;
    });
</script>
@include('partials.footer')