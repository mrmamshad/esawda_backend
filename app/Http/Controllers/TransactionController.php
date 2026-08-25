<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Renders the `transaction.php` page.
 */
class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'transaction.php', 'action' => 'index']);
    }
}
