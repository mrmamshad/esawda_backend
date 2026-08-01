@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.countries') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.countries') }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container margin-bottom-50">
    <div class="row">@foreach($countrylist ?? [] as $countrylist){{ data_get($countrylist ?? [], 'tpl', '') }}@endforeach</div>
</div>
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
