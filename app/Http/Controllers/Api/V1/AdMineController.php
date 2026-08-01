<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\Filterable;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreAdRequest;
use App\Http\Requests\V1\UpdateAdRequest;
use App\Http\Resources\V1\AdDetailResource;
use App\Http\Resources\V1\AdResource;
use App\Models\Post;
use App\Services\AdMutationService;
use Illuminate\Http\Request;

/**
 * Owner-side write and management endpoints:
 *   POST   /api/v1/ads                          create
 *   PUT    /api/v1/ads/{id}                     update
 *   DELETE /api/v1/ads/{id}                     soft-delete (status=expire, hide=1)
 *   POST   /api/v1/ads/{id}/images              append images
 *   DELETE /api/v1/ads/{id}/images/{filename}   remove one image
 *   POST   /api/v1/ads/{id}/{action}            hide/unhide/resubmit
 *   GET    /api/v1/me/ads?status=active|pending|expire|hidden
 */
class AdMineController extends Controller
{
    use Filterable;

    public function __construct(private readonly AdMutationService $svc) {}

    public function store(StoreAdRequest $request)
    {
        $post = $this->svc->create(
            $request->user()->id,
            $request->validated(),
            (array) $request->file('images', [])
        );
        return $this->created(new AdDetailResource(
            $post->load(['category', 'subCategory', 'user', 'customData'])
        ));
    }

