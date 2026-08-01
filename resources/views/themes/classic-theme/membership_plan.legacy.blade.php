@include('partials.header')
<link href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/post-ad/checkbox-radio.css" type="text/css" rel="stylesheet" >

<style>

    .margin-bottom-70 {
        margin-bottom: 70px!important;
    }
    .billing-cycle-radios .radio label::before {
         top: 6px;
     }
    .billing-cycle-radios .radio label::after {
        top: 9px;
    }
    .billing-cycle-radios{display:block;margin:0 auto;text-align:center}
    .billing-cycle-radios.text-align-left{text-align:left}

    .pricing-plans-container{display:flex}
    .pricing-plan{flex:1;padding:30px;position:relative;margin-right:30px;border-radius:4px;border:1px solid #e0e0e0;box-shadow:0 1px 4px 0 rgba(0,0,0,.05)}
    .pricing-plan:last-of-type{margin-right:0}
    .pricing-plan h3{font-size:20px;font-weight:600}
    .pricing-plan p{margin:0}
    .billed-yearly-label{display:none}
    .billed-yearly .billed-yearly-label{display:block}
    .billed-yearly .billed-monthly-label{display:none}
    .pricing-plan-label{background:#f6f6f6;border-radius:4px;font-size:18px;color:#888;text-align:center;line-height:24px;padding:15px;margin:22px 0}
    .pricing-plan-label strong{font-size:32px;font-weight:700;color:#333;margin-right:5px;line-height:30px}
    .recommended .pricing-plan-label{background-color:var(--theme-color-0_06);color:var(--theme-color-1)}
    .recommended .pricing-plan-label strong{color:var(--theme-color-1)}
    .pricing-plan-features strong{color:#333;font-weight:600;margin-bottom:5px;line-height:24px;display:inline-block}
    .pricing-plan-features ul{padding:0;margin:0}
    .pricing-plan-features ul li{display:block;margin:0;padding:3px 0;line-height:24px}
    .pricing-plan .button:hover,.pricing-plan.recommended .button{color:#fff;background-color:var(--theme-color-1);box-shadow:0 4px 12px var(--theme-color-0_15)}
    .pricing-plan .button{color:var(--theme-color-1);background-color:#fff;border:1px solid var(--theme-color-1);box-shadow:0 4px 12px var(--theme-color-0_1)}
    .pricing-plan .button:hover{box-shadow:0 4px 12px var(--theme-color-0_15)}
    .pricing-plan.recommended{box-shadow:0 0 45px var(--theme-color-0_09)}
    @media (max-width:992px){
        .pricing-plans-container{display:block}
        .pricing-plan{margin-bottom:30px;flex:auto;width:100%}
    }
    .pricing-plan.recommended:last-of-type {
        margin-right: 0;
    }

    .pricing-plan.recommended {
        box-shadow: 0 0 45px rgba(0, 0, 0, .09);
        padding: 35px;
        margin: 0 15px;
    }
    .pricing-plan .recommended-badge {
        background-color: #66676b;
        color: #fff;
        position: absolute;
        width: 100%;
        height: 45px;
        top: -45px;
        left: 0;
        text-align: center;
        border-radius: 4px 4px 0 0;
        font-weight: 600;
        line-height: 45px;
    }
    .pricing-plan .recommended-badge {
        background-color: var(--theme-color-1);
    }
    .billed-yearly-label, .billed-lifetime-label { display: none }
    .billed-yearly .billed-yearly-label, .billed-lifetime .billed-lifetime-label { display: block }
    .billed-yearly .billed-monthly-label, .billed-lifetime .billed-monthly-label { display: none }
    .headline-border-top{border-top:1px solid #e0e0e0}
    .boxed-widget{background-color:#f9f9f9;padding:0;transform:translate3d(0,0,0);z-index:90;position:relative;border-radius:4px;overflow:hidden}
    .boxed-widget-headline{color:#333;font-size:20px;padding:20px 30px;background-color:#f0f0f0;color:#333;position:relative;border-radius:4px 4px 0 0}
    .boxed-widget-headline h3{font-size:20px;padding:0;margin:0}
    .boxed-widget-inner{padding:30px}
    .boxed-widget ul{list-style:none;padding:0;margin:0}
    .boxed-widget ul li span{float:right;color:#333;font-weight:600}
    .boxed-widget ul li{color:#666;padding-bottom:1px}
    .boxed-widget.summary li.total-costs{font-size:18px;border-top:1px solid #e4e4e4;padding-top:18px;margin-top:18px}
    .boxed-widget-footer{border-top:1px solid #e4e4e4;width:100%;padding:20px 30px}
    .boxed-widget-footer .checkbox label{margin-bottom:0}
    .boxed-widget.summary li.total-costs span{font-weight:700;color:var(--theme-color-1)}
    .listing-item-container.compact.order-summary-widget .listing-item{border-radius:4px 4px 0 0;cursor:default;height:240px}
    .listing-item-container.compact.order-summary-widget{margin-bottom:0}
    .listing-item-container.compact.order-summary-widget:hover{transform:none}
    .billing-cycle{display:flex}
    .billing-cycle .radio{flex:1;margin:5px 20px 5px 0}
    .billing-cycle .radio label{border-radius:4px;border:2px solid #eee;padding:25px;height:100%;align-self:center}
    .billing-cycle .radio:last-of-type{margin-right:0}
    .billing-cycle .radio input[type=radio]+label .radio-label{position:relative;top:2px;margin-right:7px}
    .billing-cycle-details{display:block;padding-left:30px}
    .discounted-price-tag,.regular-price-tag{font-size:14px;background:#e0f5d7;color:#449626;border-radius:4px;line-height:20px;padding:4px 10px;flex-grow:0;flex:auto;width:auto;transition:.3s;margin-top:6px;margin-right:5px;display:inline-block}
    .line-through{text-decoration:line-through;background-color:#fbf6dd;color:#a18d29}
    @media (max-width:768px){
        .billing-cycle{display:flex;flex-direction:column}
        .billing-cycle .radio{margin-right:0}
    }
</style>
<!-- Titlebar
================================================== -->
<div id="titlebar" class="margin-bottom-0">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.membership') }}</h2>
                <div class="breadcrumb-section">
                    <!-- breadcrumb -->
                    <ol class="breadcrumb">
                        <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                        <li class="active">{{ __('quickad.membership') }}</li>
                        <div class="pull-right back-result"><a href="{{ $link['DASHBOARD'] ?? '#' }}"><i class="fa fa-angle-double-left"></i> {{ __('quickad.back_result') }}</a></div>
                    </ol>
                    <!-- breadcrumb -->
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Page Content
================================================== -->
<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <form name="form1" method="post">
                <div class="billing-cycle-radios margin-bottom-70">

                    @if(($total_monthly ?? "")!="0")
                    <div class="radio billed-monthly-radio radio-primary radio-inline">
                        <input id="radio-monthly" name="billed-type" type="radio" value="monthly" checked="">
                        <label for="radio-monthly"><span class="radio-label"></span> {{ __('quickad.monthly') }}</label>
                    </div>
                    @endif
                    @if(($total_annual ?? "")!="0")
                    <div class="radio billed-yearly-radio radio-primary radio-inline">
                        <input id="radio-yearly" name="billed-type" type="radio" value="yearly">
                        <label for="radio-yearly"><span class="radio-label"></span> {{ __('quickad.yearly') }}</label>
                    </div>
                    @endif
                    @if(($total_lifetime ?? "")!="0")
                    <div class="radio billed-lifetime-radio radio-primary radio-inline">
                        <input id="radio-lifetime" name="billed-type" type="radio" value="lifetime">
                        <label for="radio-lifetime"><span class="radio-label"></span> {{ __('quickad.lifetime') }}</label>
                    </div>
                    @endif
                </div>
                <!-- Pricing Plans Container -->
                <div class="pricing-plans-container">
                    @foreach($sub_types ?? [] as $sub_types)
                        <!-- Plan -->
                        <div class='pricing-plan @if((data_get($sub_types ?? [], 'recommended', ''))=="yes") recommended @endif'>
                            @if((data_get($sub_types ?? [], 'recommended', ''))=="yes") <div class="recommended-badge">{{ __('quickad.recommended') }}</div> @endif
                            <h3>{{ data_get($sub_types ?? [], 'title', '') }}</h3>
                            @if((data_get($sub_types ?? [], 'id', ''))=="free" || (data_get($sub_types ?? [], 'id', ''))=="trial")
                            <div class="pricing-plan-label"><strong>
                                    @if((data_get($sub_types ?? [], 'id', ''))=="free")
                                    {{ __('quickad.free') }}
                                    {{ $else ?? '' }}
                                    {{ __('quickad.trial') }}
                                    @endif
                                </strong></div>
                            {{ $else ?? '' }}
                            @if(($total_monthly ?? "")!="0")
                            <div class="pricing-plan-label billed-monthly-label"><strong>{{ data_get($sub_types ?? [], 'monthly_price', '') }}</strong>/ {{ __('quickad.monthly') }}</div>
                            @endif
                            @if(($total_annual ?? "")!="0")
                            <div class="pricing-plan-label billed-yearly-label"><strong>{{ data_get($sub_types ?? [], 'annual_price', '') }}</strong>/ {{ __('quickad.yearly') }}</div>
                            @endif
                            @if(($total_lifetime ?? "")!="0")
                            <div class="pricing-plan-label billed-lifetime-label"><strong>{{ data_get($sub_types ?? [], 'lifetime_price', '') }}</strong> {{ __('quickad.lifetime') }}</div>
                            @endif
                            @endif
                            <div class="pricing-plan-features">
                                <strong>{{ __('quickad.features_of') }} {{ data_get($sub_types ?? [], 'title', '') }}</strong>
                                <ul>
                                    <li>{{ data_get($sub_types ?? [], 'limit', '') }} {{ __('quickad.ad_post_limit') }}</li>
                                    <li>{{ data_get($sub_types ?? [], 'duration', '') }} {{ __('quickad.days') }} {{ __('quickad.ad_exp_in') }}</li>
                                    <li>{{ __('quickad.featured_fee') }} {{ $currency_sign ?? '' }}{{ data_get($sub_types ?? [], 'featured_fee', '') }} {{ __('quickad.for') }} {{ data_get($sub_types ?? [], 'featured_duration', '') }} {{ __('quickad.days') }}</li>
                                    <li>
                                        {{ __('quickad.urgent_fee') }} {{ $currency_sign ?? '' }}{{ data_get($sub_types ?? [], 'urgent_fee', '') }} {{ __('quickad.for') }} {{ data_get($sub_types ?? [], 'urgent_duration', '') }} {{ __('quickad.days') }}
                                    </li>
                                    <li>
                                        {{ __('quickad.highlight_fee') }} {{ $currency_sign ?? '' }}{{ data_get($sub_types ?? [], 'highlight_fee', '') }} {{ __('quickad.for') }} {{ data_get($sub_types ?? [], 'highlight_duration', '') }} {{ __('quickad.days') }}
                                    </li>
                                    <li>
                                        @if((data_get($sub_types ?? [], 'top_search_result', ''))=="yes")
                                        <span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span>
                                        {{ $else ?? '' }}
                                        <span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span>
                                        @endif
                                        {{ __('quickad.top_search_result') }}
                                    </li>
                                    <li>
                                        @if((data_get($sub_types ?? [], 'show_on_home', ''))=="yes")
                                        <span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span>
                                        {{ $else ?? '' }}
                                        <span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span>
                                        @endif
                                        {{ __('quickad.show_on_home') }}
                                    </li>
                                    <li>
                                        @if((data_get($sub_types ?? [], 'show_in_home_search', ''))=="yes")
                                        <span class="icon-text yes"><i class="icon-feather-check-circle margin-right-2"></i></span>
                                        {{ $else ?? '' }}
                                        <span class="icon-text no"><i class="icon-feather-x-circle margin-right-2"></i></span>
                                        @endif
                                        {{ __('quickad.show_in_home_search') }}
                                    </li>
                                    {{ data_get($sub_types ?? [], 'custom_settings', '') }}
                                </ul>
                            </div>
                            @if((data_get($sub_types ?? [], 'Selected', ''))=="0")
                            <button type="submit" class="btn btn-primary text-align-center margin-top-20 ripple-effect" name="upgrade" value="{{ data_get($sub_types ?? [], 'id', '') }}">{{ __('quickad.upgrade') }}</button>
                            @endif
                            @if((data_get($sub_types ?? [], 'Selected', ''))=="1")
                            <a href="javascript:void(0);" class="button full-width margin-top-20 ripple-effect">
                                {{ __('quickad.current_plan') }}
                            </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>
</div>
<div class="margin-top-80"></div>
@include('partials.footer')
<script>
    $(document).ready(function () {
        $('.billing-cycle-radios').on("click", function () {
            if ($('.billed-yearly-radio input').is(':checked')) {
                $('.pricing-plans-container').addClass('billed-yearly').removeClass('billed-lifetime');
            }
            if ($('.billed-monthly-radio input').is(':checked')) {
                $('.pricing-plans-container').removeClass('billed-yearly').removeClass('billed-lifetime');
            }
            if ($('.billed-lifetime-radio input').is(':checked')) {
                $('.pricing-plans-container').addClass('billed-lifetime').removeClass('billed-yearly');
            }
        });
    });
</script>
