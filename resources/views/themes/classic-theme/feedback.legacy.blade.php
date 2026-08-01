@include('partials.header')
<!-- main -->
<section id="main" class="clearfix page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li>{{ __('quickad.feedback') }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>{{ __('quickad.back_result') }}</a></div>
                <h2 class="title">{{ __('quickad.feedback') }}</h2>
            </ol>
            <!-- breadcrumb -->
        </div>
        <div class="section">
            <div class="feed-back">
                <h3>{{ __('quickad.what_you_think') }}</h3>
                <p>&nbsp;</p>

                <div class="feed-back-form">
                    <form method="post">
                        <span>{{ __('quickad.user_details') }}</span>
                        <input type="text" class="form-control" name="name" placeholder="{{ __('quickad.full_name') }}" required="">
                        <input type="text" class="form-control" name="email" placeholder="{{ __('quickad.email') }}" required="">
                        <input type="text" class="form-control" name="phone" placeholder="{{ __('quickad.phone_no') }}">
                        <input type="text" class="form-control" name="subject" placeholder="{{ __('quickad.subject') }}" required="">
                        <!---728x90--->
                        <span>{{ __('quickad.anything_to_tell') }}?</span>
                        <textarea type="text" class="form-control" name="message" placeholder="{{ __('quickad.message') }}..." required=""></textarea>

                        <div class="form-group">
                            @if(($recaptcha_mode ?? "")=="1")
                            <div class="g-recaptcha" data-sitekey="{{ $recaptcha_public_key ?? '' }}"></div>
                            @endif

                            <span style="color:red;font-size: 14px">@if(($recaptch_error ?? "")!="") {{ $recaptch_error ?? '' }} @endif</span>
                        </div>

                        <input type="submit" name="Submit" class="btn btn-outline" value="{{ __('quickad.submit') }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- container -->
</section>
<!-- main -->
<script src='https://www.google.com/recaptcha/api.js'></script>
@include('partials.footer')