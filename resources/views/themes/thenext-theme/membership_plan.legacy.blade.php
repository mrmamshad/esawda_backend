@include('partials.header')
<!-- Titlebar
================================================== -->
<div id="titlebar" class="gradient">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.membershipplan') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.membershipplan') }}</li>
                    </ul>
                </nav>
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
                    <div class="radio billed-monthly-radio">
                        <input id="radio-monthly" name="billed-type" type="radio" value="monthly" checked="">
                        <label for="radio-monthly"><span class="radio-label"></span> {{ __('quickad.monthly') }}</label>
                    </div>
                    @endif
                    @if(($total_annual ?? "")!="0")
                    <div class="radio billed-yearly-radio">
                        <input id="radio-yearly" name="billed-type" type="radio" value="yearly">
                        <label for="radio-yearly"><span class="radio-label"></span> {{ __('quickad.yearly') }}</label>
                    </div>
                    @endif
                    @if(($total_lifetime ?? "")!="0")
                    <div class="radio billed-lifetime-radio">
                        <input id="radio-lifetime" name="billed-type" type="radio" value="lifetime">
                        <label for="radio-lifetime"><span class="radio-label"></span> {{ __('quickad.lifetime') }}</label>
                    </div>
                    @endif
                </div>
                <!-- Pricing Plans Container -->
                <div class="pricing-plans-container">
                    <div class="row">
                        @foreach($sub_types ?? [] as $sub_types)
                        <div class="col-3">
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
                            <button type="submit" class="button full-width margin-top-20 ripple-effect" name="upgrade" value="{{ data_get($sub_types ?? [], 'id', '') }}">{{ __('quickad.upgrade') }}</button>
                        @endif
                        @if((data_get($sub_types ?? [], 'Selected', ''))=="1")
                            <a href="javascript:void(0);" class="button full-width margin-top-20 ripple-effect">
                                {{ __('quickad.current_plan') }}
                            </a>
                        @endif
                    </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="margin-top-80"></div>
@include('partials.footer')
