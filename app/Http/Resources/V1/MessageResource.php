<?php

namespace App\Http\Resources\V1;

/**
 * One chat message. `mine=true` when the current user sent it — used
 * by the frontend to pick the green (out) vs gray (in) bubble style.
 */
class MessageResource extends BaseResource
{
    public function toArray($request): array
    {
        $me = optional($request->user())->id;
        $avatarBase = rtrim(config('app.url'), '/') . '/storage/profile/';
        $sender = $this->relationLoaded('sender') ? $this->getRelation('sender') : null;

        return [
            'id'         => (int) $this->message_id,
            'thread_id'  => $this->thread_id_for($me),
            'from_id'    => (int) $this->from_id,
            'to_id'      => (int) $this->to_id,
            'from_name'  => $this->from_uname,
            'to_name'    => $this->to_uname,
            'body'       => $this->message_content,
            'type'       => $this->message_type ?: 'text',   // text | image | offer | payment_request
            'image_url'  => $this->message_type === 'image'
                            ? rtrim(config('app.url'), '/') . '/storage/' . $this->message_content
                            : null,
            'post_id'    => $this->post_id ? (int) $this->post_id : null,
            'seen'       => $this->bool($this->seen),
            'mine'       => $me !== null && (int) $this->from_id === (int) $me,
            'sender'     => $sender ? [
                'id'         => (int) $sender->id,
                'username'   => $sender->username,
                'name'       => $sender->name ?: $sender->username,
                'avatar_url' => $avatarBase . ($sender->image ?: 'default_user.png'),
                'online'     => $this->bool($sender->online),
            ] : null,
            'sent_at'    => optional($this->message_date)
                                ? (is_string($this->message_date) ? $this->message_date : $this->message_date->toIso8601String())
                                : null,
        ];
    }

    private function thread_id_for(?int $me): string
    {
        // Canonical thread key = sorted "min-max" of the two user ids.
        $a = (int) $this->from_id;
        $b = (int) $this->to_id;
        return $a < $b ? "{$a}-{$b}" : "{$b}-{$a}";
    }
}
