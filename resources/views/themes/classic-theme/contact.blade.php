@include('partials.header')
<section id="main" class="clearfix contact-us">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.contact_us') }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>
                    {{ __('quickad.back_result') }}</a></div>
            </ol>
            <!-- breadcrumb -->
            <h2 class="title">{{ __('quickad.contact_us') }}</h2>
        </div>
        <!-- gmap -->
        <div class="map" id="singleListingMap" data-latitude="{{ $latitude ?? '' }}" data-longitude="{{ $longitude ?? '' }}" data-map-icon="fa fa-marker" style="height: 300px; margin-bottom: 30px;"></div>
        <div class="business-info">
            <div class="row">
                <!-- Enquiry Form-->
                <div class="col-sm-8">
                    <div class="contactUs">
                        <h2>{{ __('quickad.contact_us') }}</h2>

                        <form id="contact-form" class="contact-form" name="contact-form" method="post" action="#">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" required="required" placeholder="{{ __('quickad.yname') }}" name="name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="email" class="form-control" required="required" placeholder="{{ __('quickad.yemail') }}" name="email">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" required="required" placeholder="{{ __('quickad.subject') }}" name="subject">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <textarea name="message" id="message" required="required" class="form-control" rows="7" placeholder="{{ __('quickad.message') }}"></textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">

                                        @if(($recaptcha_mode ?? "")=="1")
                                        <div class="g-recaptcha" data-sitekey="{{ $recaptcha_public_key ?? '' }}"></div>
                                        @endif

                                        <span style="color:red">@if(($recaptch_error ?? "")!="") {{ $recaptch_error ?? '' }} @endif</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" name="Submit" class="btn btn-outline">{{ __('quickad.send_message') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Enquiry Form-->
                <!-- contact-detail -->
                <div class="col-sm-4">
                    <div class="contactUs-detail">
                        <h4 class="heading">{{ __('quickad.get_touch') }}</h4>

                        <p>{{ __('quickad.contact_page_text') }}</p>
                        <hr>
                        <h4 class="heading">{{ __('quickad.contact_information') }}</h4>
                        <ul class="list-icons">
                            @if(($address ?? "")!="") <li><i class="fa fa-map-marker"></i>{{ $address ?? '' }}</li>@endif
                            @if(($phone ?? "")!="") <li><i class="fa fa-phone"></i><a href="tel:{{ $phone ?? '' }}">{{ $phone ?? '' }}</a></li>@endif
                            @if(($email ?? "")!="") <li><i class="fa fa-envelope"></i><a href="mailto:{{ $email ?? '' }}">{{ $email ?? '' }}</a></li>@endif
                        </ul>
                    </div>
                </div>
                <!-- contact-detail -->
            </div>
            <!-- row -->
        </div>
    </div>
    <!-- container -->
</section>
<script src='https://www.google.com/recaptcha/api.js'></script>

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