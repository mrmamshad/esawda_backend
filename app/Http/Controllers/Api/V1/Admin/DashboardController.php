<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Admin dashboard KPIs + trend series for the Next.js /admin panel.
 * Returns:
 *   counts          - headline totals
 *   trend           - % deltas vs previous period (30d)
 *   recent          - latest ads / users / transactions
 *   revenue_series  - date-bucketed sales for 7D/30D/90D/1Y ranges
 *   category_breakdown - donut chart data
 *   top_categories  - top-6 by ad volume
 *   user_growth     - cumulative signup line
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // The dashboard aggregates ~30 queries across 4 tables. Admin-only,
        // recomputed on demand → cache the whole payload for 5 minutes.
        return $this->ok(Cache::remember('admin.dashboard', 300, function () {
            $now       = now();
            $period    = 30;
            $prevStart = $now->copy()->subDays($period * 2)->startOfDay();
            $currStart = $now->copy()->subDays($period)->startOfDay();

            // ── Deltas ─────────────────────────────────────────────────
            $revCurr = (float) Transaction::where('status', 'success')->where('created_at', '>=', $currStart)->sum('amount');
            $revPrev = (float) Transaction::where('status', 'success')->whereBetween('created_at', [$prevStart, $currStart])->sum('amount');
            $txCurr  = (int) Transaction::where('created_at', '>=', $currStart)->count();
            $txPrev  = (int) Transaction::whereBetween('created_at', [$prevStart, $currStart])->count();
            $usrCurr = (int) User::where('created_at', '>=', $currStart)->count();
            $usrPrev = (int) User::whereBetween('created_at', [$prevStart, $currStart])->count();
            $adCurr  = (int) Post::where('created_at', '>=', $currStart)->count();
            $adPrev  = (int) Post::whereBetween('created_at', [$prevStart, $currStart])->count();

            $delta = fn ($curr, $prev) => $prev > 0 ? (($curr - $prev) / $prev) * 100 : ($curr > 0 ? 100 : 0);

            return [
                'counts' => [
                    'users'          => User::count(),
                    'ads_total'      => Post::count(),
                    'ads_active'     => Post::where('status', 'active')->where('hide', '0')->count(),
                    'ads_pending'    => Post::where('status', 'pending')->count(),
                    'ads_expired'    => Post::where('status', 'expire')->count(),
                    'tx_total'       => Transaction::count(),
                    'tx_success'     => Transaction::where('status', 'success')->count(),
                    'revenue_total'  => (float) Transaction::where('status', 'success')->sum('amount'),
                ],
                'trend' => [
                    'users_delta'    => round($delta($usrCurr, $usrPrev), 1),
                    'ads_delta'      => round($delta($adCurr,  $adPrev),  1),
                    'revenue_delta'  => round($delta($revCurr, $revPrev), 1),
                    'tx_delta'       => round($delta($txCurr,  $txPrev),  1),
                ],
                'recent' => [
                    // Column-slimmed — avoid pulling 64KB TEXT description
                    // rows into the dashboard list.
                    'ads'          => Post::orderByDesc('id')->limit(6)->get(['id', 'product_name', 'price', 'status', 'created_at']),
                    'users'        => User::orderByDesc('id')->limit(6)->get(['id', 'name', 'email', 'created_at']),
                    'transactions' => Transaction::orderByDesc('id')->limit(6)->get(['id', 'seller_id', 'amount', 'status', 'created_at']),
                ],
                'revenue_series' => [
                    '7D'  => $this->salesSeries(7),
                    '30D' => $this->salesSeries(30),
                    '90D' => $this->salesSeries(90),
                    '1Y'  => $this->salesSeriesMonthly(12),
                ],
                'category_breakdown' => $this->categoryBreakdown(),
                'top_categories'     => $this->topCategories(),
                'user_growth'        => $this->userGrowthSeries(30),
            ];
        }));
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
    private function categoryBreakdown(): array
    {
        try {
            return DB::table('product')
                ->leftJoin('catagory_main', 'product.category', '=', 'catagory_main.cat_id')
                ->selectRaw('COALESCE(cat_name, "Uncategorised") as name, COUNT(*) as value')
                ->groupBy('cat_name')
                ->orderByDesc('value')
                ->limit(8)
                ->get()
                ->map(fn ($r) => ['name' => (string) $r->name, 'value' => (int) $r->value])
                ->toArray();
        } catch (\Throwable $e) { return []; }
    }

    private function topCategories(): array
    {
        try {
            return DB::table('product')
                ->leftJoin('catagory_main', 'product.category', '=', 'catagory_main.cat_id')
                ->selectRaw('COALESCE(cat_name, "Uncategorised") as name, COUNT(*) as count')
                ->groupBy('cat_name')
                ->orderByDesc('count')
                ->limit(6)
                ->get()
                ->map(fn ($r) => ['name' => (string) $r->name, 'count' => (int) $r->count])
                ->toArray();
        } catch (\Throwable $e) { return []; }
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
