<?php

namespace App\Http\Resources\V1;

/**
 * One row in the left "Messages" sidebar of the chat page:
 *   - counterpart avatar + name + Online dot
 *   - last message preview + timestamp
 *   - unread count
 *
 * The controller builds this shape via aggregation; here we only
 * pass the assembled array through. Kept as a resource so the
 * response envelope stays uniform.
 */
class ThreadResource extends BaseResource
{
    public function toArray($request): array
    {
        $avatarBase = rtrim(config('app.url'), '/') . '/storage/profile/';
        return [
            'id'            => $this->id,                       // "1-2" canonical key
            'counterpart'   => [
                'id'         => (int) $this->counterpart_id,
                'username'   => $this->counterpart_username,
                'name'       => $this->counterpart_name ?: $this->counterpart_username,
                'phone'      => $this->counterpart_phone,
                'avatar_url' => $avatarBase . ($this->counterpart_image ?: 'default_user.png'),
                'online'     => $this->bool($this->counterpart_online),
            ],
            'last_message'  => [
                'body'   => $this->last_type === 'image' ? '📷 Photo' : $this->last_body,
                'type'   => $this->last_type ?: 'text',
                'mine'   => (bool) $this->last_mine,
                'sent_at'=> $this->last_sent_at,
            ],
            'unread_count'  => (int) $this->unread_count,
            'post_id'       => $this->post_id ? (int) $this->post_id : null,
        ];
    }
}
