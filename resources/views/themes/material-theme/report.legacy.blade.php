@include('partials.header')


<div class="row">
    <div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
        <div class="middle-container">
            <div class="middle-dabba">
                <h1>{{ __('quickad.reportvio') }}</h1>
                <div id="post-form" style="padding:10px">
                    <form name="form1" method="post" action="" id="send">

                        <div class="input-field">
                            <label for="name">{{ __('quickad.yname') }} @if(($name_error ?? "")!="")<span class="redc">({{ $name_error ?? '' }})</span>@endif</label>
                            <input name="name" type="text" id="name" value="{{ $name ?? '' }}">
                        </div>

                        <div class="input-field">
                            <label for="email">{{ __('quickad.yemail') }} @if(($email_error ?? "")!="")<span class="redc">({{ $email_error ?? '' }})</span>@endif</label>
                            <input name="email" type="text" id="email"  value="{{ $email ?? '' }}">
                        </div>

                        <div class="input-field">
                            <label for="username">{{ __('quickad.yusername') }}</label>
                            <input name="username" type="text" id="username" value="{{ $username ?? '' }}" >
                        </div>
                        <div class="input-field">

                            <select name="violation" class="meterialselect">
                                <option>Select {{ __('quickad.violation') }} {{ __('quickad.type') }}</option>
                                <option value="{{ __('quickad.postcontact') }}">{{ __('quickad.postcontact') }}</option>
                                <option value="{{ __('quickad.advwebsite') }}">{{ __('quickad.advwebsite') }}</option>
                                <option value="{{ __('quickad.fakeproj') }}">{{ __('quickad.fakeproj') }}</option>
                                <option value="{{ __('quickad.abnormalbid') }}">{{ __('quickad.abnormalbid') }}</option>
                                <option value="{{ __('quickad.other') }}">{{ __('quickad.other') }}</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <label for="username2">{{ __('quickad.userother') }}</label>
                            <input name="username2" type="text" id="username2" value="{{ $username2 ?? '' }}" size="42">
                        </div>
                        <div class="input-field">
                            <label for="url">{{ __('quickad.urlviolation') }}</label>
                            <input name="url" type="text" id="url" size="42" value="{{ $redirect_url ?? '' }}">
                        </div>
                        <div class="input-field">
                            <label for="details">{{ __('quickad.viodetails') }} @if(($viol_error ?? "")!="")<span class="redc">({{ $viol_error ?? '' }})</span>@endif</label>
                            <textarea name="details" class="materialize-textarea" cols="32" rows="6" id="details">{{ $details ?? '' }}</textarea></div>
                        </div>
                        <div class="input-field center">
                            <input type="submit" name="Submit" class="btn btn-primary btn-rounded" value="{{ __('quickad.reportvio') }}">
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('partials.footer')