@include('partials.header')
<div id="page-content">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li class="active">{{ __('quickad.countries') }}</li>
        </ol>
        <section class="page-title">
            <h1>{{ __('quickad.countries') }}</h1>
        </section>


        <section>
            <div class="row">@foreach($countrylist ?? [] as $countrylist){{ data_get($countrylist ?? [], 'tpl', '') }}@endforeach</div>
        </section>
    </div>

<script>
    $('#getCountry').on('click','ul li a', function(e) {
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