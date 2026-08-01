<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreReviewRequest;
use App\Http\Resources\V1\ReviewResource;
use App\Models\Post;
use App\Models\Review;
use Illuminate\Http\Request;

/**
 *   GET    /api/v1/ads/{id}/reviews         list reviews on one ad
 *   POST   /api/v1/ads/{id}/reviews         (auth) leave a review
 *   DELETE /api/v1/reviews/{id}             (auth) delete own review
 *
 * Reviews go straight into `publish=1` by default; admins can moderate
 * via the existing Filament panel. Business rules enforced here:
 *   - Cannot review your own ad
 *   - One review per (user, ad) — subsequent POSTs update the existing row
 */
class ReviewController extends Controller
{
    public function index(int $adId, Request $request)
    {
        Post::findOrFail($adId);

        $q = Review::where('productID', $adId)
                   ->where('publish', 1)
                   ->with('user')
                   ->orderByDesc('date');

        return $this->ok(ReviewResource::collection($q->paginate((int) min(50, max(1, $request->query('per_page', 12))))));
    }

    public function store(int $adId, StoreReviewRequest $request)
    {
        $ad = Post::findOrFail($adId);
        $userId = $request->user()->id;

        if ((int) $ad->user_id === (int) $userId) {
            return $this->error('SELF_REVIEW', 'You cannot review your own ad.', 422);
        }

        $review = Review::updateOrCreate(
            ['productID' => $adId, 'user_id' => $userId],
            [
                'rating'   => (float) $request->validated('rating'),
                'comments' => $request->validated('comment'),
                'date'     => now()->toDateString(),
                'publish'  => 1,
            ]
        );

        return $this->created(new ReviewResource($review->load('user')));
    }

    public function destroy(int $reviewId, Request $request)
    {
        $review = Review::findOrFail($reviewId);
        if ((int) $review->user_id !== (int) $request->user()->id
            && ! in_array($request->user()->user_type, ['admin', 'superadmin'], true)) {
            return $this->error('FORBIDDEN', 'You can only delete your own reviews.', 403);
        }
        $review->delete();
        return $this->noContent();
    }
}
