<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Auto-generated stub for legacy `invoice.php`.
 * Business logic to be ported during Phase 4+.
 */
class InvoiceController extends Controller
{
    /** Ported from legacy `php/invoice.php` (index). Reimplement inside services then render Blade view. */
    public function index(Request $request)
    {
        // TODO(migration): move legacy logic from ROOTPATH/php/invoice.php here.
        return view('placeholder', ['legacy' => 'invoice.php', 'action' => 'index']);
    }

}
