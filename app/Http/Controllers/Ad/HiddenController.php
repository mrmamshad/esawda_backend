<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Renders the `ad-hidden.php` page.
 */
class HiddenController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'ad-hidden.php', 'action' => 'index']);
    }
}
