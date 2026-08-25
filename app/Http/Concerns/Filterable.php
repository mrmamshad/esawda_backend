<?php

namespace App\Http\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Query-string → Eloquent filter grammar shared across list endpoints.
 *
 * Supported query shape (documented in Step 3 of the API plan):
 *   ?filter[category]=3
 *   ?filter[price][gte]=1000&filter[price][lte]=5000
 *   ?filter[city][in]=Dhaka,Chittagong
 *   ?filter[title][like]=bmw
 *   ?sort=-created_at,price
 *   ?q=bmw          (full-text over $fullText columns)
 *   ?per_page=24    (capped 1..60)
 *   ?page=2
 *   ?include=user,category  (whitelisted per controller)
 *
 * Controllers declare which columns/operators are allowed via the two
 * static maps below to prevent arbitrary-column SQL leakage.
 */
trait Filterable
{
    /**
     * @param  array  $allowedFilters  ['api_field' => 'db_column', ...]
     * @param  array  $allowedSorts  ['api_field' => 'db_column', ...]
     * @param  array  $fullTextCols  ['product_name', 'description', ...]
     */
    protected function applyFilters(
        Builder $query,
        Request $request,
        array $allowedFilters,
        array $allowedSorts,
        array $fullTextCols = []
    ): Builder {
        // ---- Structured filters ------------------------------------------
        $filters = (array) $request->query('filter', []);
        foreach ($filters as $field => $spec) {
            if (!array_key_exists($field, $allowedFilters)) {
                continue;
            }
            $col = $allowedFilters[$field];

            if (is_array($spec)) {
                foreach ($spec as $op => $val) {
                    $this->applyOperator($query, $col, $op, $val);
                }
            } else {
                $query->where($col, $spec);
            }
        }

        // ---- Free-text search --------------------------------------------
        if ($fullTextCols && ($q = trim((string) $request->query('q', '')))) {
            $query->where(function ($sub) use ($fullTextCols, $q) {
                foreach ($fullTextCols as $c) {
                    $sub->orWhere($c, 'like', "%{$q}%");
                }
            });
        }

        // ---- Sort --------------------------------------------------------
        $sort = trim((string) $request->query('sort', ''));
        if ($sort !== '') {
            foreach (explode(',', $sort) as $spec) {
                $spec = trim($spec);
                if ($spec === '') {
                    continue;
                }
                $dir = 'asc';
                if (str_starts_with($spec, '-')) {
                    $dir = 'desc';
                    $spec = substr($spec, 1);
                }
                if (isset($allowedSorts[$spec])) {
                    $query->orderBy($allowedSorts[$spec], $dir);
                }
            }
        }

        return $query;
    }

    private function applyOperator(Builder $q, string $col, string $op, $val): void
    {
        switch ($op) {
            case 'gte': $q->where($col, '>=', $val);
                break;
            case 'lte': $q->where($col, '<=', $val);
                break;
            case 'gt':  $q->where($col, '>', $val);
                break;
            case 'lt':  $q->where($col, '<', $val);
                break;
            case 'ne':  $q->where($col, '!=', $val);
                break;
            case 'like':$q->where($col, 'like', '%'.$val.'%');
                break;
            case 'in':
                $arr = is_array($val) ? $val : explode(',', (string) $val);
                $q->whereIn($col, array_map('trim', $arr));
                break;
            default:    $q->where($col, $val);
        }
    }

    protected function perPage(Request $request, int $default = 12, int $max = 60): int
    {
        return max(1, min($max, (int) $request->query('per_page', $default)));
    }
}
