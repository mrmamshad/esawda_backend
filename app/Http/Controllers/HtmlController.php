<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Auto-generated stub for legacy `html.php`.
 * Business logic to be ported during Phase 4+.
 */
class HtmlController extends Controller
{
    /** Ported from legacy `php/html.php` (index). Reimplement inside services then render Blade view. */
    public function index(Request $request)
    {
        // TODO(migration): move legacy logic from ROOTPATH/php/html.php here.
        return view('placeholder', ['legacy' => 'html.php', 'action' => 'index']);
    }

}
