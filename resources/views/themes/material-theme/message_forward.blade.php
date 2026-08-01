<meta http-equiv="refresh" content="2;URL={{ $forward ?? '' }}">
@include('partials.header')

<div class="row">
    <div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
        <div class="middle-dabba">
            <h1>{{ $heading ?? '' }}!</h1>
            <p>{{ $message ?? '' }}, {{ __('quickad.you_are_forward') }} {{ $forward ?? '' }} {{ __('quickad.within_second') }} <a href="{{ $forward ?? '' }}">{{ __('quickad.click_here') }}</a></p>
        </div>
    </div>
</div>
					
@include('partials.footer')