<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BlogResource;
use App\Http\Resources\V1\FaqResource;
use App\Http\Resources\V1\PageResource;
use App\Http\Resources\V1\PlanResource;
use App\Http\Resources\V1\TestimonialResource;
use App\Http\Requests\V1\ContactMessageRequest;
use App\Models\Blog;
use App\Models\EmailQueue;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Testimonial;
use Illuminate\Http\Request;

/**
 * Static / content endpoints — everything a marketing site needs but
 * that doesn't fit any of the other domain controllers.
 *
 *   GET /api/v1/pages                paginated CMS pages
 *   GET /api/v1/pages/{slug}         single page
 *   GET /api/v1/faqs                 flat list, weight-ordered
 *   GET /api/v1/testimonials         seller-profile-bottom cards
 *   GET /api/v1/plans                pricing tiers
 *   GET /api/v1/blogs                blog index (paginated, filter by tag)
 *   GET /api/v1/blogs/{idSlug}       single blog post
 */
class ContentController extends Controller
{
    /* ---- Pages ---------------------------------------------------- */

    public function pages(Request $request)
    {
        $q = Page::query()->where('active', 1);
        if ($lang = $request->query('lang')) $q->where('translation_lang', $lang);
        $q->orderBy('name');
        return $this->ok(PageResource::collection($q->paginate(min(50, (int) $request->query('per_page', 20)))));
    }

    public function page(string $slug)
    {
        $page = Page::where('slug', $slug)->where('active', 1)->firstOrFail();
        return $this->ok(new PageResource($page));
    }

    /* ---- FAQs ----------------------------------------------------- */

    public function faqs(Request $request)
    {
        $q = Faq::query()->where('active', 1);
        if ($lang = $request->query('lang')) $q->where('translation_lang', $lang);
        $q->orderBy('faq_weight')->orderBy('faq_id');
        return $this->ok(FaqResource::collection($q->get()));
    }

    /* ---- Testimonials -------------------------------------------- */

    public function testimonials(Request $request)
    {
        $limit = max(1, min(50, (int) $request->query('limit', 12)));
        return $this->ok(TestimonialResource::collection(Testimonial::query()->orderByDesc('id')->limit($limit)->get()));
    }

    /* ---- Plans --------------------------------------------------- */

    public function plans(Request $request)
    {
        $q = Plan::query()->where('status', 1)->orderBy('monthly_price');
        return $this->ok(PlanResource::collection($q->get()));
    }

    /* ---- Blogs --------------------------------------------------- */

    public function blogs(Request $request)
    {
        $q = Blog::query()->where('status', 'publish')->with(['author', 'categories'])->orderByDesc('id');

        if ($needle = trim((string) $request->query('q', ''))) {
            $q->where(fn ($s) => $s->where('title', 'like', "%{$needle}%")
                                   ->orWhere('description', 'like', "%{$needle}%"));
        }
        if ($tag = $request->query('tag')) {
            $q->where('tags', 'like', "%{$tag}%");
        }
        if ($catSlug = $request->query('category')) {
            $q->whereHas('categories', fn ($c) => $c->where('slug', $catSlug));
        }
        if ($authorUsername = $request->query('author')) {
            $q->whereHas('author', fn ($a) => $a->where('username', $authorUsername));
        }

        $perPage = max(1, min(30, (int) $request->query('per_page', 9)));
        return $this->ok(BlogResource::collection($q->paginate($perPage)));
    }

    /* ---- Blog Categories (public) --------------------------------- */

    public function blogCategories(Request $request)
    {
        $rows = \App\Models\BlogCategory::query()->orderBy('title')->get();
        return $this->ok($rows->map(fn ($c) => [
            'id'    => (int) $c->id,
            'title' => $c->title,
            'slug'  => $c->slug,
        ])->values());
    }

    public function blog(string $idSlug)
    {
        $id = (int) explode('-', $idSlug, 2)[0];
        abort_if($id <= 0, 404);
        $blog = Blog::with(['author', 'categories'])->where('id', $id)->where('status', 'publish')->firstOrFail();
        return $this->ok(new BlogResource($blog));
    }

    /* ---- Contact form (public) ------------------------------------ */

    public function contact(ContactMessageRequest $request)
    {
        $data  = $request->validated();
        $admin = (string) (config('quickad.admin_email') ?: env('MAIL_TO', 'admin@offersale.local'));

        EmailQueue::create([
            'email'   => $admin,
            'toname'  => 'offersale. admin',
            'subject' => 'Contact form: ' . ($data['subject'] ?? '(no subject)'),
            'body'    => "From: {$data['name']} <{$data['email']}>\n\n{$data['message']}",
        ]);

        return $this->ok(['message' => 'Thanks — your message has been sent.']);
    }
}
