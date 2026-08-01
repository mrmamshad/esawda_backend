@include('partials.header')

<div id="page-content">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li><a href="{{ $link['LOGIN'] ?? '#' }}">{{ __('quickad.login') }}</a></li>
            <li class="active">{{ __('quickad.forgotpass') }}</li>
        </ol>
        <!--end breadcrumb-->
        <section>
            <div class="row">
                <div class="col-md-6 col-sm-8 col-md-offset-3 col-sm-offset-2">
                    <div class="middle-dabba">
                    <h1>{{ __('quickad.change_pass') }}</h1>
                    <div style="padding:10px">


                        <form name="form1" method="post" action="{{ $link['LOGIN'] ?? '#' }}" id="send">
                            <p>
                                @if(($forgot_error ?? "")!="")<article class="byMsg byMsgError" id="formErrors">! {{ $forgot_error ?? '' }}</article>@endif
                            </p>
                            <div class="input-field">
                                <span class="fbold">{{ __('quickad.username') }} : </span> {{ $username ?? '' }}<br />
                            </div>

                            <div class="input-field">
                                <label for="password">{{ __('quickad.password') }}</label>
                                <input type="password" name="password" id="password"/>
                            </div>

                            <div class="input-field">
                                <label for="password2">{{ __('quickad.conpass') }}</label>
                                <input type="password" name="password2" id="password2"/>
                            </div>

                            <div class="input-field">
                                <input type="hidden" name="forgot" id="forgot" value="{{ $field_forgot ?? '' }}">
                                <input type="hidden" name="r" id="r" value="{{ $field_r ?? '' }}">
                                <input type="hidden" name="e" id="e" value="{{ $field_e ?? '' }}">
                                <input type="hidden" name="t" id="t" value="{{ $field_t ?? '' }}">
                                <input type="hidden" name="type" id="type" value="{{ $field_type ?? '' }}">
                                <input name="Submit" type="hidden" id="Submit" value="Login">
                                <button class="btn btn-primary waves-effect" type="submit" name="Submit"><span>{{ __('quickad.change_pass') }}</span></button>
                            </div>

                        </form>
                    </div>
                </div>
                </div>
            </div>

        </section>
        <!--end ro-->
    </div>
    <!--end container-->
</div>
<!--end page-content-->


@include('partials.footer')