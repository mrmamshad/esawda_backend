<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Renders the `testimonials.php` page.
 */
class TestimonialsController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'testimonials.php', 'action' => 'index']);
    }
}
