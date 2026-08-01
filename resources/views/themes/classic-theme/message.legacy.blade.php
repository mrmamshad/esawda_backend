@include('partials.header')
<div class="row">
    <div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
        <div class="middle-dabba">
            <h1>{{ $heading ?? '' }}!</h1>

            <p>{{ $message ?? '' }}</p>

            <p id="stylish" class="plan1">
                <button onClick="window.location.href='javascript:history.back();'"
                        class="btn btn-primary waves-effect">{{ __('quickad.back') }}
                </button>
            </p>
        </div>
    </div>
</div>
@include('partials.footer')