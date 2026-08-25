<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Renders the `advertise.php` page.
 */
class AdvertiseController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'advertise.php', 'action' => 'index']);
    }
}
