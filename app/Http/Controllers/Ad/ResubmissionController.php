<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Renders the `ad-resubmission.php` page.
 */
class ResubmissionController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'ad-resubmission.php', 'action' => 'index']);
    }
}
