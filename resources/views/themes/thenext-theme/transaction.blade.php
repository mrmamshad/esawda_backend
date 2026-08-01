@include('partials.header')
<link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/css/responsive.dataTables.min.css">
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.transactions') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.transactions') }}</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <h3 class="page-title margin-bottom-30">{{ __('quickad.transactions') }}</h3>
    <table id="datatable">
        <thead>
        <tr>
            <th class="small-width"></th>
            <th>{{ __('quickad.title') }}</th>
            <th class="small-width">{{ __('quickad.amount') }}</th>
            <th class="small-width">{{ __('quickad.premium') }}</th>
            <th>{{ __('quickad.payment_method') }}</th>
            <th>{{ __('quickad.date') }}</th>
            <th class="small-width">{{ __('quickad.status') }}</th>
            <th class="small-width"></th>
        </tr>
        </thead>
        @if(($t_blank ?? "")=="0")
        <tbody>
        <tr>
            <td colspan="7" class="text-center">{{ __('quickad.no_result_found') }}</td>
        </tr>
        </tbody>
        {{ $else ?? '' }}
        <tbody>
        @foreach($transactions ?? [] as $transactions)
            <tr>
                <td></td>
                <td>{{ data_get($transactions ?? [], 'product_name', '') }}</td>
                <td>
                    {{ data_get($transactions ?? [], 'amount', '') }}
                </td>
                <td>{{ data_get($transactions ?? [], 'premium', '') }}</td>
                <td>{{ data_get($transactions ?? [], 'payment_by', '') }}</td>
                <td>{{ data_get($transactions ?? [], 'time', '') }}</td>
                <td>{{ data_get($transactions ?? [], 'status', '') }}</td>
                <td>
                    @if((data_get($transactions ?? [], 'invoice', ''))!="")
                    <a href="{{ data_get($transactions ?? [], 'invoice', '') }}" class="button ico" data-tippy-placement="top" title="{{ __('quickad.invoice') }}" target="_blank"><i class="icon-feather-file-text"></i></a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
        @endif
    </table>
    <div class="margin-bottom-50"></div>
</div>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/jquery.dataTables.min.js"></script>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/dataTables.responsive.min.js"></script>
<script>
    
    $(document).ready(function () {
        $('#datatable').DataTable({
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
                "zeroRecords": "{{ __('quickad.no_result_found') }}",
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
