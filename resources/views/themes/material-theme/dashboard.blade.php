@include('partials.header')

<div id="page-content">
    <div class="container">
        <ul class="breadcrumb bcstyle2">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li class="active"><a>{{ __('quickad.dashboard') }}</a></li>
        </ul>
        <a href="{{ $link['POST-AD'] ?? '#' }}" class="postadinner"><span> <i class="fa fa-plus-circle"></i> {{ __('quickad.post_ad') }}</span></a>
        <!--end breadcrumb-->
        <section class="page-title center"><h1>{{ __('quickad.dashboard') }}</h1></section>
        <!--end page-title-->
        <section>
            <div class="row">
                <aside class="col-md-3 col-sm-12">
                    <div class="inner-box">
                        <div class="user-panel-sidebar">
                            <div class="collapse-box">
                                <h5 class="collapse-title no-border"> {{ __('quickad.my_classified') }} <a class="pull-right" data-toggle="collapse" href="#MyClassified"><i class="fa fa-angle-down"></i></a></h5>
                                <div id="MyClassified" class="panel-collapse collapse in">
                                    <ul class="acc-list">
                                        <li class="active"><a href="{{ $link['DASHBOARD'] ?? '#' }}" class="waves-effect"><i class="fa fa-home"></i> {{ __('quickad.dashboard') }} </a></li>
                                        <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}" class="waves-effect"><i class="fa fa-user"></i> {{ __('quickad.profile_public') }}</a></li>
                                        <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="waves-effect"><i class="fa fa-pencil"></i> {{ __('quickad.post_ad') }}</a></li>
                                        <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}" class="waves-effect"><i
                                                        class="fa fa-shopping-bag"></i> {{ __('quickad.membership') }} </a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="collapse-box"><h5 class="collapse-title"> {{ __('quickad.my_ads') }} <a class="pull-right" data-toggle="collapse" href="#MyAds"><i class="fa fa-angle-down"></i></a></h5>

                                <div id="MyAds" class="panel-collapse collapse in">
                                    <ul class="acc-list">
                                        <li><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-book"></i> {{ __('quickad.my_ads') }}<span class="badge">{{ $myads ?? '' }}</span> </a></li>
                                        <li><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-heart"></i> {{ __('quickad.favourite_ads') }} <span class="badge">{{ $favoriteads ?? '' }}</span> </a></li>
                                        <li><a href="{{ $link['PENDINGADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.pending_approval') }}<span class="badge">{{ $pendingads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['HIDDENADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.hidden_ads') }} <span class="badge">{{ $hiddenads ?? '' }}</span></a></li>
                                        <li><a href="{{ $link['EXPIREADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-calendar-times-o"></i> {{ __('quickad.expire_ads') }} <span class="badge">{{ $expireads ?? '' }}</span></a>
                                        <li><a href="{{ $link['RESUBMITADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-flag"></i> {{ __('quickad.resubmited_ads') }} <span class="badge">{{ $resubmitads ?? '' }}</span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="collapse-box">
                                <h5 class="collapse-title no-border"> {{ __('quickad.my_account') }} <a class="pull-right" data-toggle="collapse" href="#account"><i class="fa fa-angle-down"></i></a></h5>
                                <div id="account" class="panel-collapse collapse in">
                                    <ul class="acc-list">
                                        <li><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}" class="waves-effect"><i class="fa fa-cog"></i> {{ __('quickad.account_setting') }}</a></li>
                                        <li><a href="{{ $link['LOGOUT'] ?? '#' }}" class="waves-effect"><i class="fa fa-unlock"></i> {{ __('quickad.logout') }}</a></li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
                <div class="col-md-9 col-sm-12">
                    <form id="uploadForm" method="post" action="#" enctype="multipart/form-data">
                        <section>
                            <div class="user-details box">
                                <div class="user-image">
                                    <div class="image">
                                        <div class="bg-transfer">
                                            <img src="{{ $site_url ?? '' }}storage/profile/{{ $authorimg ?? '' }}">
                                        </div>
                                        <!--end bg-transfer-->
                                        <div class="single-file-input">
                                            <input type="file" id="avatar" name="avatar">
                                            <div>{{ __('quickad.upload_picture') }} <i class="fa fa-upload"></i></div>
                                        </div>
                                    </div>
                                    <!--end image-->
                                </div>
                                <!--end user-image-->
                                <div class="description clearfix">
                                    <h3>&nbsp;</h3>
                                    <h2>{{ $authorname ?? '' }}</h2>
                                    <a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}" class="btn btn-default btn-rounded scroll btn-xs">{{ __('quickad.account_setting') }}</a>
                                    <hr>
                                    <figure>
                                        <div class="pull-left"><strong>{{ __('quickad.join_date') }} :</strong></div>
                                        <div class="pull-right">{{ $join_date ?? '' }}</div>
                                    </figure>
                                </div>
                                <!--end description-->
                            </div>
                        </section>
                        <!--end user-details-->
                        <section>
                            @foreach($errors ?? [] as $errors)
                            <article class="byMsg byMsgError" id="formErrors">! {{ data_get($errors ?? [], 'message', '') }}</article>
                            @endforeach
                        </section>
                        <section>
                            <h3>{{ __('quickad.about_you') }}</h3>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="name">{{ __('quickad.full_name') }}</label>
                                        <input type="text" name="name" id="name" value="{{ $authorname ?? '' }}">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-12-->
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="email">{{ __('quickad.email') }}</label>
                                        <input id="email" type="email" value="{{ $email ?? '' }}" disabled required=""></div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-6-->
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="phone">{{ __('quickad.phone') }}</label>
                                        <input type="text" name="phone" id="Phone" value="{{ $phone ?? '' }}">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="postcode">{{ __('quickad.postcode') }}</label>
                                        <input type="text" name="postcode" id="postcode" value="{{ $postcode ?? '' }}">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="address">{{ __('quickad.address') }}</label>
                                        <input type="text" id="address" rows="1" name="address" value="{{ $address ?? '' }}"/>
                                    </div>
                                </div>
                                <!--end col-md-6-->
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="city">{{ __('quickad.city') }}</label>
                                        <input type="text" id="city" rows="1" name="city" value="{{ $city ?? '' }}"/>
                                    </div>
                                </div>
                                <!--end col-md-6-->
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <select name="country" class="meterialselect">
                                            @foreach($country ?? [] as $country)
                                                <option value="{{ data_get($country ?? [], 'asciiname', '') }}" @if(($country ?? "")==(data_get($country ?? [], 'asciiname', ''))) selected @endif>{{ data_get($country ?? [], 'asciiname', '') }}</option>
                                            @endforeach
                                        </select>
                                        <label>{{ __('quickad.country') }}</label>
                                    </div>
                                </div>
                                <!--end col-md-6-->
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="heading">{{ __('quickad.profile_tagline') }}</label>
                                        <input type="text" name="heading" id="heading" value="{{ $authortagline ?? '' }}">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-12-->
                            </div>
                            <!--end row-->
                            <div class="input-field col s12">
                                <label for="content">{{ __('quickad.about_me') }}</label>
                                <textarea class="materialize-textarea" id="content" rows="2" name="content">{{ $authorabout ?? '' }}</textarea>
                            </div>
                            <!--end input-field-->
                        </section>
                        <section><h3>{{ __('quickad.social_networks') }}</h3>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="facebook">Facebook</label>
                                        <input type="text" name="facebook" id="facebook" value="{{ $facebook ?? '' }}">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-6-->
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="twitter">Twitter</label>
                                        <input type="text" name="twitter" id="twitter" value="{{ $twitter ?? '' }}">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-6-->
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="googleplus">Google+</label>
                                        <input type="text" name="googleplus" id="googleplus" value="{{ $googleplus ?? '' }}">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-6-->
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="instagram">Instagram</label>
                                        <input type="text" name="instagram" id="instagram" value="{{ $instagram ?? '' }}">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="linkedin">Linked In</label>
                                        <input type="text" name="linkedin" id="linkedin" value="{{ $linkedin ?? '' }}">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-6-->
                                <div class="col-md-6 col-sm-6">
                                    <div class="input-field">
                                        <label for="youtube">Youtube</label>
                                        <input type="text" name="youtube" id="youtube" value="{{ $youtube ?? '' }}">
                                    </div>
                                    <!--end input-field-->
                                </div>
                                <!--end col-md-6-->
                            </div>
                            <!--end row-->
                        </section>
                        <div>
                            <h3>{{ __('quickad.newsletter') }}</h3>
                            <div class="row form-group">
                                <label class="col-md-1 label-title">&nbsp; </label>
                                <div class="col-md-11">
                                    <div class="checkbox checkbox-inline checkbox-primary ">
                                        <input type="checkbox" name="notify" id="notify" value="1" onchange="NotifyValueChanged()" @if(($notify ?? "")=="1") checked @endif>
                                        <label for="notify">{{ __('quickad.notifyemail') }}</label>
                                    </div>
                                    <div class="skills" style="margin: 0 25px">
                                        @foreach($category ?? [] as $category)
                                            <div class="checkbox checkbox-inline checkbox-primary">
                                                <input type="checkbox" name="choice[{{ data_get($category ?? [], 'id', '') }}]" id="{{ data_get($category ?? [], 'id', '') }}" value="{{ data_get($category ?? [], 'id', '') }}" {{ data_get($category ?? [], 'selected', '') }}>
                                                <label for="{{ data_get($category ?? [], 'id', '') }}">{{ data_get($category ?? [], 'name', '') }}</label>
                                            </div>
                                            <br>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                        <section class="center">
                            <div class="input-field">
                                <input type="submit" class="btn btn-primary btn-rounded" name="submit" value="{{ __('quickad.update') }}">
                            </div>
                            <!--end input-field-->
                        </section>
                    </form>
                    <!--end form-->
                </div>
                <!--end col-md-6-->
            </div>
            <!--end row-->
        </section>
    </div>
    <!--end container-->
</div>
<!--end page-content-->

@include('partials.footer')
<script type="text/javascript">
    function NotifyValueChanged()
    {
        if($('#notify').is(":checked"))
            $(".skills").show();
        else
            $(".skills").hide();
    }
    NotifyValueChanged();
</script>