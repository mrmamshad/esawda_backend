<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Auto-generated stub for legacy `contact.php`.
 * Business logic to be ported during Phase 4+.
 */
class ContactController extends Controller
{
    /** Ported from legacy `php/contact.php` (index). Reimplement inside services then render Blade view. */
    public function index(Request $request)
    {
        // TODO(migration): move legacy logic from ROOTPATH/php/contact.php here.
        return view('placeholder', ['legacy' => 'contact.php', 'action' => 'index']);
    }

}
