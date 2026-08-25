<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Renders the `sitemap.php` page.
 */
class SitemapController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'sitemap.php', 'action' => 'index']);
    }
}
