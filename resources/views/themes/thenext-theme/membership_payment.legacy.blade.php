@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.upgrade') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.upgrade') }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-8 col-lg-8 content-right-offset">
            <form id="subscribeForm" method="POST" novalidate="novalidate">
                <h3>{{ __('quickad.payment_method') }}</h3>
                <div class="payment margin-top-30">
                    @if(($plan_id ?? "")=="trial")
                        <div class="payment-tab payment-tab-active">
                            <div class="payment-tab-trigger">
                                <input checked id="trial" name="payment_method_id" type="radio" value="trial"
                                       data-name="trial">
                                <label for="trial">{{ __('quickad.start_trial') }}</label>
                            </div>
                        </div>
                    {{ $else ?? '' }}
                        @foreach($payment_types ?? [] as $payment_types)
                            @if((data_get($payment_types ?? [], 'folder', ''))=="paypal")
                                <div class="payment-tab payment-tab-active">
                                    <div class="payment-tab-trigger">
                                        <input checked id="{{ data_get($payment_types ?? [], 'folder', '') }}" class="payment_method_id" name="payment_method_id" type="radio"
                                               value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                        <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                        <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                             src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png"
                                             alt="{{ data_get($payment_types ?? [], 'title', '') }}">
                                    </div>
                                    <div class="payment-tab-content">
                                        <p>{{ __('quickad.redirect_payment_page') }}</p>
                                        @if(($paypal_payment_mode ?? "")=="both")
                                        <h4 class="margin-bottom-10">{{ __('quickad.payment_type') }}</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input id="one-time" name="payment_mode" type="radio" value="one_time" checked="">
                                                    <label for="one-time"><span class="radio-label"></span> {{ __('quickad.one_time_payment') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input id="recurring" name="payment_mode" type="radio" value="recurring">
                                                    <label for="recurring"><span class="radio-label"></span> {{ __('quickad.recurring_payment') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if((data_get($payment_types ?? [], 'folder', ''))=="stripe")
                                <div class="payment-tab">
                                    <div class="payment-tab-trigger">
                                        <input name="payment_method_id" class="payment_method_id" id="creditCart" type="radio"
                                               value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                        <label for="creditCart">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                        <img class="payment-logo"
                                             src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png" alt="">
                                    </div>

                                    <div class="payment-tab-content">
                                        <p>{{ __('quickad.redirect_payment_page') }}</p>
                                        @if(($stripe_payment_mode ?? "")=="both")
                                        <h4 class="margin-bottom-10">{{ __('quickad.payment_type') }}</h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input id="one-time-stripe" name="payment_mode" type="radio" value="one_time" checked="">
                                                    <label for="one-time-stripe"><span class="radio-label"></span> {{ __('quickad.one_time_payment') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="radio">
                                                    <input id="recurring-stripe" name="payment_mode" type="radio" value="recurring">
                                                    <label for="recurring-stripe"><span class="radio-label"></span> {{ __('quickad.recurring_payment') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if((data_get($payment_types ?? [], 'folder', ''))=="paytm" && ($country_code ?? "")=="IN")
                                <!-- paytm-->
                                <div class="payment-tab">
                                    <div class="payment-tab-trigger">
                                        <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                               type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                        <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                        <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                             src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png" alt="">
                                    </div>

                                    <div class="payment-tab-content">
                                        <p>{{ __('quickad.redirect_payment_page') }}</p>
                                    </div>
                                </div>
                                <!-- paytm -->
                            @endif

                            @if((data_get($payment_types ?? [], 'folder', ''))=="ccavenue" && ($country_code ?? "")=="IN")
                                <!-- ccavenue-->
                                <div class="payment-tab">
                                    <div class="payment-tab-trigger">
                                        <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                               type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                        <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                        <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                             src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png" alt="">
                                    </div>

                                    <div class="payment-tab-content">
                                        <p>{{ __('quickad.redirect_payment_page') }}</p>
                                    </div>
                                </div>
                                <!-- ccavenue -->
                            @endif
                            @if((data_get($payment_types ?? [], 'folder', ''))=="payumoney")
                                <!-- Payumoney -->
                                <div class="payment-tab">
                                    <div class="payment-tab-trigger">
                                        <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                               type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                        <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                        <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                             src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png" alt="">
                                    </div>

                                    <div class="payment-tab-content">
                                        <p>{{ __('quickad.redirect_payment_page') }}</p>
                                    </div>
                                </div>
                                <!-- Payumoney -->
                            @endif
                            @if((data_get($payment_types ?? [], 'folder', ''))=="paystack")
                                <div class="payment-tab">
                                    <div class="payment-tab-trigger">
                                        <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                               type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                        <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                        <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                             src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png" alt="">
                                    </div>
                                    <div class="payment-tab-content">
                                        <p>{{ __('quickad.redirect_payment_page') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if((data_get($payment_types ?? [], 'folder', ''))=="2checkout")
                                <div class="payment-tab">
                                    <div class="payment-tab-trigger">
                                        <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                               type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                        <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                        <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                             src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png" alt="">
                                    </div>
                                    <div class="payment-tab-content">
                                        <!-- CREDIT CARD FORM STARTS HERE -->
                                        <div class="row payment-form-row">
                                            <div class="col-12">
                                                <div class="card-label form-group">
                                                    <input type="text" class="form-control" name="checkoutCardNumber"
                                                           placeholder="{{ __('quickad.card_number') }}" autocomplete="cc-number" autofocus/>
                                                </div>
                                            </div>
                                            <div class="col-7">
                                                <div class="card-label form-group">
                                                    <input type="tel" class="form-control" name="checkoutCardExpiry"
                                                           placeholder="MM / YYYY" autocomplete="cc-exp" aria-required="true"
                                                           aria-invalid="false">
                                                </div>
                                            </div>
                                            <div class="col-5 pull-right">
                                                <div class="card-label form-group">
                                                    <input type="tel" class="form-control" name="checkoutCardCVC"
                                                           placeholder="CVV" autocomplete="cc-csc"/>
                                                </div>
                                            </div>
                                            <div class="col-7">
                                                <div class="card-label form-group">
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            name="checkoutCardFirstName"
                                                            placeholder="{{ __('quickad.first_name') }}"

                                                    />
                                                </div>
                                            </div>
                                            <div class="col-5 pull-right">
                                                <div class="card-label form-group">
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            name="checkoutCardLastName"
                                                            placeholder="{{ __('quickad.last_name') }}"

                                                    />
                                                </div>
                                            </div>
                                            <div class="col-7">
                                                <div class="card-label form-group">
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            name="checkoutBillingAddress"
                                                            placeholder="{{ __('quickad.address') }}"

                                                    />
                                                </div>
                                            </div>
                                            <div class="col-5 pull-right">
                                                <div class="card-label form-group">
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            name="checkoutBillingCity"
                                                            placeholder="{{ __('quickad.city') }}"

                                                    />
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="card-label form-group">
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            name="checkoutBillingState"
                                                            placeholder="{{ __('quickad.state') }}"

                                                    />
                                                </div>
                                            </div>
                                            <div class="col-4 pull-right">
                                                <div class="card-label form-group">
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            name="checkoutBillingZipcode"
                                                            placeholder="{{ __('quickad.zipcode') }}"

                                                    />
                                                </div>
                                            </div>
                                            <div class="col-4 pull-right">
                                                <div class="card-label form-group">
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            name="checkoutBillingCountry"
                                                            placeholder="{{ __('quickad.country') }}"

                                                    />
                                                </div>
                                            </div>

                                            <div id="checkoutPaymentErrors" class="text-danger" style="display:none;">
                                                <div class="col-12">
                                                    <p class="payment-errors"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- CREDIT CARD FORM ENDS HERE -->

                                    </div>

                                </div>
                            @endif
                            @if((data_get($payment_types ?? [], 'folder', ''))=="wire_transfer")
                                <div class="payment-tab">
                                    <div class="payment-tab-trigger">
                                        <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                               type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                        <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ __('quickad.bank_depost_off_pay') }}</label>
                                        <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                             src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png" alt="">
                                    </div>

                                    <div class="payment-tab-content">
                                        <div class="quickad-template">
                                            <table class="default-table table-alt-row PaymentMethod-infoTable">
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <h4 class="PaymentMethod-heading">
                                                            <strong>{{ __('quickad.bank_account_details') }}</strong></h4>
                                                        <span class="PaymentMethod-info">{{ $bank_info ?? '' }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h4 class="PaymentMethod-heading"><strong>{{ __('quickad.reference') }}</strong></h4>
                                                        <span class="PaymentMethod-info">
                                                            {{ __('quickad.membershipplan') }} : {{ $order_title ?? '' }}<br>
                                                            {{ __('quickad.username') }}: {{ $username ?? '' }}<br>
                                                            <em><small>{{ __('quickad.offline_credit_note') }}</small></em>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h4 class="PaymentMethod-heading"><strong>{{ __('quickad.amount_to_send') }}</strong>
                                                        </h4>
                                                        <span class="PaymentMethod-info">{{ $amount ?? '' }}</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </div>

                                    </div>
                                </div>
                            @endif
                            @if((data_get($payment_types ?? [], 'folder', ''))=="mollie")
                                <div class="payment-tab">
                                    <div class="payment-tab-trigger">
                                        <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                               type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                        <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                        <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                             src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png"
                                             alt="mollie">
                                    </div>
                                    <div class="payment-tab-content">
                                        <p>{{ __('quickad.redirect_payment_page') }}</p>
                                    </div>
                                </div>
                            @endif
                            @if((data_get($payment_types ?? [], 'folder', ''))=="iyzico")
                            <div class="payment-tab">
                                <div class="payment-tab-trigger">
                                    <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                           type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                    <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                    <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                         src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png"
                                         alt="iyzico">
                                </div>
                                <div class="payment-tab-content">
                                    <p>{{ __('quickad.redirect_payment_page') }}</p>
                                </div>
                            </div>
                        @endif

                            @if((data_get($payment_types ?? [], 'folder', ''))=="hyperpay")
                            <div class="payment-tab">
                                <div class="payment-tab-trigger">
                                    <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                           type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                    <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                    <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                         src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png"
                                         alt="hyperpay">
                                </div>
                                <div class="payment-tab-content">
                                    <p>{{ __('quickad.redirect_payment_page') }}</p>
                                </div>
                            </div>
                        @endif

                            @if((data_get($payment_types ?? [], 'folder', ''))=="paytabs")
                            <div class="payment-tab">
                                <div class="payment-tab-trigger">
                                    <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                           type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                    <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                    <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                         src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png"
                                         alt="paytabs">
                                </div>
                                <div class="payment-tab-content">
                                    <p>{{ __('quickad.redirect_payment_page') }}</p>
                                </div>
                            </div>
                        @endif

                            @if((data_get($payment_types ?? [], 'folder', ''))=="midtrans")
                            <div class="payment-tab">
                                <div class="payment-tab-trigger">
                                    <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                           type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                    <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                    <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                         src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png"
                                         alt="midtrans">
                                </div>
                                <div class="payment-tab-content">
                                    <p>{{ __('quickad.redirect_payment_page') }}</p>
                                </div>
                            </div>
                        @endif

                            @if((data_get($payment_types ?? [], 'folder', ''))=="telr")
                            <div class="payment-tab">
                                <div class="payment-tab-trigger">
                                    <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                           type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                    <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                    <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                         src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png"
                                         alt="telr">
                                </div>
                                <div class="payment-tab-content">
                                    <p>{{ __('quickad.redirect_payment_page') }}</p>
                                </div>
                            </div>
                        @endif
                            @if((data_get($payment_types ?? [], 'folder', ''))=="razorpay")
                            <div class="payment-tab">
                                <div class="payment-tab-trigger">
                                    <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                           type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                    <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                    <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                         src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png"
                                         alt="razorpay">
                                </div>
                                <div class="payment-tab-content">
                                    <p>{{ __('quickad.redirect_payment_page') }}</p>
                                </div>
                            </div>
                        @endif
                            @if((data_get($payment_types ?? [], 'folder', ''))=="flutterwave")
                            <div class="payment-tab">
                                <div class="payment-tab-trigger">
                                    <input name="payment_method_id" class="payment_method_id" id="{{ data_get($payment_types ?? [], 'folder', '') }}"
                                           type="radio" value="{{ data_get($payment_types ?? [], 'id', '') }}" data-name="{{ data_get($payment_types ?? [], 'folder', '') }}">
                                    <label for="{{ data_get($payment_types ?? [], 'folder', '') }}">{{ data_get($payment_types ?? [], 'title', '') }}</label>
                                    <img class="payment-logo {{ data_get($payment_types ?? [], 'folder', '') }}"
                                         src="{{ $site_url ?? '' }}includes/payments/{{ data_get($payment_types ?? [], 'folder', '') }}/logo/logo.png"
                                         alt="flutterwave">
                                </div>
                                <div class="payment-tab-content">
                                    <p>{{ __('quickad.redirect_payment_page') }}</p>
                                </div>
                            </div>
                        @endif
                        @endforeach
                    @endif
                </div>
                <input type="hidden" name="token" value="{{ $token ?? '' }}"/>
                <input type="hidden" name="upgrade" value="{{ $upgrade ?? '' }}"/>
                <input type="hidden" name="billed-type" value="{{ $billed_type ?? '' }}"/>
                <button type="submit" name="Submit"
                        class="button big ripple-effect margin-top-40 margin-bottom-65 subscribeNow"
                        id="subscribeNow">{{ __('quickad.submit') }}</button>
            </form>
        </div>
        <div class="col-xl-4 col-lg-4 margin-top-0 margin-bottom-60">
            <div class="boxed-widget summary margin-top-0">
                <div class="boxed-widget-headline">
                    <h3>{{ __('quickad.package_summary') }}</h3>
                </div>
                <div class="boxed-widget-inner">
                    <ul>
                        <li>{{ __('quickad.membership') }} <span>{{ $order_title ?? '' }}</span></li>
                        <li>{{ __('quickad.start_date') }} <span>{{ $start_date ?? '' }}</span></li>
                        <li>{{ __('quickad.expiry_date') }} <span>{{ $expiry_date ?? '' }}</span></li>
                        @if(($show_taxes ?? ""))
                        <li class="total-costs"></li>
                        <li>{{ __('quickad.plan_fee') }} <span>{{ $price_without_inclusive ?? '' }}</span></li>
                        @foreach($taxes ?? [] as $taxes)
                            <li>
                                {{ data_get($taxes ?? [], 'name', '') }} <i class="fa fa-question-circle" title="{{ data_get($taxes ?? [], 'description', '') }}" data-tippy-placement="top"></i>
                                <span>+ {{ data_get($taxes ?? [], 'value_formatted', '') }}</span>
                                <small class="d-block">{{ data_get($taxes ?? [], 'type', '') }}</small>
                            </li>
                        @endforeach
                        @endif
                        <li class="total-costs">{{ __('quickad.total_cost') }} <span>{{ $amount ?? '' }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript" src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/jquery.payment.min.js"></script>

<!-- payment js -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="https://www.2checkout.com/checkout/api/2co.min.js"></script>
<script src="https://js.stripe.com/v2/"></script>

<script>
    var packagePrice = 1;
    var LANG_CONFIRM_PAY = "{{ __('quickad.confirm_pay') }}";
    var LANG_PROCCESSING = "{{ __('quickad.proccessing') }}";
    var LANG_VALIDATING = "{{ __('quickad.validating') }}";
    var LANG_TRY_AGAIN = "{{ __('quickad.error_try_again') }}";
    var LANG_INV_EXP_DATE = "{{ __('quickad.inv_exp_date') }}";
    var LANG_INV_CVV = "{{ __('quickad.inv_cvv') }}";
    var LANG_FIELD_REQ = "{{ __('quickad.field_req') }}";
    var LANG_CODE = "{{ __('quickad.code') }}";

    $(document).ready(function () {
        /* Show price & Payment Methods */
        var paymentMethod = $('input[name="payment_method_id"]:checked').data("name");

        /* Select a Payment Method */
        $('.payment_method_id').on('change', function () {
            paymentMethod = $(this).data('name');
            var $payment_tab_content = $(this).closest('.payment-tab').find('.payment-tab-content');
            $payment_tab_content.find('[name="payment_mode"]').first().prop('checked',true);
        });

        $('.payment_method_id').first().prop('checked',true).trigger('change');

        /* Fancy restrictive input formatting via jQuery.payment library */
        $('input[name=checkoutCardNumber]').payment('formatCardNumber');
        $('input[name=checkoutCardCVC]').payment('formatCardCVC');
        $('input[name=checkoutCardExpiry]').payment('formatCardExpiry');

        $('input[name=stripeCardNumber]').payment('formatCardNumber');
        $('input[name=stripeCardCVC]').payment('formatCardCVC');
        $('input[name=stripeCardExpiry]').payment('formatCardExpiry');

        /* Pull in the public encryption key for our environment (2Checkout) */
        TCO.loadPubKey('{2CHECKOUT_SANDBOX_MODE}');

        /* Form Default Submission */
        $('#subscribeNow').on('click', function (e) {
            e.preventDefault();

            paymentMethod = $('input[name="payment_method_id"]:checked').data("name");
            var $form = $('#subscribeForm');

            if (packagePrice <= 0) {
                $form.submit();
            }

            switch (paymentMethod) {
                case 'wire_transfer':
                case 'paypal':
                case 'stripe':
                case 'ccavenue':
                case 'paytm':
                case 'payumoney':
                case 'mollie':
                case 'iyzico':
                case 'hyperpay':
                case 'paytabs':
                case 'midtrans':
                case 'telr':
                case 'razorpay':
                case 'flutterwave':
                case 'trial':
                    $form.submit();
                    break;
                case 'paystack':
                    payWithPaystack();
                    break;
                case '2checkout':
                    if (ccFormValidationForCheckout()) {
                        payWithCheckout();
                    }
                    break;
            }

            return false;
        });

        function payWithPaystack() {
            var amount = '{{ $price ?? '' }}';
            amount = 100 * amount;
            var $form = $('#subscribeForm');
            $form.find('#subscribeNow').html(LANG_PROCCESSING + ' <i class="fa fa-spinner fa-pulse"></i>');

            var handler = PaystackPop.setup({
                    key: '{{ $paystack_public_key ?? '' }}',
                    email: '{{ $email ?? '' }}',
                    amount: amount,
                    currency: '{{ $currency_code ?? '' }}',
                    metadata: {
                        custom_fields: [
                            {
                                display_name: "Blank",
                                product_id: "Blank",
                                value: "Blank"
                            }
                        ]
                    }
                    ,
                    callback: function (response) {
                        var paystackReference = response.reference;
                        /* Insert the token into the form so it gets submitted to the server */
                        $form.append($('<input type="hidden" name="paystackReference" />').val(paystackReference));
                        $form.submit();
                    }
                    ,
                    onClose: function () {
                        $form.find('#subscribeNow').html(LANG_CONFIRM_PAY);
                    }
                }
                )
            ;
            handler.openIframe();
        }

        function ccFormValidationForCheckout() {
            var $form = $('#subscribeForm');

            /* Form validation */
            /*jQuery.validator.addMethod('checkoutCardExpiry', function(value, element) {
             *//* Regular expression to match Credit Card expiration date *//*
             var reg = new RegExp('^(0[1-9]|1[0-2])\\s?\/\\s?([0-9]|[0-9])$');
             return this.optional(element) || reg.test(value);
             }, "Invalid expiration date");*/

            jQuery.validator.addMethod(
                "checkoutCardExpiry",
                function (value, element, params) {
                    var minMonth = new Date().getMonth() + 1;
                    var minYear = new Date().getFullYear();

                    var checkoutCardExpiry = $('input[name=checkoutCardExpiry]').val().split('/');
                    var $month = (0 in checkoutCardExpiry) ? checkoutCardExpiry[0].replace(/\s/g, '') : '';
                    var $year = (1 in checkoutCardExpiry) ? checkoutCardExpiry[1].replace(/\s/g, '') : '';

                    var month = parseInt($month, 10);
                    var year = parseInt($year, 10);

                    return ((year > minYear) || ((year === minYear) && (month >= minMonth)));
                }
                ,
                LANG_INV_EXP_DATE);

            jQuery.validator.addMethod('checkoutCardCVC', function (value, element) {
                /* Regular expression matching a 3 or 4 digit CVC (or CVV) of a Credit Card */
                var reg = new RegExp('^[0-9]{3,4}$');
                return this.optional(element) || reg.test(value);
            }, LANG_INV_CVV);

            var validator = $form.validate({
                lang: '{{ __('quickad.code') }}',
                rules: {
                    checkoutCardNumber: {
                        required: true
                    },
                    checkoutCardExpiry: {
                        required: true,
                        checkoutCardExpiry: true
                    },
                    checkoutCardCVC: {
                        required: true,
                        checkoutCardCVC: true
                    },
                    checkoutCardHolderFirstName: {
                        required: true
                    },
                    checkoutCardHolderLastName: {
                        required: true
                    },
                    checkoutBillingAddress: {
                        required: true
                    },
                    checkoutBillingCity: {
                        required: true
                    },
                    checkoutBillingState: {
                        required: true
                    },
                    checkoutBillingZipcode: {
                        required: true
                    },
                    checkoutBillingCountry: {
                        required: true
                    }
                },
                highlight: function (element) {
                    $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
                },
                unhighlight: function (element) {
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
                },
                errorPlacement: function (error, element) {
                    $(element).closest('.form-group').append(error);
                }
            });

            /* Abort if invalid form data */
            return validator.form();
        }

        function payWithCheckout() {
            var $form = $('#subscribeForm');

            /* Visual feedback */
            $form.find('#subscribeNow').html(LANG_VALIDATING + ' <i class="fa fa-spinner fa-pulse"></i>').prop('disabled', true);

            /* Setup token request arguments */
            var checkoutCardExpiry = $('input[name=checkoutCardExpiry]').val().split('/');

            var args = {
                sellerId: "{{ $checkout_account_number ?? '' }}",
                publishableKey: "{{ $checkout_public_key ?? '' }}",
                ccNo: $('input[name=checkoutCardNumber]').val().replace(/\s/g, ''),
                cvv: $('input[name=checkoutCardCVC]').val(),
                expMonth: (0 in checkoutCardExpiry) ? checkoutCardExpiry[0].replace(/\s/g, '') : '',
                expYear: (1 in checkoutCardExpiry) ? checkoutCardExpiry[1].replace(/\s/g, '') : ''
            };

            /* Make the token request */
            TCO.requestToken(function (data) {
                /* Visual feedback */
                $form.find('#subscribeNow').html(LANG_PROCCESSING + ' <i class="fa fa-spinner fa-pulse"></i>');

                /* Hide Stripe errors on the form */
                $form.find('#checkoutPaymentErrors').hide();
                $form.find('#checkoutPaymentErrors').find('.payment-errors').text('');

                /* Set the token as the value for the token input */
                var checkoutToken = data.response.token.token;
                $form.append($('<input type="hidden" name="2checkoutToken" />').val(checkoutToken));

                /* IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop. */
                $form.submit();

            }, function (data) {
                if (data.errorCode === 200) {
                    tokenRequest();
                } else {
                    /* Visual feedback */
                    $form.find('#subscribeNow').html(LANG_TRY_AGAIN).prop('disabled', false);

                    /* Show errors on the form */
                    $form.find('#checkoutPaymentErrors').find('.payment-errors').text(data.errorMsg);
                    $form.find('#checkoutPaymentErrors').show();
                }
            }, args);
        }

        function payWithStripe() {
            var $form = $('#subscribeForm');

            /* Visual feedback */
            $form.find('#subscribeNow').html(LANG_VALIDATING + ' <i class="fa fa-spinner fa-pulse"></i>').prop('disabled', true);

            var PublishableKey = '{{ $stripe_publishable_key ?? '' }}';
            Stripe.setPublishableKey(PublishableKey);

            /* Create token */
            var expiry = $form.find('[name=stripeCardExpiry]').payment('cardExpiryVal');
            var ccData = {
                number: $form.find('[name=stripeCardNumber]').val().replace(/\s/g, ''),
                cvc: $form.find('[name=stripeCardCVC]').val(),
                exp_month: expiry.month,
                exp_year: expiry.year
            };

            Stripe.card.createToken(ccData, function stripeResponseHandler(status, response) {
                if (response.error) {
                    /* Visual feedback */
                    $form.find('#subscribeNow').html(LANG_TRY_AGAIN).prop('disabled', false);

                    /* Show errors on the form */
                    $form.find('#stripePaymentErrors').find('.payment-errors').text(response.error.message);
                    $form.find('#stripePaymentErrors').show();
                } else {
                    /* Visual feedback */
                    $form.find('#subscribeNow').html(LANG_PROCCESSING + ' <i class="fa fa-spinner fa-pulse"></i>');

                    /* Hide Stripe errors on the form */
                    $form.find('#stripePaymentErrors').hide();
                    $form.find('#stripePaymentErrors').find('.payment-errors').text('');

                    /* Response contains id and card, which contains additional card details */
                    var stripeToken = response.id;
                    /* Insert the token into the form so it gets submitted to the server */
                    $form.append($('<input type="hidden" name="stripeToken" />').val(stripeToken));
                    $form.append($('<input type="hidden" name="exp_month" />').val(response.card.exp_month));
                    $form.append($('<input type="hidden" name="exp_year" />').val(response.card.exp_year));

                    /* and submit */
                    $form.submit();
                }
            });
        }
    });

</script>

@include('partials.footer')