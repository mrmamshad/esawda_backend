<?php

namespace App\Services;

use App\Models\Favourite;
use App\Models\Message;
use App\Models\Post;
use App\Models\Review;
use App\Models\Transaction;

/**
 * Aggregated shop-dashboard numbers (ratings, orders, sales, wishlist,
 * per-status ad counts, sales/views time series). Extracted from
 * AdMineController::shopStats so the controller stays thin and the
 * queries are testable in isolation.
 */
class AdStatsService
{
    public function shopStats(int $userId, int $days = 30): array
    {
        $q = fn () => Post::where('user_id', $userId);

        // Ratings snapshot from reviews on my ads.
        $reviewsQ = Review::whereIn('productID', (clone $q())->pluck('id'));
        $avgRating    = (float) $reviewsQ->avg('rating');
        $totalReviews = (int)   $reviewsQ->count();

        // "Orders" concept for classifieds = distinct buyer threads on my ads.
        $totalOrders  = Message::whereIn('to_user', [$userId])->distinct('from_user')->count('from_user');
        $activeOrders = Message::whereIn('to_user', [$userId])
                          ->where('created_at', '>=', now()->subDays(7))
                          ->distinct('from_user')->count('from_user');

        // Sales this month from confirmed transactions (my ad-upgrade purchases).
        $salesThisMonth = (float) Transaction::where('seller_id', $userId)
                            ->where('status', 'success')
                            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                            ->sum('amount');

        // Wishlist count = distinct users who favourited any of my ads.
        $wishlistCount = Favourite::whereIn('product_id', (clone $q())->pluck('id'))
                          ->distinct('user_id')->count('user_id');

        $countsByStatus = (clone $q())
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        return [
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
            'sales_series' => $this->salesSeries($userId, $days),
            'views_series' => $this->viewsSeries($userId, $days),
        ];
    }

    private function salesSeries(int $userId, int $days): array
    {
        $rows = Transaction::where('seller_id', $userId)
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
