<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Services\AuthService;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/** Legacy: php/ad-favourite.php. */
class FavouriteController extends Controller
{
    public function __construct(private AuthService $auth, private ThemeRenderer $theme) {}

    public function index(Request $request)
    {
        if (! $this->auth->check($request)) return redirect()->route('auth.login');

        $userId = (int) session('user.id');

        // POST: add / remove favourite
        if ($request->isMethod('post')) {
            $action = $request->input('_action');
            if ($action === 'add') {
                $pid = (int) $request->input('product_id');
                Favourite::firstOrCreate(['user_id' => $userId, 'product_id' => $pid]);
                session()->flash('flash_success', 'Added to favourites.');
            } elseif ($action === 'remove') {
                $fid = (int) $request->input('favourite_id');
                Favourite::where('id', $fid)->where('user_id', $userId)->delete();
                session()->flash('flash_success', 'Removed from favourites.');
            }
            return back();
        }

        $favourites = Favourite::with('post')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();

        return $this->theme->render('ad-favourite', ['favourites' => $favourites]);
    }
}
