@include('partials.header')
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/checkbox-radio.css" type="text/css" rel="stylesheet" >
<!-- ad-dashboard-page -->
<section id="main" class="clearfix  ad-profile-page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.dashboard') }}</li>
                <div class="pull-right back-result">
                    <a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>{{ __('quickad.back_result') }}</a>
                </div>
            </ol>
            <!-- breadcrumb -->
        </div>
        <!-- Main Content -->
        <div class="row">
            <!-- Page-Sidebar -->
            <aside class="col-sm-3 page-sidebar">
                <div class="section">
                    <div class="user-panel-sidebar">
                        <div class="collapse-box">
                            <h5 class="collapse-title no-border"> {{ __('quickad.my_classified') }} <a class="pull-right" data-toggle="collapse" href="#MyClassified"><i class="fa fa-angle-down"></i></a></h5>
                            <div id="MyClassified" class="panel-collapse collapse in">
                                <ul class="acc-list">
                                    <li class="active"><a href="{{ $link['DASHBOARD'] ?? '#' }}" class="waves-effect"><i class="fa fa-home"></i> {{ __('quickad.dashboard') }} </a></li>
                                    <li><a href="{{ $link['PROFILE'] ?? '#' }}/{{ $username ?? '' }}" class="waves-effect"><i class="fa fa-user"></i> {{ __('quickad.profile_public') }}</a></li>
                                    <li><a href="{{ $link['POST-AD'] ?? '#' }}" class="waves-effect"><i class="fa fa-pencil"></i> {{ __('quickad.post_ad') }}</a></li>
                                    <li><a href="{{ $link['MEMBERSHIP'] ?? '#' }}" class="waves-effect"><i class="fa fa-shopping-bag"></i> {{ __('quickad.membership') }} </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="collapse-box">
                            <h5 class="collapse-title"> {{ __('quickad.my_ads') }} <a class="pull-right" data-toggle="collapse" href="#MyAds"><i class="fa fa-angle-down"></i></a></h5>
                            <div id="MyAds" class="panel-collapse collapse in">
                                <ul class="acc-list">
                                    <li><a href="{{ $link['MYADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-book"></i> {{ __('quickad.my_ads') }}<span class="badge">{{ $myads ?? '' }}</span> </a></li>
                                    <li><a href="{{ $link['FAVADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-heart"></i> {{ __('quickad.favourite_ads') }}<span class="badge">{{ $favoriteads ?? '' }}</span> </a></li>
                                    <li><a href="{{ $link['PENDINGADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-info-circle"></i> {{ __('quickad.pending_ads') }}<span class="badge">{{ $pendingads ?? '' }}</span></a></li>
                                    <li><a href="{{ $link['HIDDENADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-eye-slash"></i> {{ __('quickad.hidden_ads') }} <span class="badge">{{ $hiddenads ?? '' }}</span></a></li>
                                    <li><a href="{{ $link['EXPIREADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-calendar-times-o"></i> {{ __('quickad.expire_ads') }} <span class="badge">{{ $expireads ?? '' }}</span></a>
                                    <li><a href="{{ $link['RESUBMITADS'] ?? '#' }}" class="waves-effect"><i class="fa fa-briefcase"></i> {{ __('quickad.resubmited_ads') }} <span class="badge">{{ $resubmitads ?? '' }}</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="collapse-box">
                            <h5 class="collapse-title no-border"> {{ __('quickad.my_account') }} <a class="pull-right" data-toggle="collapse" href="#account"><i class="fa fa-angle-down"></i></a></h5>
                            <div id="account" class="panel-collapse collapse in">
                                <ul class="acc-list">
                                    <li><a href="{{ $link['TRANSACTION'] ?? '#' }}" class="waves-effect"><i class="fa fa-money"></i> {{ __('quickad.transaction') }}</a></li>
                                    <li><a href="{{ $link['ACCOUNT_SETTING'] ?? '#' }}" class="waves-effect"><i class="fa fa-cog"></i> {{ __('quickad.account_setting') }}</a></li>
                                    <li><a href="{{ $link['LOGOUT'] ?? '#' }}" class="waves-effect"><i class="fa fa-unlock"></i> {{ __('quickad.logout') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <!-- # End Page-Sidebar -->
            <!-- Page-Content -->
            <div class="col-sm-9 page-content">
                <div class="panel-user-details">

                    <!-- profile-details -->
                    <div class="user-details section">
                        <div class="user-img"><img src="{{ $site_url ?? '' }}storage/profile/small_{{ $authorimg ?? '' }}" alt="{{ $authorname ?? '' }}" class="img-responsive"></div>
                        <div class="user-admin">
                            <h3><a href="#">{{ __('quickad.hello') }} {{ $authorname ?? '' }}</a>
                                @if(($sub_image ?? "")!="")
                                <img src="{{ $sub_image ?? '' }}" alt="{{ $sub_title ?? '' }}" title="{{ $sub_title ?? '' }}" width="28px"/>
                                @endif
                            </h3>
                            <div class="user-admin">
                                <div class="contacts">
                                    @if(($phone_verify ?? "")=="1")
                                     <figure class="social-links"><i class="fa fa-phone"></i><span><a href="tel:{{ $phone ?? '' }}">{{ $phone ?? '' }}</a> <img src="https://img.icons8.com/color/48/000000/verified-badge.png" alt="Verified" title="Verified" width="16px"/></span></figure>
                                    {{ $else ?? '' }}
                                    <figure class="social-links"><i class="fa fa-phone"></i><a href="#" data-toggle="modal" data-target="#mobile_verify_popup">Verify Mobile number</a></figure>
                                    @endif
                                </div>
                            </div>

                            <style>
                                .d-block {
                                    display: block!important;
                                }
                                .d-none {
                                    display: none!important;
                                }
                            </style>
                            <!-- Modal -->
                            <div id="mobile_verify_popup" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="text-center" id="mobile-form">
                                                <div class="mb-8 text-center forny-logo">
                                                    <img src="{{ $site_url ?? '' }}includes/assets/login_ajax/img/logo.png" width="100px" height="32px">
                                                </div>
                                                <div class="reset-form d-block">
                                                    <form action="dashboard_mobile_verify" name="pv-form" id="pv-form" method="post">
                                                        <h2 class="mb-5">{{ __('quickad.verify_mobile_no') }}</h2>
                                                        <p class="mb-10" id="mobile-status">
                                                            {{ __('quickad.otp_notify_1') }}
                                                        </p>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">+91</span>
                                                            <input required class="form-control" id="verify-mobile" name="mobile_no" type="phone" placeholder="{{ __('quickad.phone_no') }}">
                                                        </div>
                                                        <br>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <input type="hidden" value="1" name="dashboard_verify"/>
                                                                <input type="hidden" value="1" name="submit_mobile"/>
                                                                <button class="btn btn-primary btn-block" id="mobile-submit" type="submit">{{ __('quickad.send_otp') }}</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="reset-confirmation d-none">
                                                    <form action="otp_verify" name="otp-form" id="otp-form" method="post">
                                                        <h2 class="mb-5">{{ __('quickad.otp_verification') }}</h2>
                                                        <p class="mb-10" id="otp-status">
                                                            {{ __('quickad.otp_notify_2') }}: <span class="otp_mobile"></span>.
                                                        </p>
                                                        <div class="otp-form-content">
                                                            <div class="form-group">
                                                                <input required class="form-control" name="otp_code" type="text" placeholder="{{ __('quickad.otp_code') }}">
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <input type="hidden" value="" name="mobile_no" class="otp_mobile_no"/>
                                                                    <input type="hidden" value="1" name="submit_otp"/>
                                                                    <button class="btn btn-primary btn-block" id="otp-submit" type="submit">{{ __('quickad.verify_otp') }}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="text-center mt-10"><a href="#" class="go-back">{{ __('quickad.go_back') }}</a></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <br>
                            <span>{{ __('quickad.membership') }}  :
                                @if(($sub_title ?? "")!="") {{ $sub_title ?? '' }}  @endif
                                @if(($sub_title ?? "")=="") {{ __('quickad.free') }}  @endif
                            </span><br>

                            <small>{{ __('quickad.join_date') }}: {{ $join_date ?? '' }}</small>
                        </div>
                        <div class="user-ads-details">
                            <div class="my-quickad">
                                <h3><a href="{{ $link['MYADS'] ?? '#' }}">{{ $myads ?? '' }}</a></h3>
                                <small>{{ __('quickad.my_ads') }}</small>
                            </div>
                            <div class="favourites">
                                <h3><a href="{{ $link['FAVADS'] ?? '#' }}">{{ $favoriteads ?? '' }}</a></h3>
                                <small>{{ __('quickad.favourites') }}</small>
                            </div>
                        </div>
                    </div>
                    <!-- profile-details -->
                    <!-- My Details -->
                    <div class="section my-details">
                        @foreach($errors ?? [] as $errors)
                            <article class="byMsg byMsgError" id="formErrors">! {{ data_get($errors ?? [], 'message', '') }}</article>
                        @endforeach
                        <div class="section-title">
                            <h2>{{ __('quickad.my_details') }}</h2>
                        </div>
                        <div class="section-body">
                            <form class="row" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ __('quickad.full_name') }} <span class="required">*</span></label>

                                    <div class="col-sm-9">
                                        <input class="form-control border-form" type="text" name="name" value="{{ $authorname ?? '' }}" placeholder="{{ __('quickad.full_name') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ __('quickad.avatar') }}</label>
                                    <div class="col-sm-9">
                                        <input type="file" class="filestyle" id="filestyle-0" name="avatar" tabindex="-1" style="position: absolute; clip: rect(0px 0px 0px 0px);">
                                        <div class="bootstrap-filestyle input-group">
                                            <input type="text" class="border-form form-control " placeholder="" disabled="">
                                            <span class="group-span-filestyle input-group-btn" tabindex="0">
                                                <label for="filestyle-0" class="btn btn-outline btn-upload ">
                                                    <span class="icon-span-filestyle glyphicon glyphicon-folder-open"></span>
                                                    <span class="buttonText">{{ __('quickad.choose_file') }}</span>
                                                </label>
                                            </span>
                                        </div>
                                        <span class="help-block"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ __('quickad.phone_no') }} <span class="required">*</span></label>

                                    <div class="col-sm-9">
                                        <input type="text" class="form-control border-form" value="{{ $phone ?? '' }}" name="phone">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ __('quickad.address') }} <span class="required">*</span></label>

                                    <div class="col-sm-9">
                                        <input class="form-control border-form" type="text" name="address" value="{{ $address ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ __('quickad.website') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control border-form" value="{{ $website ?? '' }}" name="website">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ __('quickad.about_me') }}</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control border-form" id="pageContent" rows="2" name="content">{{ $authorabout ?? '' }}</textarea>
                                    </div>
                                </div>
                                <section>
                                    <div class="section-title">
                                        <h2>{{ __('quickad.social_networks') }}</h2>
                                    </div>
                                    <div class="row">
                                        <div class="input-field">
                                            <label for="facebook" class="col-sm-3 control-label active">Facebook</label>
                                            <div class="col-sm-9">
                                                <input class="form-control border-form" type="text" name="facebook" id="facebook" value="{{ $facebook ?? '' }}">
                                            </div>
                                        </div>
                                        <!--end input-field-->
                                        <div class="input-field">
                                            <label for="Twitter" class="col-sm-3 control-label active">Twitter</label>
                                            <div class="col-sm-9">
                                                <input class="form-control border-form" type="text" name="twitter" id="twitter" value="{{ $twitter ?? '' }}">
                                            </div>
                                        </div>
                                        <!--end input-field-->
                                        <div class="input-field">
                                            <label for="googleplus" class="col-sm-3 control-label active">Google+</label>
                                            <div class="col-sm-9">
                                                <input class="form-control border-form" type="text" name="googleplus" id="googleplus" value="{{ $googleplus ?? '' }}">
                                            </div>
                                        </div>
                                        <!--end input-field-->
                                        <div class="input-field">
                                            <label for="instagram" class="col-sm-3 active control-label">Instagram</label>
                                            <div class="col-sm-9">
                                                <input class="form-control border-form" type="text" name="instagram" id="instagram" value="{{ $instagram ?? '' }}">
                                            </div>
                                        </div>
                                        <!--end input-field-->
                                        <div class="input-field">
                                            <label for="linkedin" class="col-sm-3 control-label active">Linked In</label>
                                            <div class="col-sm-9">
                                                <input class="form-control border-form" type="text" name="linkedin" id="linkedin" value="{{ $linkedin ?? '' }}">
                                            </div>
                                        </div>
                                        <!--end input-field-->
                                        <div class="input-field">
                                            <label for="youtube" class="col-sm-3 control-label active">Youtube</label>
                                            <div class="col-sm-9">
                                                <input class="form-control border-form" type="text" name="youtube" id="youtube" value="{{ $youtube ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!--end row-->
                                </section>
                                <div>
                                    <div class="section-title">
                                        <h2>{{ __('quickad.newsletter') }}</h2>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-md-1 label-title">&nbsp; </label>
                                        <div class="col-md-11 subscribe-category">
                                            <div class="checkbox checkbox-primary">
                                                <input type="checkbox" name="notify" id="notify" value="1" onchange="NotifyValueChanged()" @if(($notify ?? "")=="1") checked @endif>
                                                <label for="notify">{{ __('quickad.notifyemail') }}</label>
                                            </div>
                                            <div class="skills" style="margin: 0 25px">
                                                @foreach($category ?? [] as $category)
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="choice[{{ data_get($category ?? [], 'id', '') }}]" id="{{ data_get($category ?? [], 'id', '') }}" value="{{ data_get($category ?? [], 'id', '') }}" {{ data_get($category ?? [], 'selected', '') }}>
                                                        <label for="{{ data_get($category ?? [], 'id', '') }}">{{ data_get($category ?? [], 'name', '') }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <button type="submit" class="btn btn-outline" name="submit"> {{ __('quickad.update') }}</button>
                                        <a href="#" class="btn btn-outline cancel">{{ __('quickad.cancel') }}</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- My Details -->
                </div>
                <!-- user-pro-edit -->
            </div>
            <!-- # End Page-Content -->
        </div>
        <!-- row -->
    </div>
    <!-- container -->
</section>
<!-- ad-dashboard-page -->

@include('partials.footer')
<!-- CRUD FORM CONTENT - crud_fields_scripts stack -->
<link media="all" rel="stylesheet" type="text/css" href="{{ $site_url ?? '' }}includes/assets/plugins/simditor/styles/simditor.css" />
<script src="{{ $site_url ?? '' }}includes/assets/plugins/simditor/scripts/mobilecheck.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/simditor/scripts/module.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/simditor/scripts/uploader.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/simditor/scripts/hotkeys.js"></script>
<script src="{{ $site_url ?? '' }}includes/assets/plugins/simditor/scripts/simditor.js"></script>
<script>
    (function() {
        $(function() {
            var $preview, editor, mobileToolbar, toolbar, allowedTags;
            Simditor.locale = 'en-US';
            toolbar = ['bold','italic','underline','fontScale','|','ol','ul','blockquote','table','link'];
            mobileToolbar = ["bold", "italic", "underline", "ul", "ol"];
            if (mobilecheck()) {
                toolbar = mobileToolbar;
            }
            allowedTags = ['br','span','a','img','b','strong','i','strike','u','font','p','ul','ol','li','blockquote','pre','h1','h2','h3','h4','hr','table'];
            editor = new Simditor({
                textarea: $('#pageContent'),
                placeholder: '',
                toolbar: toolbar,
                pasteImage: false,
                defaultImage: '{{ $site_url ?? '' }}includes/assets/plugins/simditor/images/image.png',
                upload: false,
                allowedTags: allowedTags
            });
            $preview = $('#preview');
            if ($preview.length > 0) {
                return editor.on('valuechanged', function(e) {
                    return $preview.html(editor.getValue());
                });
            }
        });
    }).call(this);
</script>

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
<script>
    "use strict"

    $(document).ready(function (e) {
        $('.go-back').on('click', function (e){
            $('.reset-confirmation')
                .removeClass('d-block')
                .addClass('d-none');
            $('.reset-form').addClass('d-block').removeClass('d-none');
        });
        $($('[name="pv-form"]')).on('submit', function (e){
            e.preventDefault();
            $("#mobile-submit").addClass('ajax-loader');
            var action = $("#pv-form").attr('action');
            var mobile_no = $("#verify-mobile").val();
            var form_data = $(this).serialize();
            $.ajax({
                type: "POST",
                url: ajaxurl+'?action='+action,
                data: form_data,
                success: function (response) {
                    if (response == "success") {
                        $('.otp_mobile').html(mobile_no);
                        $('.reset-form')
                            .removeClass('d-block')
                            .addClass('d-none');
                        $('.reset-confirmation').addClass('d-block').removeClass('d-none');

                        $('.reset-confirmation .otp_mobile_no').val(mobile_no);
                        $("#mobile-submit").removeClass('ajax-loader');
                    }
                    else {
                        $("#mobile-status").html('<span class="text-danger">'+response+'</span>');
                        $("#mobile-submit").removeClass('ajax-loader');
                    }
                }
            });
            return false;
        });

        $($('[name="otp-form"]')).on('submit', function (e){
            e.preventDefault();
            $("#otp-submit").addClass('ajax-loader');
            var action = $("#otp-form").attr('action');
            var form_data = $(this).serialize();
            $.ajax({
                type: "POST",
                url: ajaxurl+'?action='+action,
                data: form_data,
                success: function (response) {
                    if (response == "success") {
                        $(".otp-form-content").slideUp('slow', function () {
                            $("#otp-status").html('<span class="text-success">Thank you! Your phone number has been verified.</span>');
                            location.reload();
                        });
                    }
                    else {
                        $("#otp-status").html('<span class="text-danger">'+response+'</span>');
                        $("#otp-submit").removeClass('ajax-loader');
                    }
                }
            });
            return false;
        });
    });
</script>