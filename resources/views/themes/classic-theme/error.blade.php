@include('partials.header')
<section id="main" class="clearfix text-center">
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="found-section section">
                    <h1 style="display: none">404</h1>

                    <h2>{{ $message ?? '' }}</h2>
                    @if(($content ?? "")=="")
                        <p>{{ __('quickad.not_find_page') }}.</p>
                    @endif
                    @if(($content ?? "")!="")
                        <p>{{ $content ?? '' }}.</p>
                    @endif

                    <a href="{{ $link['INDEX'] ?? '#' }}" class="btn btn-primary">{{ __('quickad.go_home') }}</a></div>
            </div>
        </div>
    </div>
    <!-- container -->
</section>
<!-- main -->
@include('partials.footer')