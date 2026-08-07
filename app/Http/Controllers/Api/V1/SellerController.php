<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\Filterable;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdResource;
use App\Http\Resources\V1\ReviewResource;
use App\Http\Resources\V1\SellerResource;
use App\Models\Post;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Public seller-facing endpoints. Everything is keyed on username so the
 * frontend URL /seller/hasnat5 works without exposing numeric ids.
 *
 *   GET /api/v1/sellers/{username}            hero + stats
 *   GET /api/v1/sellers/{username}/ads        paginated listings
 *   GET /api/v1/sellers/{username}/reviews    testimonials
 */
class SellerController extends Controller
{
    use Filterable;

    public function show(string $username)
    {
        $user = User::query()
            ->where('username', $username)
            ->orWhere('id', is_numeric($username) ? (int) $username : -1)
            ->firstOrFail();

        // Seller stats change rarely per page-view; cache them 5 min and
        // collapse the 3 separate Post COUNTs into one grouped conditional
        // aggregate (3 queries -> 2: grouped ad stats + rating).
        $stats = Cache::remember("seller.stats.{$user->id}", 300, function () use ($user) {
            // One grouped pass: total / active / sold counts by status.
            $statusCounts = Post::where('user_id', $user->id)
                ->selectRaw("COUNT(*) as total, SUM(status = 'active' AND hide = '0') as active, SUM(status = 'expire') as sold")
                ->first();

            $prefix     = DB::getTablePrefix();
            $reviewsRef = $prefix . 'reviews';

            $ratingRow = DB::table('reviews')
                ->join('product', 'reviews.productID', '=', 'product.id')
                ->where('product.user_id', $user->id)
                ->where('reviews.publish', 1)
                ->selectRaw("AVG({$reviewsRef}.rating) as avg_rating, COUNT(*) as reviews_count")
                ->first();

            return [
                'ads_total'    => (int) ($statusCounts->total ?? 0),
                'ads_active'   => (int) ($statusCounts->active ?? 0),
                'ads_sold'     => (int) ($statusCounts->sold ?? 0),
                'avg_rating'   => $ratingRow?->avg_rating,
                'reviews_count' => (int) ($ratingRow?->reviews_count ?? 0),
            ];
        });

        $user->ads_total     = $stats['ads_total'];
        $user->ads_active    = $stats['ads_active'];
        $user->ads_sold      = $stats['ads_sold'];
        $user->avg_rating    = $stats['avg_rating'];
        $user->reviews_count = $stats['reviews_count'];

        return $this->ok(new SellerResource($user));
    }

    public function ads(string $username, Request $request)
    {
        $user = User::where('username', $username)->orWhere('id', is_numeric($username) ? (int) $username : -1)->firstOrFail();

        $q = Post::query()->active()->where('user_id', $user->id)
                 ->with(['category', 'subCategory']);

        $this->applyFilters(
            $q, $request,
            [
                'category'     => 'category',
                'sub_category' => 'sub_category',
                'city'         => 'city',
                'country'      => 'country',
                'price'        => 'price',
                'featured'     => 'featured',
            ],
            [
                'created_at' => 'created_at',
                'price'      => 'price',
                'featured'   => 'featured',
                'view'       => 'view',
            ],
            ['product_name', 'description', 'tag']
        );

        if (! $request->query('sort')) {
            $q->orderByDesc('featured')->orderByDesc('id');
        }

        return $this->ok(AdResource::collection($q->paginate($this->perPage($request, 12))));
    }

    public function reviews(string $username, Request $request)
    {
        $user = User::where('username', $username)->firstOrFail();

        $q = Review::query()
            ->join('product', 'reviews.productID', '=', 'product.id')
            ->where('product.user_id', $user->id)
            ->where('reviews.publish', 1)
            ->with('user')
            ->select('reviews.*')
            ->orderByDesc('reviews.date');

        return $this->ok(ReviewResource::collection($q->paginate($this->perPage($request, 12))));
    }
}
