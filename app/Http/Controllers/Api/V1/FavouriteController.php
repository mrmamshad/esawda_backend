<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\Filterable;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdResource;
use App\Models\Favourite;
use App\Models\Post;
use Illuminate\Http\Request;

/**
 * Wishlist (❤ icon on ListingCard).
 *
 *   GET    /api/v1/me/favourites            paginated wishlist
 *   POST   /api/v1/ads/{id}/favourite       add
 *   DELETE /api/v1/ads/{id}/favourite       remove
 *
 * Legacy table `favads(id, user_id, product_id)`. No timestamps.
 * Unique constraint is enforced at the application layer to keep
 * the write endpoint idempotent (POST twice → still one row).
 */
class FavouriteController extends Controller
{
    use Filterable;

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $ids = Favourite::where('user_id', $userId)->pluck('product_id');

        $q = Post::query()->whereIn('id', $ids)->with(['category', 'subCategory']);

        return $this->ok(AdResource::collection($q->paginate($this->perPage($request, 12))));
    }

    public function add(int $id, Request $request)
    {
        // Ensure the target ad exists (404 if not).
        Post::findOrFail($id);

        Favourite::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $id,
        ]);

        return $this->ok(['message' => 'Added to favourites.']);
    }

    public function remove(int $id, Request $request)
    {
        Favourite::where('user_id', $request->user()->id)
            ->where('product_id', $id)
            ->delete();

        return $this->noContent();
    }
}
