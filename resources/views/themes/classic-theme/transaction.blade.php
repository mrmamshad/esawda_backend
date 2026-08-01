@include('partials.header')
<link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/datatables/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/datatables/responsive.dataTables.min.css">
<section id="main" class="clearfix  ad-profile-page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.transaction') }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>{{ __('quickad.back_result') }}</a></div>
            </ol>
            <!-- breadcrumb -->
        </div>
        <!-- Main Content -->
        <div class="row">
            <!-- Page-Content -->
            <div class="col-sm-12 page-content">
                <table class="table table-striped sep ver-mspace" id="myTable">
                    <thead>
                    <tr class="no-mar no-pad">
                        <th></th>
                        <th class="dl sep-right">{{ __('quickad.ad_title') }}</th>
                        <th class="dl sep-right">{{ __('quickad.amount') }}</th>
                        <th class="dc sep-right ">{{ __('quickad.premium') }}</th>
                        <th class="dc sep-right">{{ __('quickad.payment_method') }}</th>
                        <th class="dc sep-right">{{ __('quickad.date') }}</th>
                        <th class="dc sep-right ">{{ __('quickad.status') }}</th>
                    </tr>
                    </thead>
                    @if(($t_blank ?? "")=="0")
                    <tbody>
                    <tr>
                        <td colspan="18" class="notice text-16 dc">{{ __('quickad.no_result_found') }}</td>
                    </tr>
                    </tbody>
                    @endif
                    <tbody>
                    @foreach($transactions ?? [] as $transactions)
                    <tr class="altrow">
                        <td class=" details-control"></td>
                        <td class="dc"><a href="{{ data_get($transactions ?? [], 'product_link', '') }}" target="_blank">{{ data_get($transactions ?? [], 'product_name', '') }}</a></td>
                        <td class="dl">
                            <div class="dl">
                                @if(($currency_pos ?? "")=="BEF") {{ $currency_sign ?? '' }} @endif
                                {{ data_get($transactions ?? [], 'amount', '') }}
                                @if(($currency_pos ?? "")=="AFT") {{ $currency_sign ?? '' }} @endif
                            </div>
                        </td>
                        <td class="dc">{{ data_get($transactions ?? [], 'premium', '') }}</td>
                        <td class="dc"><span>{{ data_get($transactions ?? [], 'payment_by', '') }}</span></td>
                        <td class="dc"><span>{{ data_get($transactions ?? [], 'time', '') }}</span></td>
                        <td class="dc">{{ data_get($transactions ?? [], 'status', '') }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Page-Content -->
        </div>
        <!-- Main Content -->
    </div>
    <!-- container -->
</section>
<!-- ad-dashboard-page -->

<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/assets/plugins/datatables/dataTables.responsive.min.js"></script>

<script>
    //var LANG_SEARCH = "{{ __('quickad.search') }}";

    $(document).ready(function () {
        $('#myTable').DataTable({
            responsive: {
                details: {
                    type: 'column'
                }
            },
            "language": {
                "paginate": {
                    "previous": "{{ __('quickad.previous') }}",
                    "next": "{{ __('quickad.next') }}"
                },
                "search": "{{ __('quickad.search') }}",
                "lengthMenu": "{{ __('quickad.display') }} _MENU_",
                "zeroRecords": "{{ __('quickad.no_found') }}",
                "info": "{{ __('quickad.page') }} _PAGE_ - _PAGES_",
                "infoEmpty": "{{ __('quickad.no_result_found') }}",
                "infoFiltered": "( {{ __('quickad.total_record') }} _MAX_)"
            },
            columnDefs: [{
                className: 'control',
                orderable: false,
                targets: 0
            }]
        });
    });

</script>
@include('partials.footer')