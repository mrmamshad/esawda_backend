@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.reportvio') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.reportvio') }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container margin-bottom-50">
    <div class="row"><!-- user-login -->
        <div class="col-xl-8 margin-0-auto">
            <div class="user-account clearfix">
                <h2 class="margin-bottom-50">{{ __('quickad.reportvio') }}</h2>
                <form action="#" method="post">
                    <div class="submit-field">
                      <h5>{{ __('quickad.yname') }}</h5>
                      <input class="with-border" type="text" name="name" value="{{ $name ?? '' }}">
                      @if(($name_error ?? "")!="") <span style="color: red">{{ $name_error ?? '' }}</span>@endif
                    </div>
                    <div class="submit-field">
                      <h5>{{ __('quickad.yemail') }}</h5>
                      <input class="with-border" type="email" name="email" value="{{ $email ?? '' }}">
                      @if(($email_error ?? "")!="") <span style="color: red">{{ $email_error ?? '' }}</span>@endif
                    </div>
                    <div class="submit-field">
                      <h5>{{ __('quickad.yusername') }}</h5>
                      <input class="with-border" type="text" name="username" value="{{ $username ?? '' }}">
                    </div>
                    <div class="submit-field">
                        <h5>{{ __('quickad.violation') }} {{ __('quickad.type') }}</h5>
                        <select name="violation" class="selectpicker with-border">
                            <option>{{ __('quickad.select') }} {{ __('quickad.violation') }} {{ __('quickad.type') }}</option>
                            <option value="{{ __('quickad.postcontact') }}">{{ __('quickad.postcontact') }}</option>
                            <option value="{{ __('quickad.advwebsite') }}">{{ __('quickad.advwebsite') }}</option>
                            <option value="{{ __('quickad.fakeproj') }}">{{ __('quickad.fakeproj') }}</option>
                            <option value="{{ __('quickad.abnormalbid') }}">{{ __('quickad.abnormalbid') }}</option>
                            <option value="{{ __('quickad.other') }}">{{ __('quickad.other') }}</option>
                        </select>
                    </div>
                    <div class="submit-field">
                      <h5>{{ __('quickad.userother') }}</h5>
                      <input class="with-border" type="text" name="username2" value="{{ $username2 ?? '' }}">
                    </div>
                    <div class="submit-field">
                      <h5>{{ __('quickad.urlviolation') }}</h5>
                      <input class="with-border" type="text" name="url" value="{{ $redirect_url ?? '' }}">
                    </div>
                    <div class="submit-field">
                      <h5>{{ __('quickad.viodetails') }}</h5>
                      <textarea class="with-border" name="details">{{ $details ?? '' }}</textarea>
                      @if(($viol_error ?? "")!="") <span style="color: red">{{ $viol_error ?? '' }}</span>@endif
                    </div>
                    <button type="submit" name="Submit" id="submit" class="button">{{ __('quickad.reportvio') }}</button>
                </form>
                <!-- checkbox -->
            </div>
        </div>
        <!-- user-login -->
    </div>
    <!-- row -->
</div>
@include('partials.footer')
