<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdResource;
use App\Http\Resources\V1\BlogResource;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Resources\V1\PlanResource;
use App\Http\Resources\V1\TestimonialResource;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Option;
use App\Models\Plan;
use App\Models\Post;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;

/**
 * GET /api/v1/home — one-shot homepage payload.
 *
 * The Next.js homepage previously fanned out to ~14 separate API calls
 * (10× /ads variants + categories + plans + testimonials + blogs +
 * settings) per ISR rebuild. Cross-host TLS on every one of those is what
 * made rebuilds slow and cold starts painful. This endpoint runs the same
 * queries in ONE process/round-trip and caches the assembled payload 120s
 * (matching the homepage ISR revalidate).
 *
 * Shapes intentionally mirror the individual endpoints (same Resources) so
 * the frontend can swap the fan-out for this call with zero mapping code.
 */
class HomeController extends Controller
{
    public function index()
    {
        $payload = Cache::remember('home.payload', 120, function () {
            return [
                'settings' => $this->settings(),
                'categories' => CategoryResource::collection($this->categories())->resolve(),
                'sections' => [
                    'featured' => $this->conditioned(fn ($q) => $q->featured(), 6),
                    'urgent' => $this->conditioned(fn ($q) => $q->where('urgent', '1'), 8),
                    'last24h' => $this->conditioned(
                        fn ($q) => $q->where('created_at', '>=', now()->subHours(24)), 8
                    ),
                    'highlights' => $this->conditioned(fn ($q) => $q->where('highlight', '1'), 8),
                    'used' => $this->conditioned(fn ($q) => $q, 8),
                ],
                'plans' => PlanResource::collection(
                    Plan::query()->where('status', 1)->orderBy('monthly_price')->get()
                )->resolve(),
                'testimonials' => TestimonialResource::collection(
                    Testimonial::query()->orderByDesc('id')->limit(3)->get()
                )->resolve(),
                'blogs' => BlogResource::collection(
                    Blog::query()->where('status', 'publish')->with(['author', 'categories'])
                        ->orderByDesc('id')->limit(3)->get()
                )->resolve(),
            ];
        });

        return $this->ok($payload);
    }

    /** One section in { used, new } shape, newest-first like the homepage. */
    private function conditioned(callable $scope, int $limit): array
    {
        $run = function (string $condition) use ($scope, $limit) {
            $q = Post::query()->active()->with(['category', 'subCategory', 'user'])
                ->where('condition', $condition);
            $scope($q);

            return AdResource::collection(
                $q->orderByDesc('created_at')->orderByDesc('id')->limit($limit)->get()
            )->resolve();
        };

        return ['used' => $run('used'), 'new' => $run('new')];
    }

    private function categories()
    {
        return Category::orderBy('cat_order')
            ->with(['subCategories' => function ($q) {
                $q->orderBy('cat_order')
                    ->withCount(['posts as ads_count' => fn ($a) => $a->active()]);
            }])
            ->withCount([
                'posts as ads_count' => fn ($a) => $a->active(),
                'posts as new_count' => fn ($a) => $a->active()->where('condition', 'new'),
                'posts as used_count' => fn ($a) => $a->active()->where('condition', 'used'),
            ])
            ->get();
    }

    private function settings(): array
    {
        $defaults = [
            'site_name' => config('app.name', 'offersale.'),
            'default_currency' => 'BDT',
            'currency_symbol' => '৳',
            'currency_code' => 'BDT',
            'default_locale' => config('app.locale', 'en'),
        ];

        try {
            $rows = Option::all(['option_name', 'option_value'])
                ->pluck('option_value', 'option_name')->toArray();

            return array_replace($defaults, $rows);
        } catch (\Throwable) {
            return $defaults;
        }
    }
}
