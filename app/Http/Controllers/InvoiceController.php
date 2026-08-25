<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Renders the `invoice.php` page.
 */
class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'invoice.php', 'action' => 'index']);
    }
}
