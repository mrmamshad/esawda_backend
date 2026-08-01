@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ __('quickad.faq') }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li>{{ __('quickad.faq') }}</li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>
    <div class="container">
        <div class="margin-bottom-50">
            @foreach($faq ?? [] as $faq)
            <dl class="faq-list">
                <dt class="faq-list_h">
                    <h4 class="marker">Q?</h4>
                    <h5 class="marker_head">{{ data_get($faq ?? [], 'title', '') }}</h5>
                </dt>
                <dd>
                    <h4 class="marker1">A.</h4>
                    <div class="m_13"> {{ data_get($faq ?? [], 'content', '') }}</div>
                </dd>
            </dl>
            @endforeach
        </div>
        <!-- faq-page -->
    </div>
@include('partials.footer')
