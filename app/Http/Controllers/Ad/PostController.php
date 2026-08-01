<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\AdService;
use App\Services\AuthService;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/**
 * Legacy `php/ad-post.php`. Requires login. Presents the post-ad form
 * and, on POST, creates a new Post + custom-field data + uploads.
 */
class PostController extends Controller
{
    public function __construct(
        private AdService $ads,
        private AuthService $auth,
        private ThemeRenderer $theme,
    ) {}

    public function index(Request $request)
    {
        if (! $this->auth->check($request)) {
            return redirect()->route('auth.login');
        }

        if ($request->isMethod('post') && $request->filled('submit')) {
            $data = $request->validate([
                'product_name' => 'required|string|max:150',
                'description'  => 'required|string',
                'category'     => 'required|integer',
                'sub_category' => 'nullable|integer',
                'price'        => 'nullable|integer|min:0',
                'phone'        => 'nullable|string|max:50',
                'city'         => 'nullable|string|max:50',
                'state'        => 'nullable|string|max:50',
                'country'      => 'nullable|string|max:50',
                'tag'          => 'nullable|string|max:225',
                'image'        => 'nullable|image|max:5120',
            ]);

            // Handle image upload
            $screenshot = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/products'), $filename);
                $screenshot = json_encode([$filename]);
            }

            $userId = session('user.id');
            $data['screen_shot'] = $screenshot;
            $post = $this->ads->createFromRequest($data, (int) $userId);
            session()->flash('flash_success', 'Your ad #' . $post->id . ' has been submitted for review.');
            return redirect()->route('ad.mine');
        }

        return app(\App\Services\ThemeRenderer::class)->render('ad-post', [
            'categories' => Category::orderBy('cat_order')->get(),
        ]);
    }
}
