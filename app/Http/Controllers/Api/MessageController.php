<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function inbox(Request $request)
    {
        return response()->json(
            Message::where('to_id', (string) $request->user()->id)
                   ->orderByDesc('message_date')->paginate(30)
        );
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'to_id'           => 'required|integer',
            'message_content' => 'required|string|max:5000',
            'post_id'         => 'nullable|integer',
        ]);
        $me = $request->user();
        $msg = Message::create([
            'from_id'         => (string) $me->id,
            'to_id'           => (string) $data['to_id'],
            'from_uname'      => $me->username,
            'message_content' => $data['message_content'],
            'message_date'    => now(),
            'post_id'         => $data['post_id'] ?? null,
            'recd'            => 0,
            'seen'            => '0',
        ]);
        return response()->json(['message' => $msg], 201);
    }
}
