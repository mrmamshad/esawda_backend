@include('partials.header')
<div id="page-content">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li class="active">{{ __('quickad.login') }}</li>
        </ol>
        <!--end breadcrumb-->
        <div class="row">
            <div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
                <div class="middle-dabba">
                    <h1>{{ __('quickad.login_here') }}</h1>

                    <div class="social-signup" style="padding-bottom: 20px;">
                        <div class="row">
                            <div class="col-xs-6"><a class="loginBtn loginBtn--facebook" onclick="fblogin()"><i class="fa fa-facebook"></i> <span>Facebook</span></a></div>
                            <div class="col-xs-6"><a class="loginBtn loginBtn--google" onclick="gmlogin()"><i class="fa fa-google-plus"></i> <span>Google+</span></a></div>
                        </div>
                        <div class="clear"></div>
                    </div>

                    <div id="post-form" style="padding:10px">
                        @if(($error ?? "")!="")
                        <article class="byMsg byMsgError" style="margin-bottom: 40px;" id="formErrors">! {{ $error ?? '' }}</article>
                        @endif
                        <form method="post">
                            <div class="input-field">
                                <label for="username">{{ __('quickad.username') }} / {{ __('quickad.email') }}</label>
                                <input type="text" name="username" id="username">
                            </div>
                            <!--end form-group-->
                            <div class="input-field">
                                <label for="password">{{ __('quickad.password') }}</label>
                                <input type="password" name="password" id="password">
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="ref" value="{{ $ref ?? '' }}"/>
                                <button type="submit" name="submit" id="submit" class="btn btn-primary waves-effect">{{ __('quickad.login') }}</button>&nbsp;&nbsp;
                                <a href="{{ $link['LOGIN'] ?? '#' }}?fstart=1" class="forgotlink">{{ __('quickad.forgotpass') }}?</a>
                            </div>
                            <!--end form-group-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end container-->
</div>
<!--end page-content-->

@include('partials.footer')