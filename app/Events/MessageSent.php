<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast when a message is sent between users.
 * Both sender and receiver subscribe to their own private channels.
 */
class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Broadcast to both sender and receiver private channels
        return [
            new PrivateChannel("user.{$this->message->from_id}"),
            new PrivateChannel("user.{$this->message->to_id}"),
        ];
    }

    /**
     * The event name to broadcast as.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     *
     * Field names mirror MessageResource so the frontend can append the
     * payload straight into its Message[] state. NOTE: the legacy table
     * stores the text in `message_content` with the clock in `message_date`
     * (there is no `body`/`created_at` column) — the old mapping emitted
     * nulls, so live bubbles arrived empty.
     */
    public function broadcastWith(): array
    {
        $m = $this->message;

        return [
            'id' => (int) $m->message_id,
            'from_id' => (int) $m->from_id,
            'to_id' => (int) $m->to_id,
            'body' => $m->message_content,
            'type' => $m->message_type ?: 'text',
            'image_url' => $m->message_type === 'image'
                ? rtrim(config('app.url'), '/').'/storage/'.$m->message_content
                : null,
            'post_id' => $m->post_id ? (int) $m->post_id : null,
            'sent_at' => $m->message_date instanceof \DateTimeInterface
                ? $m->message_date->toIso8601String()
                : (is_string($m->message_date) ? $m->message_date : now()->toIso8601String()),
        ];
    }
}
