<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Renders the `blog-single.php` page.
 */
class BlogSingleController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'blog-single.php', 'action' => 'index']);
    }
}
