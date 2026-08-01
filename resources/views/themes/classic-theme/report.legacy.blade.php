@include('partials.header')
<style>
    label{margin-top: 40px;}
</style>
<!-- signup-page -->
<section id="main" class="clearfix user-page">
    <div class="container">
        <div class="row text-center"><!-- user-login -->
            <div class="col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2">
                <div class="user-account clearfix">
                    <h2 style="margin-bottom: 50px;">{{ __('quickad.reportvio') }}</h2>

                    <form action="#" method="post">
                        <div class="row form-group">
                            <label class="col-sm-3 control-label">{{ __('quickad.yname') }}
                                @if(($name_error ?? "")!="") <span class="required">({{ $name_error ?? '' }})</span>@endif
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control border-form" type="text" name="name" value="{{ $name ?? '' }}"
                                       placeholder="{{ __('quickad.yname') }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-3 control-label">{{ __('quickad.yemail') }} @if(($email_error ?? "")!="") <span
                                    class="redc">({{ $email_error ?? '' }})</span>@endif</label>

                            <div class="col-sm-9">
                                <input class="form-control border-form" type="email" name="email" value="{{ $email ?? '' }}"
                                       placeholder="{{ __('quickad.yemail') }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-3 control-label">{{ __('quickad.yusername') }}</label>

                            <div class="col-sm-9">
                                <input class="form-control border-form" type="text" name="username" value="{{ $username ?? '' }}"
                                       placeholder="{{ __('quickad.yusername') }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-3 control-label">{{ __('quickad.violation') }} {{ __('quickad.type') }}</label>

                            <div class="col-sm-9">
                                <select name="violation" class="form-control">
                                    <option>Select {{ __('quickad.violation') }} {{ __('quickad.type') }}</option>
                                    <option value="{{ __('quickad.postcontact') }}">{{ __('quickad.postcontact') }}</option>
                                    <option value="{{ __('quickad.advwebsite') }}">{{ __('quickad.advwebsite') }}</option>
                                    <option value="{{ __('quickad.fakeproj') }}">{{ __('quickad.fakeproj') }}</option>
                                    <option value="{{ __('quickad.abnormalbid') }}">{{ __('quickad.abnormalbid') }}</option>
                                    <option value="{{ __('quickad.other') }}">{{ __('quickad.other') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-3 control-label">{{ __('quickad.userother') }}</label>

                            <div class="col-sm-9">
                                <input class="form-control border-form" type="text" name="username2" value="{{ $username2 ?? '' }}"
                                       placeholder="{{ __('quickad.userother') }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-3 control-label">{{ __('quickad.urlviolation') }}</label>

                            <div class="col-sm-9">
                                <input class="form-control border-form" type="text" name="url" value="{{ $redirect_url ?? '' }}"
                                       placeholder="{{ __('quickad.urlviolation') }}">
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-sm-3 control-label">{{ __('quickad.viodetails') }} @if(($viol_error ?? "")!="") <span
                                    class="redc">({{ $viol_error ?? '' }})</span>@endif </label>

                            <div class="col-sm-9">
                                <textarea class="form-control border-form" name="details">{{ $details ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="Submit" id="submit" href="#" class="btn">{{ __('quickad.reportvio') }}</button>
                        </div>
                    </form>
                    <!-- checkbox -->
                </div>
            </div>
            <!-- user-login -->
        </div>
        <!-- row -->
    </div>
    <!-- container -->
</section>
<!-- signup-page -->
</div>
@include('partials.footer')