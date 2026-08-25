<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Renders the `webhook.php` page.
 */
class WebhookController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'webhook.php', 'action' => 'index']);
    }
}
