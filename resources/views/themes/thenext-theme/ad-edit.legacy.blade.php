@include('partials.header')
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
            orakuploader_attach_images: [{{ $item_screens ?? '' }}],
            orakuploader_main_changed    : function (filename) {
                $("#mainlabel-images").remove();
                $("div").find("[filename='" + filename + "']").append("<div id='mainlabel-images' class='maintext'>Main Image</div>");
            },
            orakuploader_max_exceeded : function() {
                alert("You exceeded the max. limit of "+max_image_upload+" images.");
            },
            orakuploader_picture_deleted : function(filename) {
                    product_id = {{ $item_id ?? '' }},
                    action = "removeImage",
                    data = { action: action, product_id: product_id, imagename : filename };
                $.post(ajaxurl, data, function(response) {
                    // Remove Ads image from DOM/Server.
                    if(response != 0) {
                        $item.remove();
                        //alertify.success("Deleted! item has been Deleted.");
                    }else{
                        //alertify.error("Problem in deleting, Please try again.");
                    }
                    //jQuery('.confirm').removeClass('bookme-progress');
                    //swal.close();
                });
            }


        });
    });
</script>
<!-- Select Category Modal -->
<div class="zoom-anim-dialog mfp-hide popup-dialog big-dialog" id="categoryModal">
    <div class="popup-tab-content padding-0 tg-thememodal tg-categorymodal">
        <div class="tg-thememodaldialog">
            <div class="tg-thememodalcontent">
                <div class="tg-title">
                    <strong>{{ __('quickad.select') }} {{ __('quickad.category') }}</strong>
                </div>
                <div id="tg-dbcategoriesslider" class="tg-dbcategoriesslider tg-categories owl-carousel select-category post-option">
                    @foreach($category ?? [] as $category)
                        <div class="tg-category {{ data_get($category ?? [], 'selected', '') }}" data-ajax-catid="{{ data_get($category ?? [], 'id', '') }}" data-ajax-action="getsubcatbyidList" data-cat-name="{{ data_get($category ?? [], 'name', '') }}">
                            <div class="tg-categoryholder">
                                <div class="margin-bottom-10">
                                    @if((data_get($category ?? [], 'picture', ''))=="")
                                    <i class="{{ data_get($category ?? [], 'icon', '') }}"></i>
                                    @endif
                                    @if((data_get($category ?? [], 'picture', ''))!="")
                                    <img src="{{ data_get($category ?? [], 'picture', '') }}"/>
                                    @endif
                                </div>
                                <h3><a href="javascript:void()">{{ data_get($category ?? [], 'name', '') }}</a></h3>
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
</div>
<!-- Select Category Modal -->

