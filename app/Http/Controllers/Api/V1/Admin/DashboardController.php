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

            // Aggregate both windows (current + previous) in a single pass per
            // table using conditional SUM(CASE …). This collapses what used to
            // be 12 separate COUNT/SUM round-trips into 3 grouped queries — a
            // meaningful win on the admin dashboard's cold/refresh path.
            $inCurr = 'created_at BETWEEN ? AND ?';
            $inPrev = 'created_at BETWEEN ? AND ?';
            $winBinds = [$currStart, $currEnd, $prevStart, $prevEnd];

            // Posts: all five status buckets for the current window + total for
            // the previous window (for the ads trend), in one query.
            $post = Post::query()->selectRaw(
                "SUM(CASE WHEN {$inCurr} THEN 1 ELSE 0 END) AS ads_total,
                 SUM(CASE WHEN {$inCurr} AND status = 'active' AND hide = '0' THEN 1 ELSE 0 END) AS ads_active,
                 SUM(CASE WHEN {$inCurr} AND status = 'pending' THEN 1 ELSE 0 END) AS ads_pending,
                 SUM(CASE WHEN {$inCurr} AND status = 'expire' THEN 1 ELSE 0 END) AS ads_expired,
                 SUM(CASE WHEN {$inPrev} THEN 1 ELSE 0 END) AS ads_prev",
                [$currStart, $currEnd, $currStart, $currEnd, $currStart, $currEnd, $currStart, $currEnd, $prevStart, $prevEnd]
            )->first();

            // Transactions: count + success count + revenue for the current
            // window, plus prev-window count + revenue for trends, in one query.
            $tx = Transaction::query()->selectRaw(
                "SUM(CASE WHEN {$inCurr} THEN 1 ELSE 0 END) AS tx_total,
                 SUM(CASE WHEN {$inCurr} AND status = 'success' THEN 1 ELSE 0 END) AS tx_success,
                 SUM(CASE WHEN {$inCurr} AND status = 'success' THEN amount ELSE 0 END) AS revenue_total,
                 SUM(CASE WHEN {$inPrev} THEN 1 ELSE 0 END) AS tx_prev,
                 SUM(CASE WHEN {$inPrev} AND status = 'success' THEN amount ELSE 0 END) AS revenue_prev",
                [$currStart, $currEnd, $currStart, $currEnd, $currStart, $currEnd, $prevStart, $prevEnd, $prevStart, $prevEnd]
            )->first();

            // Users: current + previous window counts in one query.
            $usr = User::query()->selectRaw(
                "SUM(CASE WHEN {$inCurr} THEN 1 ELSE 0 END) AS users_curr,
                 SUM(CASE WHEN {$inPrev} THEN 1 ELSE 0 END) AS users_prev",
                $winBinds
            )->first();

            $counts = [
                'users' => (int) ($usr->users_curr ?? 0),
                'ads_total' => (int) ($post->ads_total ?? 0),
                'ads_active' => (int) ($post->ads_active ?? 0),
                'ads_pending' => (int) ($post->ads_pending ?? 0),
                'ads_expired' => (int) ($post->ads_expired ?? 0),
                'tx_total' => (int) ($tx->tx_total ?? 0),
                'tx_success' => (int) ($tx->tx_success ?? 0),
                'revenue_total' => (float) ($tx->revenue_total ?? 0),
            ];

            $trend = [
                'users_delta' => round($delta($counts['users'], (int) ($usr->users_prev ?? 0)), 1),
                'ads_delta' => round($delta($counts['ads_total'], (int) ($post->ads_prev ?? 0)), 1),
                'revenue_delta' => round($delta($counts['revenue_total'], (float) ($tx->revenue_prev ?? 0)), 1),
                'tx_delta' => round($delta($counts['tx_total'], (int) ($tx->tx_prev ?? 0)), 1),
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
                // Rolling last 7 days (today + previous 6 days). Avoids the
                // "week resets on Monday" surprise and keeps the vs-last-period
                // comparison an equal 7-day window. Matches how most analytics
                // dashboards (GA, Stripe, etc.) present "this week".
                return [$now->copy()->subDays(6)->startOfDay(), $now->copy()];
            case 'month':
                // Rolling last 30 days, for the same consistency reasons.
                return [$now->copy()->subDays(29)->startOfDay(), $now->copy()];
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
