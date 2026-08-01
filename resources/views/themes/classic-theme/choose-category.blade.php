@include('partials.header')
<!-- Choose-category-page -->
<section id="main" class="clearfix ad-post-page">
    <div class="container">
        <div class="breadcrumb-section">
            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                <li class="active">{{ __('quickad.choose_category') }}</li>
                <div class="pull-right back-result"><a href="{{ $link['LISTING'] ?? '#' }}"><i class="fa fa-angle-double-left"></i>
                    {{ __('quickad.back_result') }}</a></div>
            </ol>
            <!-- breadcrumb -->
        </div>
        <div id="quickad-post">
            <div class="row category-tab" id="js-category-list">
                <div class="col-md-4 col-sm-6">
                    <div class="section cat-option select-category post-option">
                        <h4>{{ __('quickad.select_category') }}</h4>
                        <ul role="tablist">
                            @foreach($category ?? [] as $category)
                            <li class="" aria-controls="cat{{ data_get($category ?? [], 'id', '') }}" role="tab" data-toggle="tab"
                                aria-expanded="false" data-ajax-catid="{{ data_get($category ?? [], 'id', '') }}"
                                data-ajax-action="getsubcatbyidList"><a href="#cat{{ data_get($category ?? [], 'id', '') }}"><span class="select"><i
                                    class="{{ data_get($category ?? [], 'icon', '') }} fa20"></i></span>{{ data_get($category ?? [], 'name', '') }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <!-- Tab panes -->
                <div class="col-md-4 col-sm-6">
                    <div class="section tab-content subcategory post-option">
                        <h4>{LANG_SELECT-SUBCATEGORY}</h4>
                        <ul id="sub_category"></ul>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="section next-stap post-option">
                        <h2>{{ __('quickad.postad_just') }} <span>30 {{ __('quickad.seconds') }}</span></h2>
                        <p>{{ __('quickad.category_note') }}</p>

                        <form method="post">
                            <div class="btn-section">
                                <input type="hidden" id="input-catid" name="catid" value="">
                                <input type="hidden" id="input-subcatid" name="subcatid" value="">
                                <button type="submit" name="choose-category" id="next-btn" class="btn btn-primary" disabled>{{ __('quickad.next') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- next-stap -->
            </div>
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2 text-center">
                    <div class="quickad-section">{{ $top_adscode ?? '' }}</div>
                </div>
            </div>
            <!-- row -->
        </div>
    </div>
    <!-- container -->
</section>
<!-- Choose-category-page -->
@include('partials.footer')

<script>
    // -------------------------------------------------------------
    //  select-category Change
    // -------------------------------------------------------------
    $('.select-category.post-option ul li').on('click', function () {
        $('.select-category.post-option ul li.link-active').removeClass('link-active');
        $(this).closest('li').addClass('link-active');

        var catid = $(this).data('ajax-catid');
        var action = $(this).data('ajax-action');
        var data = {action: action, catid: catid};

        getsubcat(catid, action, "");

        $('button').prop('disabled', true);
    });

    $('#js-category-list').on('click', '#sub_category li', function (e) {

        var $item = $(this).closest('li');
        $('#sub_category li.link-active').removeClass('link-active active');
        $item.addClass('link-active');
        var subcatid = $item.data('ajax-subcatid');
        $('#input-subcatid').val(subcatid);

        $('button').prop('disabled', false);
    });

    jQuery(function ($) {
        getsubcat("{{ $catid ?? '' }}", "getsubcatbyidList", "{{ $subcatid ?? '' }}")
    });

    function getsubcat(catid, action, selectid) {
        var data = {action: action, catid: catid, selectid: selectid};
        $.ajax({
            type: "POST",
            url: ajaxurl + '?action=' + action,
            data: data,
            success: function (result) {
                $("#sub_category").html(result);
            }
        });
        $('*[data-ajax-catid=' + catid + ']').addClass('link-active active');

        $('#input-catid').val(catid);
        $('#input-subcatid').val(selectid);

        $('button').prop('disabled', false);
    }
</script>