<div id="titlebar" class="margin-bottom-0">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.edit_ad') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.edit_ad') }}</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<div class="section gray">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-md-12">
                <div id="post_error"></div>
                <div class="payment-confirmation-page dashboard-box margin-top-0 padding-top-0 margin-bottom-50" style="display: none">
                    <div class="headline">
                        <h3>{{ __('quickad.success') }}</h3>
                    </div>
                    <div class="content with-padding padding-bottom-10">
                        <i class="la la-check-circle"></i>
                        <h1 class="margin-top-30 margin-bottom-30">{{ __('quickad.success') }}</h1>
                        <p>{{ __('quickad.adsuccess') }}</p>
                    </div>
                </div>
                <form id="post-advertise-form" action="{{ $link['EDIT-AD'] ?? '#' }}?action=edit_ad" method="post" enctype="multipart/form-data" accept-charset="UTF-8">

                    <div class="dashboard-box margin-top-0">
                        <!-- Headline -->
                        <div class="headline">
                            <h3><i class="icon-feather-briefcase"></i> {{ __('quickad.ads_details') }}</h3>
                        </div>
                        <div class="content with-padding padding-bottom-10">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="form-group text-center">
                                        <a href="#categoryModal" id="choose-category" class="button popup-with-zoom-anim"><i class="icon-feather-check-circle"></i> &nbsp;{{ __('quickad.choose_category') }}</a>
                                    </div>
                                    <div class="form-group selected-product" id="change-category-btn" @if(($subcategory ?? "")=="") style='display: none' @endif>
                                    <ul class="select-category list-inline">
                                        <li id="main-category-text">{{ $category ?? '' }}</li>
                                        <li id="sub-category-text">{{ $subcategory ?? '' }}</li>
                                        <li class="active"><a href="#categoryModal" class="popup-with-zoom-anim"><i class="icon-feather-edit"></i> {{ __('quickad.edit') }}</a></li>
                                    </ul>

                                    <input type="hidden" id="input-maincatid" name="catid" value="{{ $catid ?? '' }}">
                                    <input type="hidden" id="input-subcatid" name="subcatid" value="{{ $subcatid ?? '' }}">
                                </div>
                                <div class="submit-field">
                                    <h5>{{ __('quickad.title') }} *</h5>
                                    <input type="text" class="with-border" name="title" value="{{ $title ?? '' }}" placeholder="{{ __('quickad.ad_title') }}">
                                </div>
                                <div class="submit-field">
                                    <h5>{{ __('quickad.description') }} *</h5>
                                    <textarea cols="30" rows="5" class="with-border text-editor" name="content" placeholder="{{ __('quickad.ad_description') }}">{{ $description ?? '' }}</textarea>
                                </div>

                                <div class="submit-field" id="quickad-photo-field">
                                    <div id="item_screen" orakuploader="on"></div>
                                    <input type="hidden" name="deletePrevImg" id="deletePrevImg" value=""/>
                                </div>
                                <div id="ResponseCustomFields">
                                    @foreach($customfields ?? [] as $customfields)
                                        @if('{{ data_get($customfields ?? [], 'type', '') }}'=="text-field")
                                        <div class="submit-field">
                                            <h5>{{ data_get($customfields ?? [], 'title', '') }}</h5>
                                            {{ data_get($customfields ?? [], 'textbox', '') }}
                                        </div>
                                    @endif
                                        @if('{{ data_get($customfields ?? [], 'type', '') }}'=="textarea")
                                        <div class="submit-field">
                                            <h5>{{ data_get($customfields ?? [], 'title', '') }}</h5>
                                            {{ data_get($customfields ?? [], 'textarea', '') }}
                                        </div>
                                    @endif
                                        @if('{{ data_get($customfields ?? [], 'type', '') }}'=="drop-down")
                                        <div class="submit-field">
                                            <h5>{{ data_get($customfields ?? [], 'title', '') }}</h5>
                                            <select class="selectpicker with-border quick-select" name="custom[{{ data_get($customfields ?? [], 'id', '') }}]" data-name="{{ data_get($customfields ?? [], 'id', '') }}"
                                                    data-req="{{ data_get($customfields ?? [], 'required', '') }}">
                                                <option value="" selected>{{ __('quickad.select') }} {{ data_get($customfields ?? [], 'title', '') }}</option>
                                                {{ data_get($customfields ?? [], 'selectbox', '') }}
                                            </select>
                                            <div class="quick-error">{{ __('quickad.field_required') }}</div>
                                        </div>
                                    @endif
                                        @if('{{ data_get($customfields ?? [], 'type', '') }}'=="radio-buttons")
                                        <div class="submit-field">
                                            <h5>{{ data_get($customfields ?? [], 'title', '') }}</h5>
                                            {{ data_get($customfields ?? [], 'radio', '') }}
                                        </div>
                                    @endif
                                        @if('{{ data_get($customfields ?? [], 'type', '') }}'=="checkboxes")
                                        <div class="submit-field">
                                            <h5>{{ data_get($customfields ?? [], 'title', '') }}</h5>
                                            {{ data_get($customfields ?? [], 'checkbox', '') }}
                                        </div>
                                    @endif
                                    @endforeach
                                </div>
                                <div class="submit-field" id="quickad-price-field">
                                    <h5>{{ __('quickad.price') }}</h5>
                                    <div class="row">
                                        <div class="col-xl-6 col-md-12">
                                            <div class="input-with-icon">
                                                <input class="with-border" type="text" placeholder="{{ __('quickad.price') }}" name="price" value="{{ $price ?? '' }}">
                                                <i class="currency">{{ $user_currency_sign ?? '' }}</i>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-12 margin-top-12">
                                            <div class="checkbox">
                                                <input type="checkbox" id="negotiable" name="negotiable" value="1" @if(($negotiable ?? "")=="1") checked @endif>
                                                <label for="negotiable"><span class="checkbox-icon"></span> {{ __('quickad.negotiate') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-field">
                                    <h5>{{ __('quickad.phone_no') }}</h5>
                                    <div class="row">
                                        <div class="col-xl-6 col-md-12">
                                            <div class="input-with-icon-left">
                                                <i class="flag-img"><img src="{{ $site_url ?? '' }}includes/assets/plugins/flags/images/{{ $user_country ?? '' }}.png"></i>
                                                <input type="text" class="with-border" name="phone" value="{{ $phone ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-md-12">
                                            <div class="checkbox margin-top-12">
                                                <input type="checkbox" name="hide_phone" id="phone" value="1" @if(($hidephone ?? "")=="1") checked @endif>
                                                <label for="phone"><span class="checkbox-icon"></span> {{ __('quickad.hide') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="submit-field">
                                    <h5>{{ __('quickad.city') }} *</h5>
                                    <select id="jobcity" class="with-border" name="city" data-size="7" title="{{ __('quickad.select') }} {{ __('quickad.city') }}">
                                        <option value="0" selected="selected">{{ __('quickad.select') }} {{ __('quickad.city') }}</option>
                                        @if(($city ?? "")!="") <option value="{{ $city ?? '' }}" selected="selected">{{ $cityname ?? '' }}</option> @endif
                                    </select>
                                </div>
                                @if(($post_address_mode ?? ""))
                                <div class="submit-field">
                                    <h5>{{ __('quickad.address') }}</h5>
                                    <div class="input-with-icon">
                                        <div id="autocomplete-container" data-autocomplete-tip="{{ __('quickad.type_enter') }}">
                                            <input class="with-border" type="text" placeholder="{{ __('quickad.address') }}" name="location" id="address-autocomplete">
                                        </div>
                                        <div class="geo-location"><i class="fa fa-crosshairs"></i></div>
                                    </div>
                                    <div class="map shadow" id="singleListingMap" data-latitude="{{ $latitude ?? '' }}" data-longitude="{{ $longitude ?? '' }}"  style="height: 200px"></div>
                                    <small>{{ __('quickad.drag_map_marker') }}</small>
                                </div>
                                @endif

                                <input type="hidden" id="latitude" name="latitude"  value="{{ $latitude ?? '' }}"/>
                                <input type="hidden" id="longitude" name="longitude" value="{{ $longitude ?? '' }}"/>

                                @if(($post_tags_mode ?? "")=="1")
                                <div class="submit-field form-group">
                                    <h5>{{ __('quickad.tags') }}</h5>
                                    <input class="with-border" type="text" name="tags" value="{{ $tags ?? '' }}">
                                    <small>{{ __('quickad.tags_detail') }}</small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
            </div>
            @if(!($logged_in ?? ""))
            <div class="dashboard-box">
                <!-- Headline -->
                <div class="headline">
                    <h3><i class="icon-feather-user"></i> {{ __('quickad.user_details') }}</h3>
                </div>
                <div class="content with-padding padding-bottom-10">
                    <div class="row">
                        <div class="col-xl-6 col-md-12">
                            <div class="submit-field">
                                <h5>{{ __('quickad.full_name') }} *</h5>
                                <div class="input-with-icon-left">
                                    <i class="la la-user"></i>
                                    <input type="text" class="with-border" name="user_name" value="{{ $seller_name ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-12">
                            <div class="submit-field">
                                <h5>{{ __('quickad.email') }} *</h5>
                                <div class="input-with-icon-left">
                                    <i class="la la-envelope"></i>
                                    <input type="email" class="with-border" name="user_email" value="{{ $seller_email ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(($post_premium_listing ?? ""))
            <div class="dashboard-box">
                <div class="headline">
                    <h3><i class="icon-feather-zap"></i> {{ __('quickad.make_premium') }} <small>({{ __('quickad.optional') }})</small></h3>
                </div>
                <div class="content with-padding">
                    <div class="payment">

                        <div class="payment-tab payment-tab-active">
                            <div class="payment-tab-trigger">
                                <input checked id="free" name="make_premium" type="radio" value="0">
                                <label for="free">{{ __('quickad.free_ad') }}</label>
                            </div>
                            <div class="payment-tab-content">
                                <p>{{ __('quickad.check_by_team') }}</p>
                            </div>
                        </div>

                        <div class="payment-tab">
                            <div class="payment-tab-trigger">
                                <input type="radio" name="make_premium" id="make_premium" value="1">
                                <label for="make_premium">{{ __('quickad.premium') }} <span class="badge green pull-right">{{ __('quickad.recommended') }}</span></label>
                            </div>

                            <div class="payment-tab-content">
                                <p>{{ __('quickad.upgrade_text_info') }}</p>
                                @if(($featured ?? "")!="1")
                                <div class="row premium-plans">
                                    <div class="col-lg-3">
                                        <div class="checkbox">
                                            <input type="checkbox" id="featured" name="featured" value="1" onchange="fillPrice(this,{{ $featured_price ?? '' }});">
                                            <label for="featured"><span class="checkbox-icon"></span> <span class="badge blue">{{ __('quickad.featured') }}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 premium-plans-text">
                                        {{ __('quickad.featured_ad_text') }}
                                    </div>
                                    <div class="col-lg-3 premium-plans-price">
                                        {{ $featured_fee ?? '' }} {{ __('quickad.for') }} {{ $featured_duration ?? '' }} {{ __('quickad.days') }}
                                    </div>
                                </div>
                                @endif
                                @if(($urgent ?? "")!="1")
                                <div class="row premium-plans">
                                    <div class="col-lg-3">
                                        <div class="checkbox">
                                            <input type="checkbox" id="urgent" name="urgent" value="1" onchange="fillPrice(this,{{ $urgent_price ?? '' }});">
                                            <label for="urgent"><span class="checkbox-icon"></span> <span class="badge yellow">{{ __('quickad.urgent') }}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 premium-plans-text">
                                        {{ __('quickad.urgent_ad_text') }}
                                    </div>
                                    <div class="col-lg-3 premium-plans-price">
                                        {{ $urgent_fee ?? '' }} {{ __('quickad.for') }} {{ $urgent_duration ?? '' }} {{ __('quickad.days') }}
                                    </div>
                                </div>
                                @endif
                                @if(($highlight ?? "")!="1")
                                <div class="row premium-plans">
                                    <div class="col-lg-3">
                                        <div class="checkbox">
                                            <input type="checkbox" id="highlight" name="highlight" value="1" onchange="fillPrice(this,{{ $highlight_price ?? '' }});">
                                            <label for="highlight"><span class="checkbox-icon"></span> <span class="badge red">{{ __('quickad.highlight') }}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 premium-plans-text">
                                        {{ __('quickad.highlight_ad_text') }}
                                    </div>
                                    <div class="col-lg-3 premium-plans-price">
                                        {{ $highlight_fee ?? '' }} {{ __('quickad.for') }} {{ $highlight_duration ?? '' }} {{ __('quickad.days') }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endif
            @if(($resubmit ?? "")=="1")
            <div class="dashboard-box">
                <!-- Headline -->
                <div class="headline">
                    <h3><i class="icon-feather-user"></i> {{ __('quickad.msg_reviewer') }}</h3>
                </div>
                <div class="content with-padding padding-bottom-10">
                    <div class="submit-field">
                        <h5>{{ __('quickad.comment') }} *</h5>
                        <textarea class="with-border" name="comments"></textarea>
                        <small>{{ __('quickad.comment_placeholder') }}</small>
                    </div>
                </div>
            </div>
            @endif
            <input type="hidden" name="product_id" value="{{ $item_id ?? '' }}">
            <input type="hidden" name="submit">
            <div class="row margin-top-30 margin-bottom-80" style="align-items: center;">
                <div class="col-6">
                    <button type="submit" id="submit_job_button" name="Submit" class="button ripple-effect big"><i class="icon-feather-plus"></i> {{ __('quickad.post_ad') }}</button>
                </div>
                <div class="col-6">
                    <div id="ad_total_cost_container" class="text-right" style="display: none">
                        <strong>
                            {{ __('quickad.total') }}:
                            <span class="currency-sign">{{ $currency_sign ?? '' }}</span>
                            <span id="totalPrice">0</span>
                            <span class="currency-code">{{ $currency_code ?? '' }}</span>
                        </strong>
                    </div>
                </div>
            </div>
            </form>
        </div>
        <div class="col-xl-4 hide-under-992px">
            <div class="dashboard-box margin-top-0">
                <!-- Headline -->
                <div class="headline">
                    <h3><i class="icon-feather-alert-circle"></i> {{ __('quickad.tips') }}</h3>
                </div>
                <div class="content with-padding padding-bottom-10">
                    <ul class="list-2">
                        <li>{{ __('quickad.post_job_tips1') }}</li>
                        <li>{{ __('quickad.post_job_tips2') }}</li>
                        <li>{{ __('quickad.post_job_tips3') }}</li>
                        <li>{{ __('quickad.post_job_tips4') }}</li>
                    </ul>
                </div>
            </div>

            {{ $ad_post_page_sidebar ?? '' }}
        </div>
    </div>
</div>
</div>
<script>
    var lang_edit_cat = "<i class='icon-feather-check-circle'></i> &nbsp;{{ __('quickad.edit_category') }}";
</script>
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/category-modal.css" type="text/css" rel="stylesheet">
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/owl.post.carousel.css" type="text/css" rel="stylesheet">
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/select2.min.css" rel="stylesheet" />
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/select2.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/{{ __('quickad.code') }}.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/owl.carousel-category.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/jquery.form.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/ad_post.js"></script>

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
                toolbar = ['bold','italic','underline','|','ol','ul','blockquote','table','link'];
                mobileToolbar = ["bold", "italic", "underline", "ul", "ol"];
                if (mobilecheck()) {
                    toolbar = mobileToolbar;
                }
                allowedTags = ['br','span','a','img','b','strong','i','strike','u','font','p','ul','ol','li','blockquote','pre','h1','h2','h3','h4','hr','table'];
                editor = new Simditor({
                    textarea: $('.text-editor'),
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
@endif

@if(($post_address_mode ?? ""))
    @if(($map_type ?? "")=="google")
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
        var path = '{{ $site_url ?? '' }}';
        var getCity = false;
        var getCountry = 'all';
        var color = '{{ $map_color ?? '' }}';
        var site_url = '{{ $site_url ?? '' }}';
        simpleMap(_latitude, _longitude, element);
    </script>
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
    <script>
        $('#jobcity').on('change', function() {
            var data = $("#jobcity option:selected").val();
            var custom_data= $("#jobcity").select2('data')[0];
            var latitude = custom_data.latitude;
            var longitude = custom_data.longitude;
            var address = custom_data.text;
            $('#latitude').val(latitude);
            $('#longitude').val(longitude);
            if (document.getElementById("singleListingMap") !== null && singleListingMap) {
                $("#address-autocomplete").val(address);
                var newLatLng = new L.LatLng(latitude, longitude);
                singleListingMapMarker.setLatLng(newLatLng);
                singleListingMap.flyTo(newLatLng, 10);
            }
        });
    </script>
    @endif
@endif
@include('partials.footer')
