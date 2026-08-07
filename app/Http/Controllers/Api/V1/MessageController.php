<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\SendMessageRequest;
use App\Http\Resources\V1\MessageResource;
use App\Http\Resources\V1\ThreadResource;
use App\Models\Message;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Chat endpoints powering the 4th reference page.
 *
 *   GET  /api/v1/me/threads                    left sidebar list
 *   GET  /api/v1/me/threads/{userId}           messages with one user
 *   POST /api/v1/messages                      send a message
 *   POST /api/v1/me/threads/{userId}/read      mark thread as read
 *   GET  /api/v1/me/threads/unread-count       badge count
 *
 * The legacy schema stores each direction as a separate row (from_id,
 * to_id, message_date, seen). We synthesize a "thread" by canonical
 * min/max of the two user ids.
 */
class MessageController extends Controller
{
    public function threads(Request $request)
    {
        $me = (int) $request->user()->id;

        // Fetch every message where the current user is either side, order desc
        // so `first()` per group gives us the latest.
        $rows = Message::query()
            ->where(function ($q) use ($me) {
                $q->where('from_id', $me)->orWhere('to_id', $me);
            })
            ->orderByDesc('message_date')
            ->orderByDesc('message_id')
            // Sidebar only shows a bounded set of recent threads; fetching
            // the entire conversation history (unbounded OR scan) just to
            // group the latest is wasteful as a mailbox grows. Cap to the
            // most recent rows; grouping still yields latest-per-thread.
            ->limit(300)
            ->get();

        // Group by canonical thread key.
        $grouped = $rows->groupBy(fn (Message $m) => $this->threadKey($me, $m));

        // Preload counterpart user records in one query.
        $counterpartIds = $grouped->keys()->map(fn ($k) => (int) explode('-', $k)[1])->unique();
        $users = User::whereIn('id', $counterpartIds)->get()->keyBy('id');

        $threads = $grouped->map(function (Collection $msgs, string $key) use ($me, $users) {
            $last  = $msgs->first();
            $other = (int) $this->otherId($key, $me);
            $u     = $users->get($other);
            $unread = $msgs->filter(fn ($m) => (int) $m->to_id === $me && ! $this->bool($m->seen))->count();

            return (object) [
                'id'                     => $key,
                'counterpart_id'         => $other,
                'counterpart_username'   => $u?->username,
                'counterpart_name'       => $u?->name,
                'counterpart_image'      => $u?->image,
                'counterpart_online'     => $u?->online,
                'last_body'              => $last->message_content,
                'last_type'              => $last->message_type,
                'last_mine'              => (int) $last->from_id === $me,
                'last_sent_at'           => $last->message_date instanceof \DateTimeInterface
                                            ? $last->message_date->format('c')
                                            : $last->message_date,
                'unread_count'           => $unread,
                'post_id'                => $last->post_id,
            ];
        })->values();

        return $this->ok(ThreadResource::collection($threads));
    }

    public function thread(int $userId, Request $request)
    {
        $me = (int) $request->user()->id;

        // Ensure the counterpart exists (404 otherwise) so the URL stays REST-y.
        User::findOrFail($userId);

        $perPage = max(1, min(100, (int) $request->query('per_page', 30)));

        $q = Message::query()
            ->with('sender')
            ->where(function ($sub) use ($me, $userId) {
                $sub->where(function ($s) use ($me, $userId) {
                    $s->where('from_id', $me)->where('to_id', $userId);
                })->orWhere(function ($s) use ($me, $userId) {
                    $s->where('from_id', $userId)->where('to_id', $me);
                });
            })
            ->orderBy('message_date')
            ->orderBy('message_id');

        return $this->ok(MessageResource::collection($q->paginate($perPage)));
    }

    public function send(SendMessageRequest $request)
    {
        $me   = $request->user();
        $data = $request->validated();

        if ((int) $data['to'] === (int) $me->id) {
            return $this->error('SELF_MESSAGE', 'You cannot message yourself.', 422);
        }

        $to = User::findOrFail((int) $data['to']);

        $msg = Message::create([
            'from_id'         => (string) $me->id,
            'to_id'           => (string) $to->id,
            'from_uname'      => $me->username,
            'to_uname'        => $to->username,
            'message_content' => $data['body'],
            'message_date'    => now(),
            'message_type'    => $data['type']    ?? 'text',
            'post_id'         => $data['post_id'] ?? 0,
            'recd'            => 0,
            'seen'            => '0',
        ]);

        $msg->load('sender');
        return $this->created(new MessageResource($msg));
    }

    public function markRead(int $userId, Request $request)
    {
        $me = (int) $request->user()->id;
        User::findOrFail($userId);

        $count = Message::where('to_id', $me)
                        ->where('from_id', $userId)
                        ->where('seen', '0')
                        ->update(['seen' => '1']);

        return $this->ok(['marked_read' => $count]);
    }

    public function unreadCount(Request $request)
    {
        $count = Message::where('to_id', (int) $request->user()->id)
                        ->where('seen', '0')
                        ->count();
        return $this->ok(['unread_count' => (int) $count]);
    }

    /* --------------------------------------------------------------- */

    private function threadKey(int $me, Message $m): string
    {
        $a = (int) $m->from_id;
        $b = (int) $m->to_id;
        return $a < $b ? "{$a}-{$b}" : "{$b}-{$a}";
    }

    private function otherId(string $key, int $me): int
    {
        [$a, $b] = array_map('intval', explode('-', $key));
        return $a === $me ? $b : $a;
    }

    private function bool($v): bool
    {
        return in_array($v, [1, '1', true, 'true'], true);
    }
}
