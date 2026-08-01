<?php

namespace App\Services;

use App\Models\Review;

/**
 * Port of `plugins/starreviews/*.php`. Handles submit + aggregate
 * calculations for the star-review widget.
 */
class ReviewService
{
    public function submit(int $productId, int $userId, float $rating, string $comment = ''): Review
    {
        $rating = max(1, min(5, $rating));
        return Review::create([
            'productID' => (string) $productId,
            'user_id'   => $userId,
            'rating'    => $rating,
            'comments'  => $comment,
            'date'      => now()->toDateString(),
            'publish'   => 1,
        ]);
    }

    public function average(int $productId): float
    {
        return round((float) Review::where('productID', (string) $productId)
                                    ->where('publish', 1)->avg('rating'), 1);
    }

    public function count(int $productId): int
    {
        return Review::where('productID', (string) $productId)
                     ->where('publish', 1)->count();
    }
}
