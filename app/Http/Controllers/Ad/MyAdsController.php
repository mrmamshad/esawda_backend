<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\AuthService;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

class MyAdsController extends Controller
{
    public function __construct(private AuthService $auth, private ThemeRenderer $theme) {}

    public function index(Request $request)
    {
        if (! $this->auth->check($request)) return redirect()->route('auth.login');

        $userId = (int) session('user.id');

        // Handle delete via POST (_action=delete, post_id=N)
        if ($request->isMethod('post') && $request->input('_action') === 'delete') {
            $id = (int) $request->input('post_id');
            $deleted = Post::where('id', $id)->where('user_id', $userId)->delete();
            session()->flash('flash_success', $deleted ? 'Ad deleted.' : 'Ad not found or not yours.');
            return back();
        }

        // Show all statuses (not just active) so users see their pending ads
        $posts = Post::where('user_id', $userId)
                     ->orderByDesc('id')
                     ->paginate(20);

        return app(\App\Services\ThemeRenderer::class)->render('ad-my', ['posts' => $posts]);
    }
}
