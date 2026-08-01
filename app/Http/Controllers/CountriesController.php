<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Auto-generated stub for legacy `countries.php`.
 * Business logic to be ported during Phase 4+.
 */
class CountriesController extends Controller
{
    /** Ported from legacy `php/countries.php` (index). Reimplement inside services then render Blade view. */
    public function index(Request $request)
    {
        // TODO(migration): move legacy logic from ROOTPATH/php/countries.php here.
        return view('placeholder', ['legacy' => 'countries.php', 'action' => 'index']);
    }

}
