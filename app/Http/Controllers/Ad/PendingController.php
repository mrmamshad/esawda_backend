<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Renders the `ad-pending.php` page.
 */
class PendingController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'ad-pending.php', 'action' => 'index']);
    }
}
