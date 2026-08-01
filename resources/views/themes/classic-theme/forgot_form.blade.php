@include('partials.header')
<!-- forgot-page -->
<section id="main" class="clearfix user-page">
    <div class="container">
        <div class="row text-center">
            <!-- user-login -->
            <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                <div class="user-account">

                    <h2>{{ __('quickad.forgotpass') }}</h2>

                    @if(($success ?? "")!="")
                    <div style="padding-top:20px">
                        <div class="callout callout-success">
                            <h4>{{ __('quickad.confirmation_mail_sent') }}</h4>
                            <p>{{ $success ?? '' }}</p>
                        </div>
                    </div>
                    @endif

                    @if(($login_error ?? "")!="")
                    <article class="byMsg byMsgError" id="formErrors">! {{ $login_error ?? '' }}</article>
                    @endif

                    <!-- form -->
                    <form method="post">
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="{{ __('quickad.emailad') }}" name="email" required="required">
                        </div>
                        <button type="submit" name="Submit" id="submit" class="btn ">{{ __('quickad.req_pass') }}</button>
                        &nbsp;&nbsp;
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