    public function update(int $id, UpdateAdRequest $request)
    {
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);
        $post = $this->svc->update($post, $request->validated(), (array) $request->file('images', []));
        return $this->ok(new AdDetailResource(
            $post->load(['category', 'subCategory', 'user', 'customData'])
        ));
    }

    public function destroy(int $id, Request $request)
    {
        $post = Post::findOrFail($id);
        $this->authorize('delete', $post);
        $post->forceFill(['status' => 'expire', 'hide' => '1', 'updated_at' => now()])->save();
        return $this->ok(['message' => 'Ad removed.']);
    }

    public function addImages(int $id, Request $request)
    {
        $request->validate(['images' => ['required','array','max:8'], 'images.*' => ['image','mimes:jpg,jpeg,png,webp','max:5120']]);
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);
        $this->svc->update($post, [], (array) $request->file('images', []));
        return $this->ok(new AdDetailResource($post->fresh()));
    }

    public function deleteImage(int $id, string $filename, Request $request)
    {
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);
        $this->svc->deleteImage($post, basename($filename));
        return $this->noContent();
    }

    public function action(int $id, string $action, Request $request)
    {
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);
        match ($action) {
            'hide'     => $post->forceFill(['hide' => '1', 'updated_at' => now()])->save(),
            'unhide'   => $post->forceFill(['hide' => '0', 'updated_at' => now()])->save(),
            'resubmit' => $post->forceFill(['status' => 'pending', 'hide' => '0', 'updated_at' => now(),
                                            'expire_date' => now()->addDays(60)->timestamp])->save(),
            'sold-out' => $post->forceFill(['status' => 'sold_out',  'updated_at' => now()])->save(),
            'restock'  => $post->forceFill(['status' => 'active',    'updated_at' => now()])->save(),
            'remove'   => $post->forceFill(['status' => 'removed', 'hide' => '1', 'updated_at' => now()])->save(),
            'publish'  => $post->forceFill(['status' => 'pending', 'updated_at' => now()])->save(),
            default    => abort(422, 'Unknown action.'),
        };
        return $this->ok(new AdDetailResource($post->fresh()));
    }

    public function mine(Request $request)
    {
        $q = Post::query()->where('user_id', $request->user()->id);

        // ?status=all|active|pending|sold_out|removed|draft|expire|hidden
        match ((string) $request->query('status', '')) {
            'active'   => $q->where('status', 'active')->where('hide', '0'),
            'pending'  => $q->where('status', 'pending'),
            'expire'   => $q->where('status', 'expire'),
            'sold_out' => $q->where('status', 'sold_out'),
            'removed'  => $q->where('status', 'removed'),
            'draft'    => $q->where('status', 'draft'),
            'hidden'   => $q->where('hide',   '1'),
            default    => null,
        };

        // ?condition=new|used
        if ($c = $request->query('condition')) $q->where('condition', $c);

        $q->with(['category', 'subCategory'])->orderByDesc('id');
        return $this->ok(AdResource::collection($q->paginate($this->perPage($request, 12))));
    }

    /**
     * Shop-side aggregated stats for /shop dashboard header + charts.
     *
     *   GET /api/v1/me/shop/stats
     */
    public function shopStats(Request $request)
    {
        $userId = $request->user()->id;
        $q      = fn () => Post::where('user_id', $userId);

        // Ratings snapshot from reviews on my ads.
        $reviewsQ = \App\Models\Review::whereIn('productID', (clone $q())->pluck('id'));
        $avgRating = (float) $reviewsQ->avg('rating');
        $totalReviews = (int)   $reviewsQ->count();

        // "Orders" concept for classifieds = distinct buyer threads on my ads.
        $totalOrders  = \App\Models\Message::whereIn('to_user',   [$userId])->distinct('from_user')->count('from_user');
        $activeOrders = \App\Models\Message::whereIn('to_user',   [$userId])
                          ->where('created_at', '>=', now()->subDays(7))
                          ->distinct('from_user')->count('from_user');

        // Sales this month from confirmed transactions (my ad-upgrade purchases).
        $salesThisMonth = (float) \App\Models\Transaction::where('seller_id', $userId)
                            ->where('status', 'success')
                            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                            ->sum('amount');

        // Wishlist count = distinct users who favourited any of my ads.
        $wishlistCount = \App\Models\Favourite::whereIn('product_id', (clone $q())->pluck('id'))
                          ->distinct('user_id')->count('user_id');

        $countsByStatus = (clone $q())
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        return $this->ok([
            'store' => [
                'rating'        => round($avgRating, 1),
                'reviews_count' => $totalReviews,
                'total_orders'  => $totalOrders,
                'active_orders' => $activeOrders,
            ],
            'sales_this_month' => $salesThisMonth,
            'wishlist_count'   => $wishlistCount,
            'ads' => [
                'total'     => (int) (clone $q())->count(),
                'active'    => (int) ($countsByStatus['active']   ?? 0),
                'pending'   => (int) ($countsByStatus['pending']  ?? 0),
                'sold_out'  => (int) ($countsByStatus['sold_out'] ?? 0),
                'removed'   => (int) ($countsByStatus['removed']  ?? 0),
                'draft'     => (int) ($countsByStatus['draft']    ?? 0),
                'expire'    => (int) ($countsByStatus['expire']   ?? 0),
                'rejected'  => (int) ($countsByStatus['rejected'] ?? 0),
            ],
            // Last 30-day sales series for the chart.
            'sales_series' => $this->salesSeries($userId, 30),
            // Last 30-day view series (aggregate views on my ads by day of creation).
            'views_series' => $this->viewsSeries($userId, 30),
        ]);
    }

    /**
     * GET /api/v1/me/wishlisted — buyers who favourited my ads.
     * Returns { data: [{ user, ad }], meta }.
     */
    public function wishlisted(Request $request)
    {
        $userId = $request->user()->id;
        $mineIds = Post::where('user_id', $userId)->pluck('id');

        $rows = \App\Models\Favourite::with(['user:id,username,name,image', 'post:id,product_name,slug,price,screen_shot'])
            ->whereIn('product_id', $mineIds)
            ->orderByDesc('id')
            ->paginate($this->perPage($request, 20));

        return $this->ok($rows);
    }

    /* --------------------------------------------------------------- */

    private function salesSeries(int $userId, int $days): array
    {
        $rows = \App\Models\Transaction::where('seller_id', $userId)
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as d, SUM(amount) as total')
            ->groupBy('d')->orderBy('d')
            ->get()->keyBy(fn ($r) => (string) $r->d);

        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $out[] = ['date' => $day, 'total' => (float) ($rows[$day]->total ?? 0)];
        }
        return $out;
    }

    private function viewsSeries(int $userId, int $days): array
    {
        $rows = Post::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as d, SUM(view) as total')
            ->groupBy('d')->orderBy('d')
            ->get()->keyBy(fn ($r) => (string) $r->d);

        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $out[] = ['date' => $day, 'total' => (int) ($rows[$day]->total ?? 0)];
        }
        return $out;
    }
}
