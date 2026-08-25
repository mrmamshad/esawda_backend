<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Renders the `html.php` page.
 */
class HtmlController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'html.php', 'action' => 'index']);
    }
}
