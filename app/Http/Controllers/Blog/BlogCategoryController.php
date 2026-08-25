<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Renders the `blog-category.php` page.
 */
class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        return view('placeholder', ['legacy' => 'blog-category.php', 'action' => 'index']);
    }
}
