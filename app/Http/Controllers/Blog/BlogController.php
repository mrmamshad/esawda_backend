<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Renders the `blog.php` page.
 */
class BlogController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'blog.php', 'action' => 'index']);
    }
}
