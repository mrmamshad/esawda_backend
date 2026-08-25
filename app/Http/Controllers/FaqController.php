<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Renders the `faq.php` page.
 */
class FaqController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'faq.php', 'action' => 'index']);
    }
}
