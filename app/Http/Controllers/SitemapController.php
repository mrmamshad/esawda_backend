<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Auto-generated stub for legacy `sitemap.php`.
 * Business logic to be ported during Phase 4+.
 */
class SitemapController extends Controller
{
    /** Ported from legacy `php/sitemap.php` (index). Reimplement inside services then render Blade view. */
    public function index(Request $request)
    {
        // TODO(migration): move legacy logic from ROOTPATH/php/sitemap.php here.
        return view('placeholder', ['legacy' => 'sitemap.php', 'action' => 'index']);
    }

}
