<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Renders the `blog-author.php` page.
 */
class BlogAuthorController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'blog-author.php', 'action' => 'index']);
    }
}
