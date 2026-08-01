@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.feedback') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.feedback') }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
    <div class="container margin-bottom-50">
        <div class="row">
            <div class="col-xl-8 margin-0-auto">
                <h2 class="margin-bottom-20">{{ __('quickad.what_you_think') }}</h2>
                <div class="feed-back-form">
                    <form method="post">
                        <div class="submit-field">
                        <h5>{{ __('quickad.user_details') }}</h5>
                        <input type="text" class="with-border" name="name" placeholder="{{ __('quickad.full_name') }}" required="">
                        <input type="text" class="with-border" name="email" placeholder="{{ __('quickad.email') }}" required="">
                        <input type="text" class="with-border" name="phone" placeholder="{{ __('quickad.phone_no') }}">
                        <input type="text" class="with-border" name="subject" placeholder="{{ __('quickad.subject') }}" required="">
                        </div>
                        <div class="submit-field">
                        <h5>{{ __('quickad.anything_to_tell') }}</h5>
                        <textarea type="text" class="with-border" name="message" placeholder="{{ __('quickad.message') }}..." required=""></textarea>
                        </div>
                        <div class="submit-field">
                            @if(($recaptcha_mode ?? "")=="1")
                            <div class="g-recaptcha" data-sitekey="{{ $recaptcha_public_key ?? '' }}"></div>
                            @endif

                            <span style="color:red;font-size: 14px">@if(($recaptch_error ?? "")!="") {{ $recaptch_error ?? '' }} @endif</span>
                        </div>

                        <input type="submit" name="Submit" class="button" value="{{ __('quickad.submit') }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
<!-- main -->
<script src='https://www.google.com/recaptcha/api.js'></script>
@include('partials.footer')
