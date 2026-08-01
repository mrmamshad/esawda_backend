@include('partials.header')
<section id="main" class="clearfix page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}"><i class="fa fa-home"></i> {{ __('quickad.home') }}</a></li>
                <li>{{ __('quickad.faq') }}</li>
                <div class="pull-right back-result">
                    <a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>
                    {{ __('quickad.back_result') }}</a>
                </div>
                <h2 class="title">{{ __('quickad.faq') }}</h2>
            </ol>
            <!-- breadcrumb -->
        </div>
        <div class="faq-page section">
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
    <!-- container -->
</section>
@include('partials.footer')