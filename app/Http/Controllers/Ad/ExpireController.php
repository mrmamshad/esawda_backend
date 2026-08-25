<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Renders the `ad-expire.php` page.
 */
class ExpireController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'ad-expire.php', 'action' => 'index']);
    }
}
