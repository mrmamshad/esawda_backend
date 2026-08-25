<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\Filterable;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AdDetailResource;
use App\Http\Resources\V1\AdResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * /api/v1/ads — public read surface used by the Browse page, Ad Detail,
 * "Ads from same seller", "Featured Ad" rail, and search-suggest.
 *
 * Filter grammar (from Filterable trait, Step-3 spec):
 *   ?filter[category]=1
 *   ?filter[sub_category]=10
 *   ?filter[city]=Dhaka
 *   ?filter[country]=BD
 *   ?filter[price][gte]=1000&filter[price][lte]=5000
 *   ?filter[featured]=1
 *   ?filter[user]=42
 *   ?q=bmw
 *   ?sort=-created_at,price
 *   ?per_page=12&page=2
 *   ?include=category,sub_category,user
 */
class AdController extends Controller
{
    use Filterable;

    /** API field → DB column whitelist for filters. */
    private const ALLOWED_FILTERS = [
        'category' => 'category',
        'sub_category' => 'sub_category',
        'city' => 'city',
        'state' => 'state',
        'country' => 'country',
        'price' => 'price',
        'featured' => 'featured',
        'urgent' => 'urgent',
        'highlight' => 'highlight',
        'user' => 'user_id',
        'condition' => 'condition',
    ];

    /** API field → DB column whitelist for sort. */
    private const ALLOWED_SORTS = [
        'created_at' => 'created_at',
        'price' => 'price',
        'view' => 'view',
        'featured' => 'featured',
        'id' => 'id',
    ];

    public function index(Request $request)
    {
        $query = Post::query()->active();
        $this->applyFilters(
            $query, $request,
            self::ALLOWED_FILTERS, self::ALLOWED_SORTS,
            ['product_name', 'description', 'tag']
        );
        $this->applyCustomFieldFilters($query, $request);

        // `since_hours=N` → only ads created within the last N hours. Used by
        // the home "Last 24 hours" section so its title is truthful.
        if (($h = (int) $request->query('since_hours')) > 0) {
            $query->where('created_at', '>=', now()->subHours($h));
        }

        // Default sort surfaces featured/urgent first, newest last — matches the
        // "Featured" badge behaviour in the Browse-page reference.
        if (!$request->query('sort')) {
            $query->orderByDesc('featured')->orderByDesc('urgent')->orderByDesc('id');
        }
        $this->applyIncludes($query, $request);

        return $this->ok(AdResource::collection($query->paginate($this->perPage($request, 12))));
    }

    /**
     * Handle ?filter[custom][12]=Petrol style filters by inner-joining
     * against custom_data once per requested field. Each field becomes
     * an AND clause; multiple values within one field are OR'd.
     *
     * Supports operators too: filter[custom][12][in]=Petrol,Diesel
     */
    private function applyCustomFieldFilters($query, Request $request): void
    {
        $filters = $request->query('filter', []);
        if (!is_array($filters) || empty($filters['custom']) || !is_array($filters['custom'])) {
            return;
        }

        foreach ($filters['custom'] as $fieldId => $spec) {
            $fieldId = (int) $fieldId;
            if ($fieldId <= 0) {
                continue;
            }

            $alias = 'cd_'.$fieldId;
            $query->join("custom_data as {$alias}", function ($j) use ($alias, $fieldId) {
                $j->on("{$alias}.product_id", '=', 'product.id')
                    ->where("{$alias}.field_id", $fieldId);
            });

            if (is_array($spec)) {
                foreach ($spec as $op => $val) {
                    switch ($op) {
                        case 'in':
                            $arr = is_array($val) ? $val : explode(',', (string) $val);
                            $query->whereIn("{$alias}.field_data", array_map('trim', $arr));
                            break;
                        case 'gte': $query->where("{$alias}.field_data", '>=', $val);
                            break;
                        case 'lte': $query->where("{$alias}.field_data", '<=', $val);
                            break;
                        case 'like': $query->where("{$alias}.field_data", 'like', "%{$val}%");
                            break;
                        default:     $query->where("{$alias}.field_data", $val);
                    }
                }
            } else {
                $query->where("{$alias}.field_data", $spec);
            }
        }
        $query->select('product.*'); // avoid custom_data columns polluting SELECT *
    }

