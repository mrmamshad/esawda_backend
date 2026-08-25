<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Post;
use App\Models\Transaction;
use App\Services\AuthService;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/** Legacy `php/dashboard.php`. */
class DashboardController extends Controller
{
    public function __construct(private AuthService $auth, private ThemeRenderer $theme) {}

    public function index(Request $request)
    {
        if (!$this->auth->check($request)) {
            return redirect()->route('auth.login');
        }

        $me = (int) session('user.id');
        $data = [
            'total_active' => Post::where('user_id', $me)->where('status', 'active')->count(),
            'total_pending' => Post::where('user_id', $me)->where('status', 'pending')->count(),
            'total_expire' => Post::where('user_id', $me)->where('status', 'expire')->count(),
            'unread_msgs' => Message::where('to_id', (string) $me)->where('seen', '0')->count(),
            'transactions' => Transaction::where('seller_id', $me)->orderByDesc('id')->limit(5)->get(),
        ];

        try {
            return $this->theme->render('dashboard', $data);
        } catch (\Throwable) {
            return view('placeholder', ['legacy' => 'dashboard.php', 'action' => 'index'] + $data);
        }
    }
}
