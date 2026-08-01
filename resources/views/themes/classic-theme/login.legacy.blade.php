@include('partials.header')
<!-- signin-page -->
<section id="main" class="clearfix user-page">
    <div class="container">
        <div class="row text-center">
            <!-- user-login -->
            <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <div class="user-account">
                    <h2>{{ __('quickad.user_login') }}</h2>

                    <div class="social-signup socialLoginDivHide" style="padding-bottom: 20px;">
                        <div class="row">
                            @if(($facebook_app_id ?? "")!="")
                            <div class="col-xs-6"><a class="loginBtn loginBtn--facebook" onclick="fblogin()"><i class="fa fa-facebook"></i> <span>Facebook</span></a></div>
                            @endif
                            @if(($google_app_id ?? "")!="")
                            <div class="col-xs-6"><a class="loginBtn loginBtn--google" onclick="gmlogin()"><i class="fa fa-google"></i> <span>Google</span></a></div>
                            @endif
                        </div>
                        <div class="clear"></div>
                    </div>
                    @if(($error ?? "")!="")
                    <article class="byMsg byMsgError" style="margin-bottom: 40px;" id="formErrors">! {{ $error ?? '' }}</article>
                    @endif
                    <!-- form -->
                    <form method="post">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="{{ __('quickad.username') }} / {{ __('quickad.email') }}" name="username">
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="{{ __('quickad.password') }}" name="password">
                        </div>
                        <input type="hidden" name="ref" value="{{ $ref ?? '' }}"/>
                        <button type="submit" name="submit" id="submit" class="btn ">{{ __('quickad.login') }}</button>
                        &nbsp;&nbsp;
                    </form>
                    <!-- form -->
                    <!-- forgot-password -->
                    <div class="user-option">
                        <div class="checkbox pull-left">
                            <label for="logged"><input type="checkbox" name="logged" id="logged">{{ __('quickad.keep_me_login') }}</label>
                        </div>
                        <div class="pull-right forgot-password"><a href="{{ $link['LOGIN'] ?? '#' }}?fstart=1">{{ __('quickad.forgotpass') }}?</a>
                        </div>
                    </div>
                    <!-- forgot-password -->
                </div>
                <a href="{{ $link['SIGNUP'] ?? '#' }}" class="btn-primary">{{ __('quickad.create_new_account') }}</a></div>
            <!-- user-login -->
        </div>
        <!-- row -->
    </div>
    <!-- container -->
</section>
<!-- signin-page -->
@include('partials.footer')