    public function show(string $idSlug, Request $request)
    {
        $id = (int) explode('-', $idSlug, 2)[0];
        abort_if($id <= 0, 404);

        $ad = Post::with(['category', 'subCategory', 'user', 'customData'])
            ->where('id', $id)
            ->where(function ($q) {
                // Public: only active, non-hidden ads. The ad's own
                // author may still preview a pending/rejected ad.
                $q->where(fn ($inner) => $inner->where('status', 'active')->where('hide', '0')->where(function ($exp) {
                    $exp->whereNull('expire_date')
                        ->orWhere('expire_date', '=', 0)
                        ->orWhere('expire_date', '>', time());
                }));
                if ($user = auth('sanctum')->user()) {
                    $q->orWhere('user_id', $user->id);
                }
            })
            ->firstOrFail();

        if ($ad->bundle_items) {
            $ad->setRelation('bundleItems', Post::whereIn('id', $ad->bundle_items)->get());
        }

        // Fire-and-forget view counter (no need to block the response).
        if ($ad->status->value === 'active' && $ad->hide === '0') {
            Post::where('id', $id)->increment('view');
        }

        return $this->ok(new AdDetailResource($ad));
    }

    public function featured(Request $request)
    {
        $limit = max(1, min(24, (int) $request->query('limit', 6)));
        $key = "ads.featured.$limit.".(string) $request->query('include', 'category,sub_category,user');
        // Featured rail changes often (promotions rotate) → short 120s TTL.
        $rows = Cache::remember($key, 120, function () use ($limit, $request) {
            $q = Post::query()->active()->featured()
                ->orderByDesc('id')
                ->limit($limit);
            $this->applyIncludes($q, $request);

            return $q->get();
        });

        return $this->ok(AdResource::collection($rows));
    }

    public function similar(int $id, Request $request)
    {
        $limit = max(1, min(24, (int) $request->query('limit', 6)));
        $key = "ads.similar.$id.$limit";
        $rows = Cache::remember($key, 300, function () use ($id, $limit, $request) {
            $ad = Post::findOrFail($id);
            $q = Post::query()->active()
                ->where('id', '!=', $id)
                ->where(function ($sub) use ($ad) {
                    $sub->where('user_id', $ad->user_id)
                        ->orWhere('sub_category', $ad->sub_category)
                        ->orWhere('category', $ad->category);
                })
                ->orderByDesc('id')
                ->limit($limit);
            $this->applyIncludes($q, $request);

            return $q->get();
        });

        return $this->ok(AdResource::collection($rows));
    }

    public function searchSuggest(Request $request)
    {
        $needle = trim((string) $request->query('q', ''));
        if (mb_strlen($needle) < 2) {
            return $this->ok([]);
        }

        $hits = Cache::remember('ads.suggest.'.md5(mb_strtolower($needle)), 60, function () use ($needle) {
            return Post::query()->active()
                ->where('product_name', 'like', "%{$needle}%")
                ->orderByDesc('id')
                ->limit(10)
                ->get(['id', 'slug', 'product_name', 'price']);
        });

        return $this->ok($hits->map(fn ($h) => [
            'id' => (int) $h->id,
            'url_slug' => $h->id.'-'.($h->slug ?: 'ad'),
            'title' => $h->product_name,
            'price' => (int) $h->price,
        ]));
    }

    /** Apply ?include=cat,sub_cat,user selectively (whitelist). */
    private function applyIncludes($query, Request $request): void
    {
        $requested = array_filter(explode(',', (string) $request->query('include', 'category,sub_category,user')));
        $map = [
            'category' => 'category',
            'sub_category' => 'subCategory',
            'user' => 'user',
        ];
        foreach ($requested as $r) {
            if (isset($map[trim($r)])) {
                $query->with($map[trim($r)]);
            }
        }
    }
}
