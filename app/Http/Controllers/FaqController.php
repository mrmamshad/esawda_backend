<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Auto-generated stub for legacy `faq.php`.
 * Business logic to be ported during Phase 4+.
 */
class FaqController extends Controller
{
    /** Ported from legacy `php/faq.php` (index). Reimplement inside services then render Blade view. */
    public function index(Request $request)
    {
        // TODO(migration): move legacy logic from ROOTPATH/php/faq.php here.
        return view('placeholder', ['legacy' => 'faq.php', 'action' => 'index']);
    }

}
