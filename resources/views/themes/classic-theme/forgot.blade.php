@include('partials.header')
<!-- signin-page -->
<section id="main" class="clearfix user-page">
    <div class="container">
        <div class="row text-center">
            <!-- user-login -->
            <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <div class="user-account">

                    <h2>{{ __('quickad.change_pass') }}</h2>

                    @if(($forgot_error ?? "")!="")
                    <article class="byMsg byMsgError" id="formErrors">! {{ $forgot_error ?? '' }}</article>
                    @endif

                    <!-- form -->
                    <form method="post">
                        <div class="form-group"><span class="fbold">{{ __('quickad.username') }} : </span> {{ $username ?? '' }}<br/>
                        </div>
                        <div class="form-group">
                            <label for="password">{{ __('quickad.password') }}</label>
                            <input type="password" class="form-control" name="password" id="password"/>
                        </div>
                        <div class="form-group">
                            <label for="password2">{{ __('quickad.conpass') }}</label>
                            <input type="password" class="form-control" name="password2" id="password2"/>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="forgot" id="forgot" value="{{ $field_forgot ?? '' }}">
                            <input type="hidden" name="r" id="r" value="{{ $field_r ?? '' }}">
                            <input type="hidden" name="e" id="e" value="{{ $field_e ?? '' }}">
                            <input type="hidden" name="t" id="t" value="{{ $field_t ?? '' }}">
                            <input type="hidden" name="type" id="type" value="{{ $field_type ?? '' }}">
                            <input name="Submit" type="hidden" id="Submit" value="Login">
                            <button class="btn" type="submit" name="Submit"><span>{{ __('quickad.change_pass') }}</span></button>
                        </div>
                    </form>
                    <!-- form -->
                </div>
            </div>
            <!-- user-login -->
        </div>
        <!-- row -->
    </div>
    <!-- container -->
</section>
<!-- signin-page -->
@include('partials.footer')