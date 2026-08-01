<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Auto-generated stub for legacy `ad-hidden.php`.
 * Business logic to be ported during Phase 4+.
 */
class HiddenController extends Controller
{
    /** Ported from legacy `php/ad-hidden.php` (index). Reimplement inside services then render Blade view. */
    public function index(Request $request)
    {
        // TODO(migration): move legacy logic from ROOTPATH/php/ad-hidden.php here.
        return view('placeholder', ['legacy' => 'ad-hidden.php', 'action' => 'index']);
    }

}
