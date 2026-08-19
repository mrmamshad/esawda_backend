<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MessageBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_message_sent_event_broadcasts(): void
    {
        Event::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $response = $this->actingAs($sender)->postJson('/api/v1/messages', [
            'to' => $receiver->id,
            'body' => 'Hello via WebSocket test',
        ]);

        $response->assertCreated();

        Event::assertDispatched(MessageSent::class, function ($event) use ($sender, $receiver) {
            return $event->message->from_id === (string) $sender->id &&
                   $event->message->to_id === (string) $receiver->id &&
                   $event->message->message_content === 'Hello via WebSocket test';
        });
    }

    public function test_message_broadcasts_to_correct_channels(): void
    {
        Event::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $this->actingAs($sender)->postJson('/api/v1/messages', [
            'to' => $receiver->id,
            'body' => 'Channel test',
        ]);

        Event::assertDispatched(MessageSent::class, function ($event) use ($sender, $receiver) {
            $channels = $event->broadcastOn();

            // Should broadcast to both sender and receiver private channels
            $channelNames = array_map(fn($ch) => $ch->name, $channels);

            return in_array("private-user.{$sender->id}", $channelNames) &&
                   in_array("private-user.{$receiver->id}", $channelNames);
        });
    }

    public function test_cannot_send_message_to_self(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/messages', [
            'to' => $user->id,
            'body' => 'Self message attempt',
        ]);

        $response->assertStatus(422)
                 ->assertJson(['error' => ['code' => 'SELF_MESSAGE']]);

        Event::assertNotDispatched(MessageSent::class);
    }

    public function test_message_includes_correct_broadcast_data(): void
    {
        Event::fake();

        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $this->actingAs($sender)->postJson('/api/v1/messages', [
            'to' => $receiver->id,
            'body' => 'Data structure test',
            'post_id' => 123,
        ]);

        Event::assertDispatched(MessageSent::class, function ($event) use ($sender, $receiver) {
            $broadcastData = $event->broadcastWith();

            return isset($broadcastData['id']) &&
                   $broadcastData['from_id'] === (string) $sender->id &&
                   $broadcastData['to_id'] === (string) $receiver->id &&
                   $broadcastData['body'] === 'Data structure test' &&
                   $broadcastData['post_id'] === 123 &&
                   isset($broadcastData['created_at']);
        });
    }
}
