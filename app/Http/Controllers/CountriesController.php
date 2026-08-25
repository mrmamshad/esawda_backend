<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Renders the `countries.php` page.
 */
class CountriesController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'countries.php', 'action' => 'index']);
    }
}
