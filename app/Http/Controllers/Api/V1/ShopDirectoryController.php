<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\Filterable;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ShopResource;
use App\Models\User;
use App\Services\ShopCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Public shop discovery, deliberately separate from product taxonomy. */
class ShopDirectoryController extends Controller
{
    use Filterable;

    public function __construct(private readonly ShopCategoryService $categories) {}

    /** GET /api/v1/shops — paginated shop profiles. */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'filter.category' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'max:50'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:60'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = User::query()
            ->where('user_type', 'seller')
            ->where('status', '1')
            ->whereNotNull('shop_name')
            ->where('shop_name', '<>', '')
            ->withCount([
                'posts',
                'posts as active_products_count' => fn ($posts) => $posts->active(),
            ]);

        $requestedCategory = trim((string) $request->input('filter.category', ''));
        if ($requestedCategory !== '') {
            $category = $this->categories->resolve($requestedCategory);
            if ($category) {
                $query->where('shop_category', $category);
            } else {
                $query->whereKey(-1);
            }
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where(function ($shops) use ($search): void {
                $shops->where('shop_name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('shop_description', 'like', "%{$search}%")
                    ->orWhere('shop_address', 'like', "%{$search}%");
            });
        }

        $sorts = [
            'created_at' => 'created_at',
            'shop_name' => 'shop_name',
            'active_products' => 'active_products_count',
        ];
        $sort = trim((string) $request->query('sort', ''));
        $sortApplied = false;
        foreach (explode(',', $sort) as $spec) {
            $direction = str_starts_with($spec, '-') ? 'desc' : 'asc';
            $field = ltrim(trim($spec), '-');
            if (isset($sorts[$field])) {
                $query->orderBy($sorts[$field], $direction);
                $sortApplied = true;
            }
        }

        if (!$sortApplied) {
            $query->orderByDesc('shop_verified_at')
                ->orderByDesc('created_at')
                ->orderByDesc('id');
        }

        return $this->ok(
            ShopResource::collection($query->paginate($this->perPage($request, 12)))
        );
    }

    /** GET /api/v1/shop-categories — configured categories with live counts. */
    public function categories(): JsonResponse
    {
        $shops = User::query()
            ->where('user_type', 'seller')
            ->where('status', '1')
            ->whereNotNull('shop_name')
            ->where('shop_name', '<>', '');

        $totalShops = (clone $shops)->count();
        $counts = $shops
            ->whereNotNull('shop_category')
            ->select('shop_category')
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('shop_category')
            ->pluck('aggregate', 'shop_category');

        // Normalise legacy values by slug so casing differences do not make a
        // configured category appear empty (new applications are exact-match).
        $countsBySlug = [];
        foreach ($counts as $category => $count) {
            $slug = Str::slug((string) $category);
            $countsBySlug[$slug] = ($countsBySlug[$slug] ?? 0) + (int) $count;
        }

        $items = array_map(
            fn (string $name): array => [
                'name' => $name,
                'slug' => Str::slug($name),
                'shops_count' => $countsBySlug[Str::slug($name)] ?? 0,
            ],
            $this->categories->all(),
        );

        return $this->ok($items, ['total_shops' => $totalShops]);
    }
}
