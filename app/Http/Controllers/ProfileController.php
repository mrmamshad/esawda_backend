<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/** Legacy `php/profile.php` — public user profile page + their ads. */
class ProfileController extends Controller
{
    public function __construct(private ThemeRenderer $theme) {}

    public function index(Request $request, ?string $username = null)
    {
        if (! $username) abort(404);

        $user = User::where('username', $username)->firstOrFail();
        // legacy: bump profile view
        $user->increment('view');

        $posts = Post::where('user_id', $user->id)->active()->orderByDesc('id')->paginate(12);

        try { return $this->theme->render('profile', ['user' => $user, 'posts' => $posts]); }
        catch (\Throwable) { return view('placeholder', ['legacy' => 'profile.php', 'action' => $username]); }
    }
}
