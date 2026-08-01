@include('partials.header')
<script src="https://checkout.stripe.com/v2/checkout.js"></script>
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.advertise_with_us') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.advertise_with_us') }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container margin-bottom-50">

    <div class="row">
        <div class="col-sm-12">
            <div class="found-section section">
                @if(($logged_in ?? ""))
                <div class="section html-pages"><div class="qbm-box"></div></div>
                {{ $else ?? '' }}
                <h1 class="margin-bottom-20">{{ __('quickad.login_required') }}</h1>

                <p>{{ __('quickad.login_req_access') }}</p>

                <a href="#sign-in-dialog" class="button ripple-effect popup-with-zoom-anim ">{{ __('quickad.click_here') }} </a>
                @endif
            </div>
        </div>
    </div>
</div>
@include('partials.footer')
