<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Option;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Testimonial;
use App\Services\ListingService;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/**
 * Legacy `php/home.php` — landing page.
 * Assembles categories, popular cities, featured ads, blog posts,
 * testimonials and pricing plans, then hands off to the themed
 * `index.blade.php` (converted from `index.tpl`).
 */
class HomeController extends Controller
{
    public function __construct(
        private ListingService $listing,
        private ThemeRenderer $theme,
    ) {}

    public function index(Request $request)
    {
        $categories = Category::orderBy('cat_order')->get();
        $data = [
            // Legacy variable names (theme .blade.php files use these directly)
            'category' => $categories,       // used by {LOOP: CATEGORY}
            'cat' => $categories,       // used by {LOOP: CAT}
            'item' => $this->listing->promoted(8),   // premium ads
            'item2' => Post::active()->orderByDesc('id')->limit(8)->get(), // latest ads
            'testimonials' => Testimonial::orderByDesc('id')->limit(6)->get(),
            'recent_blog' => Blog::where('status', 'publish')
                ->orderByDesc('created_at')->limit(3)->get(),
            'plans' => Plan::where('status', 1)->get(),
            'sub_types' => [],
            // Site option toggles (0/1) — pull from ad_options when present
            'blog_enable' => (int) $this->opt('blog_enable', 1),
            'show_blog_home' => (int) $this->opt('show_blog_home', 1),
            'show_testimonials_home' => (int) $this->opt('show_testimonials_home', 1),
            'show_membershipplan_home' => (int) $this->opt('show_membershipplan_home', 1),
            'auto_detect_location' => (int) $this->opt('auto_detect_location', 0),
            'gmap_api_key' => $this->opt('gmap_api_key', ''),
            'blog_banner' => $this->opt('blog_banner', ''),
            'banner_image' => $this->opt('banner_image', ''),
            'currency_sign' => $this->opt('currency_sign', '$'),
            'ad_home_page_below_search_section' => $this->opt('ad_home_page_below_search_section', ''),
            'ad_home_page_below_category_section' => $this->opt('ad_home_page_below_category_section', ''),
            'ad_home_page_below_featured_section' => $this->opt('ad_home_page_below_featured_section', ''),
            'ad_home_page_below_latest_section' => $this->opt('ad_home_page_below_latest_section', ''),
        ];

        return $this->safeRender('index', $data, ['legacy' => 'home.php', 'action' => 'index']);
    }

    /** Read a value from the legacy `ad_options` key-value table (with cache). */
    private function opt(string $key, mixed $default = null): mixed
    {
        static $cache = null;
        if ($cache === null) {
            try {
                $cache = Option::pluck('option_value', 'option_name')->toArray();
            } catch (\Throwable) {
                $cache = [];
            }
        }

        return $cache[$key] ?? $default;
    }

    public function variant1(Request $request)
    {
        return $this->safeRender('index-new', [], ['legacy' => 'index1.php', 'action' => 'index']);
    }

    public function variant2(Request $request)
    {
        return $this->safeRender('home-map', [], ['legacy' => 'index2.php', 'action' => 'index']);
    }

    /** Render theme view if it exists, otherwise fall back to placeholder (avoids crashes when DB is empty). */
    private function safeRender(string $view, array $data, array $fallback)
    {
        try {
            return $this->theme->render($view, $data);
        } catch (\Throwable) {
            return view('placeholder', $fallback);
        }
    }
}
