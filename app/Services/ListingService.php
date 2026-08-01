<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * Consolidates the search + filter logic that lived in `php/listing.php`.
 * Handles category, subcategory, city, keyword, price-range, custom-field
 * filters, ordering, and pagination.
 */
class ListingService
{
    public const DEFAULT_LIMIT = 12;

    public function search(Request $request): LengthAwarePaginator
    {
        $q = Post::query()->active();

        if ($cat = $request->query('cat'))        $q->where('category', $cat);
        if ($sub = $request->query('subcat'))     $q->where('sub_category', $sub);
        if ($kw  = $request->query('keywords'))   $q->where('product_name', 'like', "%$kw%");
        if ($city = $request->query('city'))      $q->where('city', $city);
        if ($country = $request->query('country'))$q->where('country', $country);

        if ($request->filled('range1'))           $q->where('price', '>=', (int) $request->query('range1'));
        if ($request->filled('range2'))           $q->where('price', '<=', (int) $request->query('range2'));

        // Ordering — legacy accepts `sort=price|date|view` + `order=asc|desc`.
        $sortMap = ['price' => 'price', 'date' => 'created_at', 'view' => 'view'];
        $sort  = $sortMap[$request->query('sort', 'date')] ?? 'created_at';
        $order = strtolower($request->query('order', 'desc')) === 'asc' ? 'asc' : 'desc';
        // featured/urgent first (legacy behavior). Use orderBy (portable across sqlite/mysql).
        $q->orderByDesc('featured')
          ->orderByDesc('urgent')
          ->orderBy($sort, $order);

        $limit = min(60, max(1, (int) $request->query('limit', self::DEFAULT_LIMIT)));
        return $q->paginate($limit)->withQueryString();
    }

    /** Legacy: `get_items()` — returns items filtered by promo flag. */
    public function promoted(int $limit = 8): \Illuminate\Support\Collection
    {
        return Post::active()
            ->where(function ($q) {
                $q->where('featured', '1')
                  ->orWhere('urgent',   '1')
                  ->orWhere('highlight','1');
            })
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }
}
