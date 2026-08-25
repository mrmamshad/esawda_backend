<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Admin dashboard KPIs + trend series for the Next.js /admin panel.
 * Accepts an optional master date filter:
 *   ?range=today|week|month|custom  (+ &from=&to= for custom)
 * When a range is supplied every aggregate (counts, trend, recent lists,
 * category breakdown, window series) is scoped to that window.
 *
 * Returns:
 *   counts          - headline totals (window-scoped when filtered)
 *   trend           - % deltas vs the previous equal-length window
 *   recent          - latest ads / users / transactions in the window
 *   revenue_series  - trailing 7D/30D/90D/1Y (unfiltered, legacy chart)
 *   category_breakdown - donut chart data (window-scoped)
 *   top_categories  - top-6 by ad volume (window-scoped)
 *   user_growth     - cumulative signup line (unfiltered, legacy)
 *   window          - the resolved filter window + bucketed series
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = (string) ($request->query('range') ?? 'today');
        $from = $request->query('from');
        $to = $request->query('to');

        [$fromDate, $toDate] = $this->resolveWindow($range, $from, $to);

        // Bucket the cache key to the hour so "today"/"week"/"month" stay
        // stable for a short time and don't recompute on every poll.
        $cacheKey = 'admin.dashboard:'.$range.':'.$fromDate->format('YmdH').':'.$toDate->format('YmdH');

        return $this->ok(Cache::remember($cacheKey, 300, function () use ($range, $fromDate, $toDate) {
            $currStart = $fromDate;
            $currEnd = $toDate;

            // Previous window: equal length immediately before the current one.
            $lenSecs = max(1, $currStart->diffInSeconds($currEnd));
            $prevEnd = $currStart->copy()->subSecond();
            $prevStart = $prevEnd->copy()->subSeconds($lenSecs);

            $delta = fn ($curr, $prev) => $prev > 0 ? (($curr - $prev) / $prev) * 100 : ($curr > 0 ? 100 : 0);

            $counts = [
                'users' => User::whereBetween('created_at', [$currStart, $currEnd])->count(),
                'ads_total' => Post::whereBetween('created_at', [$currStart, $currEnd])->count(),
                'ads_active' => Post::whereBetween('created_at', [$currStart, $currEnd])->where('status', 'active')->where('hide', '0')->count(),
                'ads_pending' => Post::whereBetween('created_at', [$currStart, $currEnd])->where('status', 'pending')->count(),
                'ads_expired' => Post::whereBetween('created_at', [$currStart, $currEnd])->where('status', 'expire')->count(),
                'tx_total' => Transaction::whereBetween('created_at', [$currStart, $currEnd])->count(),
                'tx_success' => Transaction::whereBetween('created_at', [$currStart, $currEnd])->where('status', 'success')->count(),
                'revenue_total' => (float) Transaction::whereBetween('created_at', [$currStart, $currEnd])->where('status', 'success')->sum('amount'),
            ];

            $trend = [
                'users_delta' => round($delta($counts['users'], User::whereBetween('created_at', [$prevStart, $prevEnd])->count()), 1),
                'ads_delta' => round($delta($counts['ads_total'], Post::whereBetween('created_at', [$prevStart, $prevEnd])->count()), 1),
                'revenue_delta' => round($delta($counts['revenue_total'], (float) Transaction::whereBetween('created_at', [$prevStart, $prevEnd])->where('status', 'success')->sum('amount')), 1),
                'tx_delta' => round($delta($counts['tx_total'], Transaction::whereBetween('created_at', [$prevStart, $prevEnd])->count()), 1),
            ];

            return [
                'counts' => $counts,
                'trend' => $trend,
                'recent' => [
                    'ads' => Post::whereBetween('created_at', [$currStart, $currEnd])->orderByDesc('id')->limit(6)->get(['id', 'product_name', 'price', 'status', 'created_at']),
                    'users' => User::whereBetween('created_at', [$currStart, $currEnd])->orderByDesc('id')->limit(6)->get(['id', 'username', 'email', 'created_at']),
                    'transactions' => Transaction::whereBetween('created_at', [$currStart, $currEnd])->orderByDesc('id')->limit(6)->get(['id', 'seller_id', 'amount', 'status', 'transaction_gatway', 'product_name', 'created_at']),
                ],
                'revenue_series' => [
                    '7D' => $this->salesSeries(7),
                    '30D' => $this->salesSeries(30),
                    '90D' => $this->salesSeries(90),
                    '1Y' => $this->salesSeriesMonthly(12),
                ],
                'category_breakdown' => $this->categoryBreakdown($currStart, $currEnd),
                'top_categories' => $this->topCategories($currStart, $currEnd),
                'user_growth' => $this->userGrowthSeries(30),
                'window' => [
                    'range' => $range,
                    'from' => $currStart->toDateTimeString(),
                    'to' => $currEnd->toDateTimeString(),
                    'revenue' => $this->bucketed('revenue', $currStart, $currEnd),
                    'users' => $this->bucketed('users', $currStart, $currEnd),
                    'transactions' => $this->bucketed('transactions', $currStart, $currEnd),
                ],
            ];
        }));
    }

    /** Resolve the filter into a concrete [from, to] Carbon window. */
    private function resolveWindow(string $range, ?string $from, ?string $to): array
    {
        $now = now();
        $f = $from ? Carbon::parse($from) : null;
        $t = $to ? Carbon::parse($to) : null;

        switch ($range) {
            case 'week':
                return [$now->copy()->startOfWeek()->startOfDay(), $now->copy()];
            case 'month':
                return [$now->copy()->startOfMonth()->startOfDay(), $now->copy()];
            case 'custom':
                if ($f && $t) {
                    return [$f->copy()->startOfDay(), $t->copy()->endOfDay()];
                }

                return [$now->copy()->startOfDay(), $now->copy()];
            case 'today':
            default:
                return [$now->copy()->startOfDay(), $now->copy()];
        }
    }

    /**
     * Bucket a metric across the window. Granularity adapts to the span:
     * hourly for a single day, daily up to 45 days, monthly beyond that.
     * "users" is returned as a cumulative running total within the window.
     */
    private function bucketed(string $kind, Carbon $from, Carbon $to): array
    {
        $spanHours = max(0, $from->diffInHours($to));
        if ($spanHours <= 24) {
            $format = '%Y-%m-%d %H:00';
            $step = 'hour';
        } elseif ($from->diffInDays($to) <= 45) {
            $format = '%Y-%m-%d';
            $step = 'day';
        } else {
            $format = '%Y-%m-01';
            $step = 'month';
        }

        if ($kind === 'revenue') {
            $q = Transaction::where('status', 'success');
            $agg = 'SUM(amount)';
        } elseif ($kind === 'users') {
            $q = User::query();
            $agg = 'COUNT(*)';
        } else {
            $q = Transaction::query();
            $agg = 'COUNT(*)';
        }

        $rows = (clone $q)->whereBetween('created_at', [$from, $to])
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as d, {$agg} as total")
            ->groupBy('d')->orderBy('d')
            ->get()->keyBy(fn ($r) => (string) $r->d);

        $cursor = match ($step) {
            'hour' => $from->copy()->startOfHour(),
            'day' => $from->copy()->startOfDay(),
            'month' => $from->copy()->startOfMonth(),
        };

        $out = [];
        while ($cursor->lte($to)) {
            $key = match ($step) {
                'hour' => $cursor->format('Y-m-d H:00'),
                'day' => $cursor->format('Y-m-d'),
                'month' => $cursor->format('Y-m-01'),
            };
            $label = match ($step) {
                'hour' => $cursor->format('Y-m-d H:00'),
                'day' => $cursor->format('Y-m-d'),
                'month' => $cursor->format('Y-m-d'),
            };
            $out[] = ['date' => $label, 'total' => (float) ($rows[$key]->total ?? 0)];
            $cursor = match ($step) {
                'hour' => $cursor->copy()->addHour(),
                'day' => $cursor->copy()->addDay(),
                'month' => $cursor->copy()->addMonth(),
            };
        }

        if ($kind === 'users') {
            $cum = 0;
            foreach ($out as $i => $o) {
                $cum += $o['total'];
                $out[$i]['total'] = $cum;
            }
        }

        return $out;
    }

    /** Daily revenue for the last N days, zero-filled. */
    private function salesSeries(int $days): array
    {
        $rows = Transaction::where('status', 'success')
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

    /** Monthly revenue for a 1-year view. */
    private function salesSeriesMonthly(int $months): array
    {
        $rows = Transaction::where('status', 'success')
            ->where('created_at', '>=', now()->subMonths($months))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-01') as d, SUM(amount) as total")
            ->groupBy('d')->orderBy('d')
            ->get()->keyBy(fn ($r) => (string) $r->d);

        $out = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $day = now()->subMonths($i)->startOfMonth()->toDateString();
            $out[] = ['date' => $day, 'total' => (float) ($rows[$day]->total ?? 0)];
        }

        return $out;
    }

    /** Top categories by ad count — best-effort join, silently degrades if legacy schema differs. */
    private function categoryBreakdown(?Carbon $from = null, ?Carbon $to = null): array
    {
        try {
            $q = DB::table('product')
                ->leftJoin('catagory_main', 'product.category', '=', 'catagory_main.cat_id');
            if ($from && $to) {
                $q->whereBetween('product.created_at', [$from, $to]);
            }

            return $q->selectRaw('COALESCE(cat_name, "Uncategorised") as name, COUNT(*) as value')
                ->groupBy('cat_name')
                ->orderByDesc('value')
                ->limit(8)
                ->get()
                ->map(fn ($r) => ['name' => (string) $r->name, 'value' => (int) $r->value])
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function topCategories(?Carbon $from = null, ?Carbon $to = null): array
    {
        try {
            $q = DB::table('product')
                ->leftJoin('catagory_main', 'product.category', '=', 'catagory_main.cat_id');
            if ($from && $to) {
                $q->whereBetween('product.created_at', [$from, $to]);
            }

            return $q->selectRaw('COALESCE(cat_name, "Uncategorised") as name, COUNT(*) as count')
                ->groupBy('cat_name')
                ->orderByDesc('count')
                ->limit(6)
                ->get()
                ->map(fn ($r) => ['name' => (string) $r->name, 'count' => (int) $r->count])
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function userGrowthSeries(int $days): array
    {
        $rows = User::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as d, COUNT(*) as total')
            ->groupBy('d')->orderBy('d')
            ->get()->keyBy(fn ($r) => (string) $r->d);

        $out = [];
        $cum = (int) User::where('created_at', '<', now()->subDays($days))->count();
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $cum += (int) ($rows[$day]->total ?? 0);
            $out[] = ['date' => $day, 'total' => $cum];
        }

        return $out;
    }
}
