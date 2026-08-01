@include('partials.header')
<div id="titlebar">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $title ?? '' }}</h2>
                <!-- Breadcrumbs -->
                <nav id="breadcrumbs">
                    <ul>
                        <li><a href="{{ $link['INDEX'] ?? '#' }}">{{ __('quickad.home') }}</a></li>
                        <li><a href="{{ $link['BLOG'] ?? '#' }}">{{ __('quickad.blog') }}</a></li>
                        <li>{{ $title ?? '' }}</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<div class="container margin-bottom-50">
    <div class="row">
        <div class="col-md-8 col-12">
            <div class="listings-container blog-listing blog-single">
                <div class="job-listing">
                    <div class="job-listing-details">
                        @if(($blog_banner ?? ""))
                            @if(($image ?? ""))
                            <div class="job-listing-company-logo">
                                <img class="lazy-load" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ $site_url ?? '' }}storage/blog/{{ $image ?? '' }}" alt="{{ $title ?? '' }}">
                            </div>
                            @endif
                        @endif
                        <div class="job-listing-footer">
                            <ul>
                                <li><a href="{{ $author_link ?? '' }}">
                                        <img class="lazy-load author-avatar" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ $site_url ?? '' }}storage/profile/{{ $author_pic ?? '' }}">
                                        {{ __('quickad.by') }} {{ $author ?? '' }}</a></li>
                                <li><i class="la la-clock-o"></i> {{ $created_at ?? '' }}</li>
                                <li>
                                    <div class="blog-cat"><i class="fa fa-folder-o"></i> {{ $categories ?? '' }}</div>
                                </li>
                            </ul>
                        </div>
                        <div class="job-listing-description">
                            <h2 class="job-listing-title">{{ $title ?? '' }}</h2>

                            <div class="user-html">{{ $description ?? '' }}</div>
                            @if(($show_tag ?? ""))
                            <div class="job-tags margin-bottom-20">
                                {{ __('quickad.tags') }}: {{ $blog_tags ?? '' }}
                            </div>
                            @endif
                            <ul class="share-buttons-icons margin-bottom-10">
                                <li><a href="mailto:?subject={{ $title ?? '' }}&body={{ $blog_link ?? '' }}" data-button-color="#dd4b39"
                                       title="{{ __('quickad.share_email') }}" data-tippy-placement="top" rel="nofollow"
                                       target="_blank"><i class="fa fa-envelope"></i></a></li>
                                <li><a href="https://facebook.com/sharer/sharer.php?u={{ $blog_link ?? '' }}"
                                       data-button-color="#3b5998" title="{{ __('quickad.share_facebook') }}"
                                       data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-facebook"></i></a></li>
                                <li><a href="https://twitter.com/share?url={{ $blog_link ?? '' }}&text={{ $title ?? '' }}"
                                       data-button-color="#1da1f2" title="{{ __('quickad.share_twitter') }}"
                                       data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-twitter"></i></a></li>
                                <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $blog_link ?? '' }}"
                                       data-button-color="#0077b5" title="{{ __('quickad.share_linkedin') }}"
                                       data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-linkedin"></i></a></li>
                                <li>
                                    <a href="https://pinterest.com/pin/create/bookmarklet/?&url={{ $blog_link ?? '' }}&description={{ $title ?? '' }}"
                                       data-button-color="#bd081c" title="{{ __('quickad.share_pinterest') }}"
                                       data-tippy-placement="top" rel="nofollow" target="_blank"><i
                                                class="fa fa-pinterest-p"></i></a></li>
                                <li><a href="https://web.whatsapp.com/send?text={{ $blog_link ?? '' }}" data-button-color="#25d366"
                                       title="{{ __('quickad.share_whatsapp') }}" data-tippy-placement="top" rel="nofollow"
                                       target="_blank"><i class="fa fa-whatsapp"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @if(($blog_comment_enable ?? ""))
            @if(($comments_count ?? ""))
            <div class="blog-widget">
                <h3 class="widget-title margin-bottom-25">{{ __('quickad.comments') }} ({{ $comments_count ?? '' }})</h3>

                <div class="latest-comments">
                    <ul>
                        @foreach($comments ?? [] as $comments)
                            <li id="li-comment-{{ data_get($comments ?? [], 'id', '') }}" @if({{ data_get($comments ?? [], 'is_child', '') }})
                                class="children-{{ data_get($comments ?? [], 'level', '') }}" @endif>
                                <div class="comments-box" id="comment-{{ data_get($comments ?? [], 'id', '') }}">
                                    <div class="comments-avatar">
                                        <img src="{{ $site_url ?? '' }}storage/profile/{{ data_get($comments ?? [], 'avatar', '') }}" alt="{{ data_get($comments ?? [], 'name', '') }}">
                                    </div>
                                    <div class="comments-text">
                                        <div class="avatar-name">
                                            <h5>{{ data_get($comments ?? [], 'name', '') }}</h5>
                                            <span>{{ data_get($comments ?? [], 'created_at', '') }}</span>
                                            @if({{ data_get($comments ?? [], 'level', '') }} < 3)
                                            <a class="comments-reply comment-reply-link" href="javascript:void(0)"
                                               data-commentid="{{ data_get($comments ?? [], 'id', '') }}" data-postid="{{ $blog_id ?? '' }}"
                                               data-belowelement="comment-{{ data_get($comments ?? [], 'id', '') }}"
                                               data-respondelement="respond"><i class="fa fa-reply"></i>{{ __('quickad.reply') }}</a>
                                            @endif
                                        </div>
                                        <p>{{ data_get($comments ?? [], 'comment', '') }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if(($show_paging ?? ""))
            <div class="pagination-container margin-bottom-20">
                <nav class="pagination">
                    <ul>
                        @foreach($comment_paging ?? [] as $comment_paging)
                            @if((data_get($comment_paging ?? [], 'current', ''))=="0")
                            <li><a href="{{ data_get($comment_paging ?? [], 'link', '') }}">{{ data_get($comment_paging ?? [], 'title', '') }}</a></li>
                        {{ $else ?? '' }}
                            <li><a href="#" class="current-page">{{ data_get($comment_paging ?? [], 'title', '') }}</a></li>
                        @endif
                        @endforeach
                    </ul>
                </nav>
            </div>
            @endif

            @endif
            @if(($show_comment_form ?? ""))
            <div class="blog-widget" id="respond">
                <h3 class="widget-title">{{ __('quickad.post_comment') }}
                    <small><a rel="nofollow" id="cancel-comment-reply-link" href="javascript:void(0)"
                              style="display: none;">{{ __('quickad.cancel_reply') }}</a></small>
                </h3>

                <div class="">
                    @if(($comment_error ?? ""))
                    <div class="notification error">
                        <p>{{ $comment_error ?? '' }}</p>
                    </div>
                    @endif
                    @if(($comment_success ?? ""))
                    <div class="notification success">
                        <p>{{ $comment_success ?? '' }}</p>
                    </div>
                    @endif
                    <form action="#respond" method="post" id="commentform" class="blog-comment-form">
                        <div class="row">
                            @if(!(($admin_logged_in ?? "") || ($logged_in ?? "")))
                            <div class="col-md-6">
                                <div class="input-with-icon">
                                    <input class="with-border" type="text" placeholder="{{ __('quickad.yname') }} *" name="user_name"
                                           value="{{ $user_name ?? '' }}" required="">
                                    <i class="icon-feather-user"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-with-icon">
                                    <input class="with-border" type="email" placeholder="{{ __('quickad.yemail') }} *"
                                           name="user_email" value="{{ $user_email ?? '' }}" required>
                                    <i class="icon-feather-mail"></i>
                                </div>
                            </div>
                            @endif
                            @if(($admin_logged_in ?? "") && ($logged_in ?? ""))
                            <div class="col-md-12">
                                <div class="commenting-as">
                                    <label for="commenting-as">{{ __('quickad.commenting_as') }}</label>
                                    <select id="commenting-as" name="commenting-as"
                                            class="selectpicker with-border col-md-4">
                                        <option value="admin">{{ $admin_username ?? '' }} ({{ __('quickad.admin') }})</option>
                                        <option value="user">{{ $username ?? '' }}</option>
                                    </select>
                                </div>
                            </div>
                            ELSEIF({{ $admin_logged_in ?? '' }}){
                            <div class="col-md-12">
                                <p>{{ __('quickad.commenting_as') }} <strong>{{ $admin_username ?? '' }}</strong> ({{ __('quickad.admin') }})</p>
                            </div>
                            ELSEIF({{ $logged_in ?? '' }}){
                            <div class="col-md-12">
                                <p>{{ __('quickad.commenting_as') }} <strong>{{ $username ?? '' }}</strong></p>
                            </div>
                            @endif
                            <div class="col-md-12">
                                <textarea rows="5" id="comment-field" class="with-border" name="comment" placeholder="{{ __('quickad.your_comment') }}"
                                          required>{{ $comment ?? '' }}</textarea>
                                <button type="submit" name="comment-submit"
                                        class="button ripple-effect">{{ __('quickad.submit') }}</button>
                                <input type="hidden" name="comment_parent" id="comment_parent" value="0">
                                <input type="hidden" name="comment_post_ID" value="{{ $blog_id ?? '' }}" id="comment_post_ID">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            {{ $else ?? '' }}
            <div class="blog-widget">
                {{ __('quickad.login_post_comment') }}
            </div>
            @endif
            @endif
        </div>
        <div class="col-md-4 hide-under-768px">
            <div class="blog-widget">
                <form action="{{ $link['BLOG'] ?? '#' }}">
                    <div class="input-with-icon">
                        <input class="with-border" type="text" placeholder="{{ __('quickad.search') }}..." name="s"
                               id="search-widget">
                        <i class="icon-feather-search"></i>
                    </div>
                </form>
            </div>
            <div class="blog-widget">
                <h3 class="widget-title">{{ __('quickad.categories') }}</h3>

                <div class="">
                    <ul>
                        @foreach($blog_cat ?? [] as $blog_cat)
                            <li class="clearfix">
                                <a href="{{ data_get($blog_cat ?? [], 'link', '') }}">
                                    <span class="pull-left">{{ data_get($blog_cat ?? [], 'title', '') }}</span>
                                    <span class="pull-right">({{ data_get($blog_cat ?? [], 'blog', '') }})</span></a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="blog-widget">
                <h3 class="widget-title">{{ __('quickad.recent_blog') }}</h3>

                <div class="recent-post-widget">
                    @foreach($recent_blog ?? [] as $recent_blog)
                        <div>
                            @if(($blog_banner ?? ""))
                            <a href="{{ data_get($recent_blog ?? [], 'link', '') }}">
                                <img class="lazy-load post-thumb" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ $site_url ?? '' }}storage/blog/{{ data_get($recent_blog ?? [], 'image', '') }}" alt="{{ data_get($recent_blog ?? [], 'title', '') }}">
                            </a>
                            @endif

                            <div class="recent-post-widget-content">
                                <h2><a href="{{ data_get($recent_blog ?? [], 'link', '') }}">{{ data_get($recent_blog ?? [], 'title', '') }}</a></h2>

                                <div class="post-date">
                                    <i class="icon-feather-clock"></i> {{ data_get($recent_blog ?? [], 'created_at', '') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @if(($testimonials_enable ?? "") && ($show_testimonials_blog ?? ""))
            <div class="blog-widget">
                <h3 class="widget-title">{{ __('quickad.testimonials') }}</h3>
                <div class="single-carousel">
                    @foreach($testimonials ?? [] as $testimonials)
                    <div class="single-testimonial">
                        <div class="single-inner">
                            <div class="testimonial-content">
                                <p>{{ data_get($testimonials ?? [], 'content', '') }}</p>
                            </div>
                            <div class="testi-author-info">
                                <div class="image"><img class="lazy-load" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"  data-original="{{ $site_url ?? '' }}storage/testimonials/{{ data_get($testimonials ?? [], 'image', '') }}" alt="{{ data_get($testimonials ?? [], 'name', '') }}"></div>
                                <h5 class="name">{{ data_get($testimonials ?? [], 'name', '') }}</h5>
                                <span class="designation">{{ data_get($testimonials ?? [], 'designation', '') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="blog-widget">
                <h3 class="widget-title">{{ __('quickad.tags') }}</h3>

                <div class="">
                    <div class="job-tags">
                        {{ $all_tags ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ $site_url ?? '' }}templates/{{ $tpl_name ?? '' }}/js/comment-reply.js"></script>
@include('partials.footer')