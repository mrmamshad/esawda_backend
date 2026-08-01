<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Auto-generated stub for legacy `blog.php`.
 * Business logic to be ported during Phase 4+.
 */
class BlogController extends Controller
{
    /** Ported from legacy `php/blog.php` (index). Reimplement inside services then render Blade view. */
    public function index(Request $request)
    {
        // TODO(migration): move legacy logic from ROOTPATH/php/blog.php here.
        return view('placeholder', ['legacy' => 'blog.php', 'action' => 'index']);
    }

}
