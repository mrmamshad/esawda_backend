@include('partials.header')


    <div id="page-content">
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.contact_us') }}</li>
            </ol>
            <section class="page-title">
                <h1 class="pull-left">{{ __('quickad.contact_us') }}</h1>
                <div class="pull-right featured-contact">
                    <i class="icon_comment_alt"></i>
                    <h4>24/7 {{ __('quickad.support') }}</h4>
                    <h3>{{ $phone ?? '' }}</h3>
                </div>
            </section>
            <!--end section-title-->
        </div>
        <!--end container-->
        <section>
            <div class="map height-400px" id="singleListingMap" data-latitude="{{ $latitude ?? '' }}" data-longitude="{{ $longitude ?? '' }}" data-map-icon="fa fa-marker" style="height: 400px"></div>
            <!--end map-->
        </section>
        <section class="block">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-3">
                        <h3>{{ __('quickad.contact_information') }}</h3>
                        <div class="box">
                            <address>
                                <strong>{{ __('quickad.location') }}</strong>
                                <figure>{{ $address ?? '' }}</figure>
                                <br>
                                <strong>{LANG_PHONE-NO}</strong>
                                <figure><a href="tel:{{ $phone ?? '' }}">{{ $phone ?? '' }}</a></figure>
                                <br>
                                <strong>{{ __('quickad.email') }}</strong>
                                <figure><a href="#">{{ $email ?? '' }}</a></figure>
                            </address>
                        </div>
                    </div>
                    <!--end col-md-3-->
                    <div class="col-md-9 col-sm-9">
                        <h3>{{ __('quickad.enquiry_form') }}</h3>
                        <form method="post">
                            <div class="row">
                                <div class="col-md-4 col-sm-4">
                                    <div class="input-field">
                                        <label for="name">{{ __('quickad.name') }}</label>
                                        <input type="text" name="name" id="name" required="">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-4-->
                                <div class="col-md-4 col-sm-4">
                                    <div class="input-field">
                                        <label for="email">{{ __('quickad.email') }}</label>
                                        <input type="email" name="email" id="email" required="">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-4-->
                                <div class="col-md-4 col-sm-4">
                                    <div class="input-field">
                                        <label for="subject">{{ __('quickad.subject') }}</label>
                                        <input type="text" name="subject" id="subject" required="">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-4-->
                                <div class="col-md-12 col-sm-12">
                                    <div class="input-field">
                                        <label for="message">{{ __('quickad.message') }}</label>
                                        <textarea class="materialize-textarea" id="message" rows="4" name="message" required=""></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <button type="submit" name="Submit" class="btn btn-primary icon shadow">{{ __('quickad.send_message') }}<i class="fa fa-caret-right"></i></button>
                                </div>
                            </div>
                            <!--end row-->



                            <!--end input-field-->
                        </form>
                    </div>
                    <!--end col-md-9-->
                </div>
                <!--end row-->
            </div>
            <!--end container-->
        </section>
    </div>
    <!--end page-content-->


@if(($map_type ?? "")=="google")
<link href="{{ $site_url ?? '' }}includes/assets/plugins/map/google/map-marker.css" type="text/css" rel="stylesheet">
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/jquery-migrate-1.2.1.min.js'></script>
<script type='text/javascript' src='//maps.google.com/maps/api/js?key={{ $gmap_api_key ?? '' }}&#038;libraries=places%2Cgeometry&#038;ver=2.2.1'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/richmarker-compiled.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/markerclusterer_packed.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/gmapAdBox.js'></script>
<script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/maps.js'></script>
<script>
    var _latitude = '{{ $latitude ?? '' }}';
    var _longitude = '{{ $longitude ?? '' }}';
    var element = "singleListingMap";
    var path = '{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/';
    var getCity = false;
    var color = '{{ $map_color ?? '' }}';
    var site_url = '{{ $site_url ?? '' }}';
    simpleMap(_latitude, _longitude, element);
</script>
{{ $else ?? '' }}
<script>
    var openstreet_access_token = '{{ $openstreet_access_token ?? '' }}';
</script>
<link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/css/style.css">
<!-- Leaflet // Docs: https://leafletjs.com/ -->
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet.min.js"></script>

<!-- Leaflet Maps Scripts (locations are stored in leaflet-quick.js) -->
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-markercluster.min.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-gesture-handling.min.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-quick.js"></script>

<!-- Leaflet Geocoder + Search Autocomplete // Docs: https://github.com/perliedman/leaflet-control-geocoder -->
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-autocomplete.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-control-geocoder.js"></script>
@endif

@include('partials.footer')