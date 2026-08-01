<!DOCTYPE html>
<html lang="{{ __('quickad.code') }}" dir="{{ $language_direction ?? '' }}">
<head>
    <title>{{ $page_title ?? '' }} - {{ $site_title ?? '' }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="{{ $site_title ?? '' }}">
    <meta name="keywords" content="{{ $meta_keywords ?? '' }}">
    <meta name="description" content="{{ $meta_description ?? '' }}">

    <meta property="fb:app_id" content="{{ $facebook_app_id ?? '' }}" />
    <meta property="og:site_name" content="{{ $site_title ?? '' }}" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:url" content="{{ $link['POST-AD'] ?? '#' }}" />
    <meta property="og:title" content="{{ $page_title ?? '' }}" />
    <meta property="og:description" content="{{ $meta_description ?? '' }}" />
    <meta property="og:type" content="website" />
    <meta property="twitter:card" content="summary">
    <meta property="twitter:title" content="{{ $page_title ?? '' }}">
    <meta property="twitter:description" content="{{ $meta_description ?? '' }}">
    <meta property="twitter:domain" content="{{ $site_url ?? '' }}">
    <link rel="shortcut icon" href="{{ $site_url ?? '' }}storage/logo/{{ $site_favicon ?? '' }}">

    <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/css/icons.css">
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/bootstrap.min.css">


    <!-- Render-blocking styles -->
    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/base.css" type="text/css" rel="stylesheet" />
    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/PageLoggedOutPostAd.css" type="text/css" rel="stylesheet" />
    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/styles.css" type="text/css" rel="stylesheet">
    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/ModalDeferredLogin.less" type="text/css" rel="stylesheet">

    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/file-uploader.css" type="text/css" rel="stylesheet" />
    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/checkbox-radio.css" type="text/css" rel="stylesheet" >
    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/category-modal.css" type="text/css" rel="stylesheet">
    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/owl.post.carousel.css" type="text/css" rel="stylesheet">
    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/loader.css" type="text/css" rel="stylesheet">

    <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/flags/flags.min.css">

    <!-- Template Developed By Bylancer -->
    <script type='text/javascript' src='{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/jquery-2.2.1.min.js'></script>

    @if(($language_direction ?? "")=="rtl")
    <link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/post-rtl.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/bootstrap-rtl.min.css">
    @endif
    <!-- orakuploader -->
    <link type="text/css" href="{{ $site_url ?? '' }}plugins/orakuploader/orakuploader.css" rel="stylesheet"/>
    <script type="text/javascript" src="{{ $site_url ?? '' }}plugins/orakuploader/jquery.min.js"></script>
    <script type="text/javascript" src="{{ $site_url ?? '' }}plugins/orakuploader/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{ $site_url ?? '' }}plugins/orakuploader/orakuploader.js"></script>
    @if(($language_direction ?? "")=="rtl")
    <link type="text/css" href="{{ $site_url ?? '' }}plugins/orakuploader/orakuploader-rtl.css" rel="stylesheet"/>
    @endif
    <!-- orakuploader -->
    @if(($post_watermark ?? "")=="1")
    <script>
        var watermark_image = 'storage/logo/watermark.png';
    </script>
    @endif
    @if(($post_watermark ?? "")=="0")
    <script>
        var watermark_image = '';
    </script>
    @endif
    <script>
        var ajaxurl = "{{ $app_url ?? '' }}user-ajax.php";
        var lang_edit_cat = "{{ __('quickad.edit_category') }}";
        var lang_upload_images = "{{ __('quickad.upload_images') }}";
        var siteurl = '{{ $site_url ?? '' }}';
        var template_name = '{{ $tpl_name ?? '' }}';
        var max_image_upload = '{{ $max_image_upload ?? '' }}';

        // Language Var
        var LANG_MAIN_IMAGE = "{{ __('quickad.main_image') }}";
        var LANG_LOGGED_IN_SUCCESS = "{{ __('quickad.logged_in_success') }}";
        var LANG_ERROR_TRY_AGAIN = "{{ __('quickad.error_try_again') }}";
        var LANG_HIDDEN = "{{ __('quickad.hidden') }}";
        var LANG_ERROR = "{{ __('quickad.error') }}";
        var LANG_CANCEL = "{{ __('quickad.cancel') }}";
        var LANG_DELETED = "{{ __('quickad.deleted') }}";
        var LANG_ARE_YOU_SURE = "{{ __('quickad.are_you_sure') }}";
        var LANG_YOU_WANT_DELETE = "{{ __('quickad.you_want_delete') }}";
        var LANG_YES_DELETE = "{{ __('quickad.yes_delete') }}";
        var LANG_AD_DELETED = "{{ __('quickad.ad_deleted') }}";
        var LANG_SHOW = "{{ __('quickad.show') }}";
        var LANG_HIDE = "{{ __('quickad.hide') }}";
        var LANG_HIDDEN = "{{ __('quickad.hidden') }}";
        var LANG_ADD_FAV = "{{ __('quickad.add_favourite') }}";
        var LANG_REMOVE_FAV = "{{ __('quickad.remove_favourite') }}";
        var LANG_SELECT_CITY = "{{ __('quickad.select_city') }}";
        $(document).ready(function(){
            // -------------------------------------------------------------
            //  Intialize orakuploader
            // -------------------------------------------------------------
            $('#item_screen').orakuploader({
                site_url :  siteurl,
                orakuploader_path : 'plugins/orakuploader/',
                orakuploader_main_path : 'storage/products',
                orakuploader_thumbnail_path : 'storage/products/thumb',
                orakuploader_add_image : siteurl+'plugins/orakuploader/images/add.svg',
                orakuploader_watermark : watermark_image,
                orakuploader_add_label : lang_upload_images,
                orakuploader_use_main : true,
                orakuploader_use_sortable : true,
                orakuploader_use_dragndrop : true,
                orakuploader_use_rotation: false,
                orakuploader_resize_to : 800,
                orakuploader_thumbnail_size  : 250,
                orakuploader_maximum_uploads : max_image_upload,
                orakuploader_max_exceeded : max_image_upload,
                orakuploader_hide_on_exceed : true,
                orakuploader_main_changed    : function (filename) {
                    $("#mainlabel-images").remove();
                    $("div").find("[filename='" + filename + "']").append("<div id='mainlabel-images' class='maintext'>Main Image</div>");
                },
                orakuploader_max_exceeded : function() {
                    alert("You exceeded the max. limit of "+max_image_upload+" images.");
                }
            });
        });
    </script>
    <script>
        $('body').toggleClass('loaded');
        $(document).ready(function() {
            setTimeout(function(){
                $('body').addClass('loaded');
            }, 3000);

        });
    </script>
</head>
<body data-role="page" class="{{ $language_direction ?? '' }}" id="page" data-ipapi="{{ $live_location_api ?? '' }}" data-showlocationicon="{{ $location_track_icon ?? '' }}">
<div id="loader-wrapper">
    <div id="loader"></div>

    <div class="loader-section section-left"></div>
    <div class="loader-section section-right"></div>

</div>
<!-- /.modal -->

<!-- Change Country Modal -->
<div class="modal fade modalHasList" id="selectCountry" tabindex="-1" role="dialog" aria-labelledby="selectCountryLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">{{ __('quickad.close') }}</span>
                </button>
                <h4 class="modal-title uppercase font-weight-bold" id="selectCountryLabel">
                    <i class="icon-map"></i> {{ __('quickad.select_your_country') }}
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="row" style="padding: 0 20px">
                        <ul class="column col-md-12 col-sm-12 cities">
                            @foreach($countrylist ?? [] as $countrylist)
                                <li><span class="flag flag-{{ data_get($countrylist ?? [], 'lowercase_code', '') }}"></span><a href="{{ $link['POST-AD'] ?? '#' }}/{{ data_get($countrylist ?? [], 'lang', '') }}/{{ data_get($countrylist ?? [], 'lowercase_code', '') }}" data-id="{{ data_get($countrylist ?? [], 'id', '') }}" data-name="{{ data_get($countrylist ?? [], 'name', '') }}">{{ data_get($countrylist ?? [], 'name', '') }}</a></li>
                            @endforeach

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Change Country Modal -->
<!-- Select Category Modal -->
<div class="modal fade tg-thememodal tg-categorymodal" tabindex="-1" role="dialog">
    <div class="modal-dialog tg-thememodaldialog" role="document">
        <button type="button" id="dismiss-modal" class="tg-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <div class="modal-content tg-thememodalcontent">
            <div class="tg-title">
                <strong>{{ __('quickad.select_category') }}</strong>
            </div>
            <div id="tg-dbcategoriesslider" class="tg-dbcategoriesslider tg-categories owl-carousel select-category post-option">
                @foreach($category ?? [] as $category)
                    <div class="tg-category {{ data_get($category ?? [], 'selected', '') }}" data-ajax-catid="{{ data_get($category ?? [], 'id', '') }}" data-ajax-action="getsubcatbyidList" data-cat-name="{{ data_get($category ?? [], 'name', '') }}">
                        <div class="tg-categoryholder">
                            @if((data_get($category ?? [], 'picture', ''))=="")
                            <div><i class="{{ data_get($category ?? [], 'icon', '') }}"></i></div>
                            @endif
                            @if((data_get($category ?? [], 'picture', ''))!="")
                            <div><img src="{{ data_get($category ?? [], 'picture', '') }}"/></div>
                            @endif
                            <h3><a href="#">{{ data_get($category ?? [], 'name', '') }}</a></h3>
                        </div>
                    </div>
                @endforeach

            </div>
            <ul class="tg-subcategories" style="display: none">
                <li>
                    <div class="tg-title">
                        <strong>{{ __('quickad.select_subcategory') }}</strong><div id="sub-category-loader" style="visibility:hidden"></div>
                    </div>
                    <div class=" tg-verticalscrollbar tg-dashboardscrollbar">
                        <ul id="sub_category">

                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Select Category Modal -->
<!-- Link to existing Quickad account -->
<div class="quickad-template">
    <div class="responsive-modal hide" id="post_ad_email_exist">
        <section class="FacebookSignUpModal modal fb-bs-identifier">
            <header class="FacebookSignUpModal-header modal-header">
                <a class="close FacebookSignUpModal-close close-fb-modal-bs" data-dismiss="modal">
                    <span>&times;</span>
                </a>
                <h3 id="link_account_welcome" class="FacebookSignUpModal-heading" style="display: block">
                    {{ __('quickad.link_exist_account') }}
                </h3>
                <h3 id="link_account_success" class="FacebookSignUpModal-heading" style="display: none">
                    {{ __('quickad.link_account_success') }}
                </h3>
                <div id="link_account_error" class="FacebookSignUpModal-heading text-danger" style="display: none">{{ __('quickad.usernotfound') }}</div>
            </header>

            <div class="modal-body" id="fb-modal-body">
                <div class="FacebookSignUpModal-content">
                    <div id="post_loading" class="modal-loader Loader Loader--full" style="display: none"></div>

                    <!-- Link To Existing Account -->
                    <form class="FacebookSignUpModal-form fl-form" style="display:block">
                        <div class="form-step">
                            <p>
                                <span id="quickad_email_already_linked"></span>
                                <br>
                                <span>{{ __('quickad.enter_pass_link_ac') }}</span>
                            </p>
                        </div>
                        <div class="form-step">
                            <label>{{ __('quickad.username') }}:</label>
                            <div id="quickad_username_display"></div>
                        </div>
                        <div class="form-step">
                            <label>{{ __('quickad.email') }}:</label>
                            <div id="quickad_email_display"></div>
                        </div>
                        <div class="form-step">
                            <input type="hidden">
                            <label>{{ __('quickad.password') }}:</label>
                            <input type="password" class="default-input" id="password" name="password">
                            <p>
                                <a href="{{ $link['LOGIN'] ?? '#' }}?fstart=1" target="_blank" id="fb_forgot_password_btn">
                                    <small>{{ __('quickad.forgotpass') }}</small>
                                </a>
                            </p>
                        </div>
                        <div class="link-account-error alert alert-error" style="display:none"></div>
                        <div class="form-step">
                            <input type="hidden" name="email" id="email" value=""/>
                            <input type="hidden" name="username" id="username" value=""/>
                            <button id="link_account" type="button" value="Submit" class="btn btn-info FacebookSignUpModal-ctaButton">
                                {{ __('quickad.link_account') }}
                            </button>
                            <div class="FacebookSignUpModal-loader Loader Loader--full fb_submit_loading" style="display:none;" ></div>
                        </div>
                    </form>

                    <!-- Error -->
                    <div id="post_error_div" style="display:none">
                        <div id="post_error_content">

                        </div>
                        <button class="fbCloseBtn btn FacebookSignUpModal-ctaButton">{{ __('quickad.close') }}</button>
                    </div>

                </div>
            </div>
        </section>
        <div id="facebookConnect-backdrop" class="modal-backdrop"></div>
    </div>
</div>
<!-- Link to existing Quickad account -->

<!-- Premium Ad -->
<div class="quickad-template">
    <div class="responsive-modal hide" id="premium_ad_modal">
        <section class="FacebookSignUpModal modal fb-bs-identifier">
            <header class="FacebookSignUpModal-header modal-header">
                <a class="close FacebookSignUpModal-close close-fb-modal-bs" data-dismiss="modal"><span>&times;</span></a><br>
            </header>

            <div class="modal-body">
                <div class="ModalPayment-body">
                    <div id="post_loading" class="modal-loader Loader Loader--full" style="display: none"></div>
                    <figure class="ModalPayment-figure">
                        <img class="ModalPayment-image" src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/images/secure-payment.png" alt="Secure Payment">
                    </figure>
                    <div class="ModalPayment-heading">{{ __('quickad.confirm_payment') }}</div>

                    <div class="ModalPayment-subHeading">{{ __('quickad.upgrades') }}</div>
                    <div id="display_premium_tpl">

                    </div>

                </div>
                <div class="ModalPayment-footer">
                    <p>{{ __('quickad.confirm_payment_text') }}</p>
                    <button id="paymentModalConfirmButton" class="btn btn-large btn-success ModalPayment-footer-btn">{{ __('quickad.confirm_payment') }}</button>
                </div>
            </div>
        </section>
        <div id="facebookConnect-backdrop" class="modal-backdrop"></div>
    </div>
</div>
<!-- Premium Ad -->
<!-- /.modal -->

<div class="quickad-template">
    <main id="main" class="main-content">
        <section class="PagePostProject">
            <div class="PagePostProject-container container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="PagePostProject-bg-container">
                            <div class="PagePostProject-bg">
                                <div class="PagePostProject-bg-shape PagePostProject-bg-shape--one"></div>
                                <div class="PagePostProject-bg-shape PagePostProject-bg-shape--two"></div>
                                <div class="PagePostProject-bg-shape PagePostProject-bg-shape--three"></div>
                            </div>
                        </div>
                        <header class="PagePostProject-header">
                            <div class="PagePostProject-logo">
                                <!-- LOGO HERE -->
                                <a href="{{ $link['INDEX'] ?? '#' }}">
                                    <img class="img-responsive" src="{{ $site_url ?? '' }}storage/logo/{{ $site_logo ?? '' }}" alt="Logo">
                                </a>
                                @if(($country_type ?? "")=="multi")
                                <button class="flag-menu country-flag btn btn-default" id="#selectCountry" data-toggle="modal" data-target="#selectCountry" style="margin-left: 20px">
                                    <img src="{{ $site_url ?? '' }}includes/assets/plugins/flags/images/{{ $user_country ?? '' }}.png" style="float: left;">
                                </button>
                                @endif
                            </div>

                            <div id="post_success_uploaded" class="middle-container" style="display: none">
                                <div class="middle-dabba">
                                    <h1>{{ __('quickad.success') }}!</h1>

                                    <p>{{ __('quickad.adsuccess') }}</p>
                                </div>
                            </div>

                            <div id="ad_post_title">
                                <h1 class="PagePostProject-header-title">{{ __('quickad.post_advertise') }}</h1>
                                <p class="PagePostProject-header-desc">
                                    {{ __('quickad.post_advertise_quto') }}
                                </p>
                            </div>
                        </header>


                        <fl-project-form id="ad_post_form">
                            <div id="post_error">

                            </div>
                            <form class="fl-form" action="{{ $link['POST-AD'] ?? '#' }}?action=post_ad" id="post-advertise-form" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
                                <div class="form-group text-center">
                                    <a href="#" id="choose-category" class="tg-btn" data-toggle="modal" data-target=".tg-categorymodal">{{ __('quickad.choose_category') }}</a>
                                </div>
                                <div class="form-group selected-product" id="change-category-btn" style='display: none'>
                                    <ul class="select-category list-inline">
                                        <li id="main-category-text"></li>
                                        <li id="sub-category-text"></li>
                                        <li class="active"><a href="#" data-toggle="modal" data-target=".tg-categorymodal"><i class="fa fa-pencil-square-o"></i> {{ __('quickad.edit') }}</a></li>
                                    </ul>

                                    <input type="hidden" id="input-maincatid" name="catid" value="">
                                    <input type="hidden" id="input-subcatid" name="subcatid" value="">
                                </div>
                                <ol>
                                <li>
                                    <fieldset class="PagePostProject-fieldset">
                                        <legend class="PagePostProject-legend">{{ __('quickad.ad_title') }} *</legend>
                                        <ol>
                                            <li class="form-step">
                                                <input type="text" class="large-input focusable-field" placeholder="{{ __('quickad.ad_title') }}" name="title" value="">
                                                <div class="ng-active hidden">
                                                    <div class="form-error"></div>
                                                </div>
                                            </li>
                                        </ol>
                                    </fieldset>
                                    <fieldset class="PagePostProject-fieldset">
                                        <legend class="PagePostProject-legend">{{ __('quickad.description') }} *</legend>
                                        <ol>
                                            <li class="form-step">
                                                <textarea class="large-textarea focusable-field" id="pageContent" placeholder="{{ __('quickad.ad_description') }}..." name="content" rows="3"></textarea>
                                            </li>
                                        </ol>
                                    </fieldset>
                                </li>



                                <li id="quickad-photo-field">
                                    <fieldset class="PagePostProject-fieldset">
                                        <div id="item_screen" orakuploader="on"></div>
                                    </fieldset>
                                </li>

                                <li style="padding-bottom: 20px;">
                                    <fieldset class="PagePostProject-fieldset">
                                        <legend class="PagePostProject-legend">{{ __('quickad.additional_info') }}</legend>
                                    </fieldset>

                                    <div id='custom-field-block @if(($showcustomfield ?? "")!="1") hidden @endif'>
                                        <div id="ResponseCustomFields">
                                            @foreach($customfields ?? [] as $customfields)
                                                @if((data_get($customfields ?? [], 'type', ''))=="text-field")
                                                <div class="row form-group {{ data_get($customfields ?? [], 'title', '') }}">
                                                    <label class="col-sm-3 label-title">{{ data_get($customfields ?? [], 'title', '') }}</label>
                                                    <div class="col-sm-9">{{ data_get($customfields ?? [], 'textbox', '') }}</div>
                                                </div>
                                            @endif
                                                @if((data_get($customfields ?? [], 'type', ''))=="textarea")
                                                <div class="row form-group {{ data_get($customfields ?? [], 'title', '') }}">
                                                    <label class="col-sm-3 label-title">{{ data_get($customfields ?? [], 'title', '') }}</label>
                                                    <div class="col-sm-9">{{ data_get($customfields ?? [], 'textarea', '') }}</div>
                                                </div>
                                            @endif
                                            @if((data_get($customfields ?? [], 'type', ''))=="drop-down")
                                            <div class="row form-group {{ data_get($customfields ?? [], 'title', '') }}">
                                                <label class="col-sm-3 label-title">{{ data_get($customfields ?? [], 'title', '') }}</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control quick-select" name="custom[{{ data_get($customfields ?? [], 'id', '') }}]" data-name="{{ data_get($customfields ?? [], 'id', '') }}"
                                                            data-req="{{ data_get($customfields ?? [], 'required', '') }}">
                                                        <option value="" selected>{{ __('quickad.select') }} {{ data_get($customfields ?? [], 'title', '') }}</option>
                                                        {{ data_get($customfields ?? [], 'selectbox', '') }}
                                                    </select>
                                                    <div class="quick-error">{{ __('quickad.field_required') }}</div>
                                                </div>
                                            </div>
                                            @endif
                                                @if((data_get($customfields ?? [], 'type', ''))=="radio-buttons")
                                                <div class="row form-group {{ data_get($customfields ?? [], 'title', '') }}">
                                                    <label class="col-sm-3 label-title">{{ data_get($customfields ?? [], 'title', '') }}</label>
                                                    <div class="col-sm-9">{{ data_get($customfields ?? [], 'radio', '') }}</div>
                                                </div>
                                            @endif
                                                @if((data_get($customfields ?? [], 'type', ''))=="checkboxes")
                                                <div class="row form-group {{ data_get($customfields ?? [], 'title', '') }}">
                                                    <label class="col-sm-3 label-title">{{ data_get($customfields ?? [], 'title', '') }}</label>
                                                    <div class="col-sm-9">
                                                        {{ data_get($customfields ?? [], 'checkbox', '') }}
                                                    </div>
                                                </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="row form-group" id="quickad-price-field">
                                        <label class="col-sm-3 label-title">{{ __('quickad.price') }} </label>
                                        <div class="col-sm-9">
                                            <div class="input-group custom-input-group">
                                                <span class="input-group-addon currency-adon">{{ $user_currency_sign ?? '' }}</span>
                                                <input type="text" class="form-control" placeholder="e.g. 1000" name="price">
                                            </div>
                                            <label class="btn border-left-zero label-adon">
                                                <input type="checkbox" name="negotiable" id="negotiable"
                                                       class="FacebookSignUpModal-radio" value="1">{{ __('quickad.negotiate') }}
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <label class="col-sm-3 label-title">{{ __('quickad.mob_no') }} </label>
                                        <div class="col-sm-9">
                                            <div class="input-group custom-input-group">
                                                        <span class="input-group-addon" style="padding: 4px 10px;">
                                                            <img src="{{ $site_url ?? '' }}includes/assets/plugins/flags/images/{{ $user_country ?? '' }}.png">
                                                        </span>
                                                <input type="text" class="form-control" placeholder="e.g. 987654321" name="phone">
                                            </div>
                                            <label class="btn border-left-zero label-adon">
                                                <input type="checkbox" name="hide_phone" id="phone"
                                                       class="FacebookSignUpModal-radio" value="1">{{ __('quickad.hide') }}
                                            </label>
                                        </div>
                                    </div>
                                    @if(($post_tags_mode ?? "")=="1")
                                    <div class="row form-group">
                                        <label class="col-sm-3 label-title">{{ __('quickad.tags') }} </label>
                                        <div class="col-sm-9">
                                            <input name="tags" class="form-control" type="text" value="" placeholder="{{ __('quickad.tags') }}">
                                            <span>{{ __('quickad.tags_detail') }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </li>
                                <li>
                                    <fieldset class="PagePostProject-fieldset">
                                        <legend class="PagePostProject-legend">{{ __('quickad.city') }} *</legend>
                                        <ol>
                                            <li class="form-step">
                                                <select id="postadcity" name="city" class="large-input focusable-field" lang="ar">
                                                    <option value="0" selected="selected">{{ __('quickad.select_city') }}</option>
                                                </select>
                                            </li>
                                        </ol>
                                    </fieldset>



                                    <fieldset class='PagePostProject-fieldset @if(($post_address_mode ?? "")!="1") hidden @endif'>
                                        <legend class="PagePostProject-legend">{{ __('quickad.address') }}</legend>
                                        <ol>
                                            <li class="form-step">
                                                <div class="tg-inputwithicon">
                                                    <div id="autocomplete-container" data-autocomplete-tip="{{ __('quickad.type_enter') }}">
                                                        <input type="text" class="large-input focusable-field" placeholder="{{ __('quickad.ad_location') }}" name="location" id="address-autocomplete">
                                                    </div>
                                                    <div class="geo-location"><i class="fa fa-crosshairs"></i></div>
                                                    <div class="map height-200px shadow" id="singleListingMap" data-latitude="{{ $latitude ?? '' }}" data-longitude="{{ $longitude ?? '' }}" style="height: 200px"></div>
                                                    <p class="note" style="opacity: 1">{{ __('quickad.drag_map_marker') }}.</p>
                                                </div>
                                            </li>
                                        </ol>
                                    </fieldset>
                                    <input type="hidden" id="latitude" name="latitude"  value="{{ $latitude ?? '' }}"/>
                                    <input type="hidden" id="longitude" name="longitude" value="{{ $longitude ?? '' }}"/>
                                </li>

                                @if(($logged_in ?? "")=="0")
                                <li>
                                    <fieldset class="PagePostProject-fieldset" style="margin-bottom: 20px">
                                        <legend class="PagePostProject-legend">{{ __('quickad.seller_info') }} *</legend>
                                    </fieldset>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <fieldset class="PagePostProject-fieldset">
                                                <ol>
                                                    <li class="form-step">
                                                        <label>{{ __('quickad.seller_name') }}</label>
                                                        <input type="text" class="large-input focusable-field" placeholder="{{ __('quickad.seller_name') }}" name="seller_name">
                                                    </li>
                                                </ol>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <fieldset class="PagePostProject-fieldset">
                                                <ol>
                                                    <li class="form-step">
                                                        <label>{{ __('quickad.seller_email') }}</label>
                                                        <input type="text" class="large-input focusable-field" placeholder="{{ __('quickad.seller_email') }}" name="seller_email">
                                                    </li>
                                                </ol>
                                            </fieldset>
                                        </div>
                                    </div>


                                </li>
                                @endif

                                <!-- PACKAGE -->
                                <li>
                                    <div class='NumberedForm-content  @if(($post_premium_listing ?? "")=="0") hidden @endif'>
                                        <fieldset class="PagePostProject-fieldset">
                                            <legend class="PagePostProject-legend">{{ __('quickad.make_premium') }} {{ __('quickad.optional') }}</legend>
                                            <div class="PagePostProject-optionalTabs">
                                                <ul class="PagePostProject-optionalTabs-list">
                                                    <li class="PagePostProject-optionalTabs-item">
                                                        <input type="radio" id="standard" name="optional-upgrades" value="free" class="PagePostProject-selectableCard-input PagePostProject-optionalTabs-input ng-valid ng-dirty ng-touched">
                                                        <label for="standard" class="PagePostProject-selectableCard-label PagePostProject-optionalTabs-label">
                                                            <img class="PagePostProject-optionalTabs-icon" alt="decoration" src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/images/standard-project-icon.svg">
                                                            <div class="PagePostProject-optionalTabs-copy">
                                                                <h4 class="PagePostProject-optionalTabs-heading">{{ __('quickad.free_ad') }}</h4>
                                                                <p class="PagePostProject-optionalTabs-intro">{{ __('quickad.check_by_team') }}</p>
                                                            </div>
                                                            <div class="PagePostProject-optionalTabs-price PagePostProject-optionalTabs-price--large"><strong>{{ __('quickad.free') }}</strong></div>
                                                        </label>
                                                    </li>
                                                    <li class="PagePostProject-optionalTabs-item ">
                                                        <input type="radio" id="advanced" name="optional-upgrades" value="premium" class="PagePostProject-selectableCard-input PagePostProject-optionalTabs-input">
                                                        <label for="advanced" class="PagePostProject-selectableCard-label PagePostProject-optionalTabs-label">
                                                            <img class="PagePostProject-optionalTabs-icon" alt="decoration" src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/images/recruiter-icon.svg">
                                                            <div class="PagePostProject-optionalTabs-copy">
                                                                <h4 class="PagePostProject-optionalTabs-heading" >{{ __('quickad.premium') }} <span class="PagePostProject-optionalTabs-promotion">{{ __('quickad.recommended') }}</span></span></h4>
                                                                <p class="PagePostProject-optionalTabs-intro">{{ __('quickad.upgrade_text_info') }}</p>

                                                                <div class="PagePostProject-optionalTabs-content">
                                                                    <div class="PagePostProject-optionalTabs-content-inner">
                                                                        <table class="UpgradeListing">
                                                                            <tbody class="UpgradeListing-body">
                                                                            <!-- FEATURED UPGRADE -->
                                                                            <tr id="project-upgrade-item-featured" class="UpgradeListing-option">
                                                                                <td class="UpgradeListing-info">
                                                                                    <div class="Checkbox">
                                                                                        <input type="checkbox" name="featured" class="Checkbox-input focusable-field" id="featured" onclick="fillPrice(this,{{ $featured_price ?? '' }});">
                                                                                        <label class="UpgradeListing-checkbox Checkbox-label Checkbox-label--large" for="featured">
                                                                                            <span class="Checkbox-addIcon"></span>
                                                                                        </label>
                                                                                        <div class="UpgradeListing-tags">
                                                                                            <span class="UpgradeListing-promoTag promotion-tag has-no-icon promotion-featured">
                                                                                                {{ __('quickad.featured') }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>

                                                                                <td class="UpgradeListing-intro">
                                                                                    <p class="UpgradeListing-desc">{{ __('quickad.featured_ad_text') }}</p>
                                                                                </td>

                                                                                <td class="UpgradeListing-price js-upgrade-price">
                                                                                    <div id="priced_featured_upgrade_block" class="UpgradeListing-price-value">
                                                                                        <span id="featured-upgrade-price">{{ $featured_fee ?? '' }} {{ __('quickad.for') }} {{ $featured_duration ?? '' }} {{ __('quickad.days') }}</span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>

                                                                            <!-- URGENT UPGRADE -->
                                                                            <tr id="project-upgrade-item-urgent" class="UpgradeListing-option" data-robots="ProjectUpgradeUrgent">
                                                                                <td class="UpgradeListing-info">
                                                                                    <div class="Checkbox">
                                                                                        <input type="checkbox" name="urgent" data-name="urgent" class="Checkbox-input focusable-field" id="urgent" onclick="fillPrice(this,{{ $urgent_price ?? '' }});">
                                                                                        <label class="UpgradeListing-checkbox Checkbox-label Checkbox-label--large" for="urgent">
                                                                                            <span class="Checkbox-addIcon"></span>
                                                                                        </label>
                                                                                        <div class="UpgradeListing-tags">
                                                                                                                <span class="UpgradeListing-promoTag promotion-tag has-no-icon promotion-assisted">
                                                                                                                    {{ __('quickad.urgent') }}
                                                                                                                </span>
                                                                                        </div>
                                                                                    </div>

                                                                                </td>

                                                                                <td class="UpgradeListing-intro">
                                                                                    <p class="UpgradeListing-desc">{{ __('quickad.urgent_ad_text') }}</p>
                                                                                </td>

                                                                                <td class="UpgradeListing-price js-upgrade-price">
                                                                                    <div id="priced_urgent_upgrade_block" class="UpgradeListing-price-value">
                                                                                        <span id="urgent-upgrade-price">{{ $urgent_fee ?? '' }} {{ __('quickad.for') }} {{ $urgent_duration ?? '' }} {{ __('quickad.days') }}</span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>

                                                                            <!-- HIGHLIGHT UPGRADE -->
                                                                            <tr id="project-upgrade-item-private" class="UpgradeListing-option" data-robots="ProjectUpgradePrivate">
                                                                                <td class="UpgradeListing-info">
                                                                                    <div class="Checkbox">
                                                                                        <input type="checkbox" name="highlight" class="Checkbox-input focusable-field" id="highlight" value="" onclick="fillPrice(this,{{ $highlight_price ?? '' }});">
                                                                                        <label class="UpgradeListing-checkbox Checkbox-label Checkbox-label--large" for="highlight">
                                                                                            <span class="Checkbox-addIcon"></span>
                                                                                        </label>
                                                                                        <div class="UpgradeListing-tags">
                                                                                                                <span class="UpgradeListing-promoTag promotion-tag has-no-icon promotion-private">
                                                                                                                    {{ __('quickad.highlight') }}
                                                                                                                </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>


                                                                                <td class="UpgradeListing-intro">
                                                                                    <p class="UpgradeListing-desc" >{{ __('quickad.highlight_ad_text') }}</p>
                                                                                </td>

                                                                                <td class="UpgradeListing-price js-upgrade-price">

                                                                                    <div id="priced_private_upgrade_block" class="UpgradeListing-price-value">
                                                                                        <span id="private-upgrade-price">{{ $highlight_fee ?? '' }} {{ __('quickad.for') }} {{ $highlight_duration ?? '' }} {{ __('quickad.days') }}</span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>

                                                                        </table>

                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </fieldset>
                                    </div>
                                </li>
                                <!-- PACKAGE -->

                                <li>
                                    <div class="PagePostProject-submit">
                                        <input type="hidden" name="submit">
                                        <button id="submit_advertise" class="btn btn-xlarge btn-info " type="submit" name="Submit"><span>{{ __('quickad.post_y_ad') }}</span></button>
                                        <div id="ad_total_cost_container" class="PagePostProject-totalCost" style="display: none;">
                                            <strong>
                                                {{ __('quickad.total') }}:
                                                <span class="currency-sign">{{ $currency_sign ?? '' }}</span>
                                                <span id="totalPrice">0</span>
                                                <span class="currency-code">{{ $currency_code ?? '' }}</span>
                                            </strong>

                                        </div>
                                    </div>

                                    <p class="PagePostProject-submit-terms">
                                        {{ __('quickad.click_con') }}
                                        <a  class="PagePostProject-submit-link" target="_blank" href="{{ $termcondition_link ?? '' }}">{{ __('quickad.term_con') }}</a>
                                        {{ __('quickad.and') }}
                                        <a  class="PagePostProject-submit-link" target="_blank" href="{{ $privacy_link ?? '' }}">{{ __('quickad.privacy') }}</a>
                                        <br>
                                    </p>
                                </li>
                            </ol>
                            </form>
                        </fl-project-form>

                        <footer class="FooterLite">
                            <p class="FooterLite-copyright">
                                {{ $copyright_text ?? '' }}
                            </p>
                            <nav class="FooterLite-nav">
                                @foreach($htmlpage ?? [] as $htmlpage)
                                    <a href="{{ data_get($htmlpage ?? [], 'link', '') }}" class="FooterLite-nav-link">{{ data_get($htmlpage ?? [], 'title', '') }}</a>
                                @endforeach
                            </nav>
                        </footer>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
<script>
    var lang_code = '{{ __('quickad.code') }}';
    var lang_code = lang_code.toLowerCase();
    var ajaxurl = "{{ $app_url ?? '' }}user-ajax.php";
    var lang_edit_cat = "{{ __('quickad.edit_category') }}";
</script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/bootstrap.min.js"></script>
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/select2/select2.min.css" rel="stylesheet" />
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/select2/select2.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/owl.carousel-category.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/{{ __('quickad.code') }}.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/ajaxForm/jquery.form.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/ad_post_js.js"></script>
@if(($post_desc_editor ?? "")=="1")
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
                placeholder: '{{ __('quickad.ad_description') }}',
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
@endif

@if(($post_address_mode ?? ""))
    @if(($map_type ?? "")=="google")
        <!-- If address mode enable: ADDRESS FIELD JAVASCRIPT -->
        <link href="{{ $site_url ?? '' }}includes/assets/plugins/map/google/map-marker.css" type="text/css" rel="stylesheet">
        <script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/jquery-migrate-1.2.1.min.js'></script>
        <script type='text/javascript' src='//maps.google.com/maps/api/js?key={{ $gmap_api_key ?? '' }}&#038;libraries=places%2Cgeometry&#038;ver=2.2.1'></script>
        <script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/richmarker-compiled.js'></script>
        <script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/markerclusterer_packed.js'></script>
        <script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/gmapAdBox.js'></script>
        <script type='text/javascript' src='{{ $site_url ?? '' }}includes/assets/plugins/map/google/maps.js'></script>

        <script>
            var _latitude = '{{ $latitude ?? '' }}';
            var _longitude = '{{ $longitude ?? '' }}';
            var element = "singleListingMap";
            var color = '#9C27B0';
            var zoom = '#9C27B0';
            var getCity = false;
            var path = '{{ $site_url ?? '' }}';
            var Countries = '{{ $user_country ?? '' }}';
            if(Countries != ""){
                var str = Countries;
                var str_array = str.split(',');
                var getCountry = [];
                for(var i = 0; i < str_array.length; i++)
                {
                    getCountry.push(str_array[i]);

                }
            }
            else{
                var getCountry = "all";
            }
            simpleMap(_latitude, _longitude, element, true);

            $('#postadcity').on('change', function() {
                var data = $("#postadcity option:selected").val();
                var custom_data= $("#postadcity").select2('data')[0];
                var latitude = custom_data.latitude;
                var longitude = custom_data.longitude;
                var address = custom_data.text;
                $('#latitude').val(latitude);
                $('#longitude').val(longitude);
                simpleMap(latitude, longitude, element, true, address);
            });
        </script>
        <!-- If address mode enable: ADDRESS FIELD JAVASCRIPT -->
    {{ $else ?? '' }}
<script>
    var openstreet_access_token = '{{ $openstreet_access_token ?? '' }}';
</script>
        <link rel="stylesheet" href="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/css/style.css">
        <!-- Leaflet // Docs: https://leafletjs.com/ -->
        <script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet.min.js"></script>
        <!-- Leaflet Maps Scripts (locations are stored in leaflet-quick.js) -->
        <script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-markercluster.min.js"></script>
        <script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-gesture-handling.min.js"></script>
        <script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-quick.js"></script>
        <!-- Leaflet Geocoder + Search Autocomplete // Docs: https://github.com/perliedman/leaflet-control-geocoder -->
        <script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-autocomplete.js"></script>
        <script src="{{ $site_url ?? '' }}includes/assets/plugins/map/openstreet/leaflet-control-geocoder.js"></script>
    @endif
@endif
</body>
</html>