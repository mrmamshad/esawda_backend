@include('partials.header')
<div id="page-content">
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
            <li class="active">{{ __('quickad.faq') }}</li>
        </ol>
        <section class="page-title">
            <h1>{{ __('quickad.faq') }}</h1>
        </section>


        <section>
            @foreach($faq ?? [] as $faq)
            <div class="answer">
                <div class="box">
                    <h3>{{ data_get($faq ?? [], 'title', '') }}</h3>
                    <p>{{ data_get($faq ?? [], 'content', '') }}</p>
                </div>
                <figure class="hidden">Was this answer helpful? <a href="#">Yes<i class="fa fa-thumbs-up"></i></a> <a href="#">No<i class="fa fa-thumbs-down"></i></a></figure>
            </div>
            <!--end answer-->
            @endforeach
        </section>
</div>
@include('partials.footer')