<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\AuthService;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/** Legacy `php/ad-edit.php`. */
class EditController extends Controller
{
    public function __construct(private AuthService $auth, private ThemeRenderer $theme) {}

    public function index(Request $request, ?string $id = null)
    {
        if (! $this->auth->check($request)) return redirect()->route('auth.login');
        if (! $id) abort(404);

        $post = Post::where('id', $id)->where('user_id', session('user.id'))->firstOrFail();

        if ($request->isMethod('post') && $request->filled('submit')) {
            $post->fill($request->only([
                'product_name','description','category','sub_category',
                'price','phone','city','state','country','tag','negotiable','hide_phone',
            ]));
            $post->updated_at = now();
            $post->status = 'pending'; // legacy: re-approval required on edit
            $post->save();
            session()->flash('flash_success', 'Ad updated — pending re-approval.');
            return redirect()->route('ad.mine');
        }

        try {
            return $this->theme->render('ad-edit', ['post' => $post]);
        } catch (\Throwable) {
            return view('placeholder', ['legacy' => 'ad-edit.php', 'action' => "id=$id"]);
        }
    }
}
