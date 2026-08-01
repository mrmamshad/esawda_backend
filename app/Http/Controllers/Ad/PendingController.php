<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Auto-generated stub for legacy `ad-pending.php`.
 * Business logic to be ported during Phase 4+.
 */
class PendingController extends Controller
{
    /** Ported from legacy `php/ad-pending.php` (index). Reimplement inside services then render Blade view. */
    public function index(Request $request)
    {
        // TODO(migration): move legacy logic from ROOTPATH/php/ad-pending.php here.
        return view('placeholder', ['legacy' => 'ad-pending.php', 'action' => 'index']);
    }

}
