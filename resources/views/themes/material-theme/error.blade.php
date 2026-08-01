@include('partials.header')

<div id="page-content" style="transform: translateY(0px);">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li class="active">404 {{ __('quickad.error') }}</li>
        </ol>
        <!--end breadcrumb-->
        <section class="page-title center error">
            <h1>404</h1>
            <h2>{{ __('quickad.error') }}</h2>

            @if(($content ?? "")=="")
            <p>{{ __('quickad.not_find_page') }}.</p>
            @endif
            @if(($content ?? "")!="")
            <p>{{ $content ?? '' }}.</p>
            @endif
        </section>
        <!--end page-title-->
        <div class="row">
            <div class="col-md-4 col-sm-4 col-md-offset-4 col-sm-offset-4">
                <form class="form inputs-underline">
                    <div class="form-group center">
                        <a href="{{ $link['INDEX'] ?? '#' }}" class="btn btn-primary ju-btn-default btn-filled rounded">{{ __('quickad.go_home') }}</a>
                    </div><!-- /input-group -->
                </form>
                <!--end form-->
            </div>
        </div>
    </div>
    <!--end container-->
</div>

@include('partials.footer')
