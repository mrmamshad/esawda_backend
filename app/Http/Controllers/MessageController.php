<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Services\AuthService;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/** Legacy `php/message.php` — user inbox. */
class MessageController extends Controller
{
    public function __construct(private AuthService $auth, private ThemeRenderer $theme) {}

    public function index(Request $request)
    {
        if (! $this->auth->check($request)) return redirect()->route('auth.login');

        $me = (int) session('user.id');

        // Send new message
        if ($request->isMethod('post') && $request->filled('message_content')) {
            $data = $request->validate([
                'to_id'           => 'required|integer',
                'message_content' => 'required|string|max:5000',
                'post_id'         => 'nullable|integer',
            ]);
            Message::create([
                'from_id'         => (string) $me,
                'to_id'           => (string) $data['to_id'],
                'from_uname'      => session('user.username'),
                'message_content' => $data['message_content'],
                'message_date'    => now(),
                'post_id'         => $data['post_id'] ?? null,
                'recd'            => 0,
                'seen'            => '0',
            ]);
            session()->flash('flash_success', 'Message sent.');
            return back();
        }

        $inbox = Message::where('to_id', (string) $me)->orderByDesc('message_date')->limit(50)->get();
        $sent  = Message::where('from_id', (string) $me)->orderByDesc('message_date')->limit(50)->get();

        return app(\App\Services\ThemeRenderer::class)->render('message', ['inbox' => $inbox, 'sent' => $sent]);
    }
}
