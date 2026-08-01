<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ __('quickad.invoice') }} - {{ $site_title ?? '' }}</title>
    <style>
        :root{--theme-color-1: {{ $theme_color ?? '' }};}
    </style>
    <link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/invoice.css">
</head>
<body>

<!-- Print Button -->
<div class="print-button-container">
    <a href="javascript:window.print()" class="print-button">{{ __('quickad.print_invoice') }}</a>
</div>

<!-- Invoice -->
<div id="invoice">
    <!-- Header -->
    <div class="row">
        <div class="col-xl-6">
            <div id="logo"><img src="{{ $site_url ?? '' }}storage/logo/{{ $site_logo ?? '' }}" alt="{{ $site_title ?? '' }}"></div>
        </div>
        <div class="col-xl-6">
            <p id="details">
                <strong>{{ __('quickad.invoice') }}:</strong> {{ $invoice_nr_prefix ?? '' }}{{ $invoice_id ?? '' }} <br>
                <strong>{{ __('quickad.date') }}:</strong> {{ $invoice_date ?? '' }}
            </p>
        </div>
    </div>


    <!-- Client & Supplier -->
    <div class="row">
        <div class="col-xl-12">
            <h2>{{ __('quickad.invoice') }}</h2>
        </div>
        <div class="col-md-6">
            <h3 class="margin-bottom-5">{{ __('quickad.supplier') }}</h3>
            <p>
                @if(($invoice_admin_name ?? "")!="") <strong>{{ __('quickad.name') }}:</strong> {{ $invoice_admin_name ?? '' }}<br>@endif
                @if(($invoice_admin_address ?? "")!="") <strong>{{ __('quickad.address') }}:</strong> {{ $invoice_admin_address ?? '' }}<br>@endif
                @if(($invoice_admin_city ?? "")!="") <strong>{{ __('quickad.city') }}:</strong> {{ $invoice_admin_city ?? '' }}<br>@endif
                @if(($invoice_admin_state ?? "")!="") <strong>{{ __('quickad.state') }}:</strong> {{ $invoice_admin_state ?? '' }}<br>@endif
                @if(($admin_country ?? "")!="") <strong>{{ __('quickad.country') }}:</strong> {{ $admin_country ?? '' }}<br>@endif
                @if(($invoice_admin_zipcode ?? "")!="") <strong>{{ __('quickad.zipcode') }}:</strong> {{ $invoice_admin_zipcode ?? '' }}<br>@endif
                @if(($invoice_admin_tax_type ?? "")!="" && ($invoice_admin_tax_id ?? "")!="")
                <strong>{{ $invoice_admin_tax_type ?? '' }}:</strong> {{ $invoice_admin_tax_id ?? '' }}<br>
                @endif
                @if(($invoice_admin_custom_name_1 ?? "")!="" && ($invoice_admin_custom_value_1 ?? "")!="")
                <strong>{{ $invoice_admin_custom_name_1 ?? '' }}:</strong> {{ $invoice_admin_custom_value_1 ?? '' }}<br>
                @endif
                @if(($invoice_admin_custom_name_2 ?? "")!="" && ($invoice_admin_custom_value_2 ?? "")!="")
                <strong>{{ $invoice_admin_custom_name_2 ?? '' }}:</strong> {{ $invoice_admin_custom_value_2 ?? '' }}<br>
                @endif
            </p>
        </div>
        <div class="col-md-6">
            <h3 class="margin-bottom-5">{{ __('quickad.customer') }}</h3>
            <p>
                @if(($billing_name ?? "")!="") <strong>{{ __('quickad.name') }}:</strong> {{ $billing_name ?? '' }}<br>@endif
                @if(($billing_address ?? "")!="") <strong>{{ __('quickad.address') }}:</strong> {{ $billing_address ?? '' }}<br>@endif
                @if(($billing_city ?? "")!="") <strong>{{ __('quickad.city') }}:</strong> {{ $billing_city ?? '' }}<br>@endif
                @if(($billing_state ?? "")!="") <strong>{{ __('quickad.state') }}:</strong> {{ $billing_state ?? '' }}<br>@endif
                @if(($billing_country ?? "")!="") <strong>{{ __('quickad.country') }}:</strong> {{ $billing_country ?? '' }}<br>@endif
                @if(($billing_zipcode ?? "")!="") <strong>{{ __('quickad.zipcode') }}:</strong> {{ $billing_zipcode ?? '' }}<br>@endif
                @if(($billing_details_type ?? "")!="business")
                <strong>@if(($invoice_admin_tax_type ?? "")!="") {{ $invoice_admin_tax_type ?? '' }} {{ $else ?? '' }} {{ __('quickad.tax_id') }}@endif:</strong> {{ $billing_tax_id ?? '' }}<br>
                @endif
            </p>
        </div>
    </div>
    <!-- Invoice -->
    <div class="row">
        <div class="col-xl-12">
            <table class="margin-top-20">
                <tr>
                    <th>{{ __('quickad.item') }}</th>
                    <th>{{ __('quickad.amount') }}</th>
                </tr>
                <tr>
                    <td>{{ $item_name ?? '' }}</td>
                    <td>{{ $item_amount ?? '' }}</td>
                </tr>
                @foreach($taxes ?? [] as $taxes)
                <tr>
                    <td>{{ data_get($taxes ?? [], 'name', '') }}<br><small>{{ data_get($taxes ?? [], 'description', '') }}</small></td>
                    <td>{{ data_get($taxes ?? [], 'value_formatted', '') }}</td>
                </tr>
                @endforeach
            </table>
            <table id="totals">
                <tr>
                    <th>{{ __('quickad.total') }}<br><small>Paid via {{ $paid_via ?? '' }}</small></th>
                    <th><span>{{ $total_amount ?? '' }}</span></th>
                </tr>
            </table>
        </div>
    </div>
    <!-- Footer -->
    <div class="row">
        <div class="col-xl-12">
            <ul id="footer">
                <li><span>{{ $site_url ?? '' }}</span></li>
                <li>{{ $invoice_admin_email ?? '' }}</li>
                <li>{{ $invoice_admin_phone ?? '' }}</li>
            </ul>
        </div>
    </div>
</div>
</html>