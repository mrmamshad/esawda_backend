@include('partials.header')
<!-- main -->
<section id="main" class="clearfix page">
    <div class="container">

        <div class="breadcrumb-section">
            <ul class="breadcrumb bcstyle2">
                <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                <li class="active"><a>{{ __('quickad.feedback') }}</a></li>
            </ul>
            <a href="{{ $link['POST-AD'] ?? '#' }}" class="postadinner"><span> <i class="fa fa-plus-circle"></i> {{ __('quickad.post_ad') }}</span></a>
            <!--end breadcrumb-->
            <section class="page-title center"><h1>{{ __('quickad.feedback') }}</h1></section>
        </div>
        <div class="section">
            <div class="feed-back">
                <h3>{{ __('quickad.what_you_think') }}</h3>

                <div class="feed-back-form">
                    <form method="post">
                        <span>{{ __('quickad.user_details') }}</span>
                        <input type="text" class="form-control" name="name" placeholder="{{ __('quickad.full_name') }}" required="">
                        <input type="text" class="form-control" name="email" placeholder="{{ __('quickad.email') }}" required="">
                        <input type="text" class="form-control" name="phone" placeholder="{{ __('quickad.phone_no') }}">
                        <input type="text" class="form-control" name="subject" placeholder="{{ __('quickad.subject') }}" required="">
                        <!---728x90--->
                        <span>{{ __('quickad.anything_to_tell') }}?</span>
                        <textarea type="text" class="form-control" name="message" placeholder="{{ __('quickad.message') }}..." required=""></textarea>
                        <input type="submit" name="Submit" class="btn btn-primary" value="{{ __('quickad.submit') }}">
                    </form>
                </div>
            </div>
        </div>
    </div><!-- container -->
</section><!-- main -->


@include('partials.footer')