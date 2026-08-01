@include('partials.header')
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
<!-- Pricing Plans -->
<section class="bg-white py-8">

    <div class="container">

        <div class="section-title text-center mb-8 position-relative">
            <h1 class="font-weight-semibold">{{ __('quickad.membershipplan') }}</h1>
            <p class="font-weight-bold text-gray-600 quickad_lang_translator" data-quickad-lang="{{ __('quickad.all_packages') }}">{{ __('quickad.all_packages') }}</p>
        </div>
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
            <div class="owl-carousel pricing-plans-carousel pricing-plans-container" data-owl-items="3" data-owl-dots="3">
            @foreach($sub_types ?? [] as $sub_types)

                <div class="p-3">
                    <div class="pricing-plan recommended text-center border py-7 px-2 bg-light rounded-10">
                        @if((data_get($sub_types ?? [], 'recommended', ''))=="1") <div class="ribbon"><i class="fa fa-star-o"></i></div> @endif
                        <h4 class="font-weight-bold text-gray-600 mb-4">{{ data_get($sub_types ?? [], 'title', '') }}</h4>

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
                        <ul class="list-unstyled mb-6">
                            <li class="mb-2">
                                <span class="icon-text yes"><i class="fa fa-check-circle mr-2"></i></span>
                                {{ __('quickad.ad_exp_in') }} : <span class="font-weight-bold">{{ data_get($sub_types ?? [], 'duration', '') }} </span> {{ __('quickad.days') }}</li>
                            <li class="mb-2">
                                <span class="icon-text yes"><i class="fa fa-check-circle mr-2"></i></span>
                                {{ __('quickad.featured_fee') }} <span class="font-weight-bold">{{ $currency_sign ?? '' }}{{ data_get($sub_types ?? [], 'featured_fee', '') }}</span></li>
                            <li class="mb-2">
                                <span class="icon-text yes"><i class="fa fa-check-circle mr-2"></i></span>
                                {{ __('quickad.urgent_fee') }} <span class="font-weight-bold">{{ $currency_sign ?? '' }}{{ data_get($sub_types ?? [], 'urgent_fee', '') }}</span></li>
                            <li class="mb-2">
                                <span class="icon-text yes"><i class="fa fa-check-circle mr-2"></i></span>
                                {{ __('quickad.highlight_fee') }} <span class="font-weight-bold">{{ $currency_sign ?? '' }}{{ data_get($sub_types ?? [], 'highlight_fee', '') }}</span></li>
                            <li class="mb-2">
                                @if((data_get($sub_types ?? [], 'top_search_result', ''))=="yes")
                                <span class="icon-text yes"><i class="fa fa-check-circle mr-2"></i></span>
                                @endif
                                @if((data_get($sub_types ?? [], 'top_search_result', ''))=="no")
                                <span class="icon-text no"><i class="fa fa-times-circle mr-2"></i></span>
                                @endif
                                {{ __('quickad.top_search_result') }}</li>
                            <li class="mb-2">
                                @if((data_get($sub_types ?? [], 'show_on_home', ''))=="yes")
                                <span class="icon-text yes"><i class="fa fa-check-circle mr-2"></i></span>
                                @endif
                                @if((data_get($sub_types ?? [], 'show_on_home', ''))=="no")
                                <span class="icon-text no"><i class="fa fa-times-circle mr-2"></i></span>
                                @endif
                                {{ __('quickad.show_on_home') }}</li>
                            <li class="mb-2">
                                @if((data_get($sub_types ?? [], 'show_in_home_search', ''))=="yes")
                                <span class="icon-text yes"><i class="fa fa-check-circle mr-2"></i></span>
                                @endif
                                @if((data_get($sub_types ?? [], 'show_in_home_search', ''))=="no")
                                <span class="icon-text no"><i class="fa fa-times-circle mr-2"></i></span>
                                @endif
                                {{ __('quickad.show_in_home_search') }}</li>
                        </ul>

                        <div class="position-relative">
                            @if((data_get($sub_types ?? [], 'Selected', ''))=="0")
                            <button type="submit" class="btn btn-primary" name="upgrade" value="{{ data_get($sub_types ?? [], 'id', '') }}">{{ __('quickad.upgrade') }}</button>
                            @endif
                            @if((data_get($sub_types ?? [], 'Selected', ''))=="1")
                            <a href="javascript:void(0);" class="btn btn-dark-grey">
                                <i class="fa fa-paper-plane mr-2"></i> {{ __('quickad.current_plan') }}
                            </a>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach

        </div>
        </form>

    </div>
</section>
<!-- END Pricing Plans -->
@include('partials.footer')

<script type="text/javascript">
    $(document).ready(function(){

        $.each($('.quickad_lang_translator'), function() {
            $lang = $(this).data('quickad-lang');
            $(this).html($lang);
            console.log($lang);
        });

    });
</script>
