<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Renders the `contact.php` page.
 */
class ContactController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'contact.php', 'action' => 'index']);
    }
}
