<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Str;

/**
 * Consolidates logic that used to live in `includes/functions/func.global.php`
 * for creating, updating, expiring and moderating classified ads.
 *
 * Phase 5 will fill these methods with logic ported from the legacy
 * `php/ad-post.php`, `php/ad-edit.php`, and `admin/post_*.php` files.
 */
class AdService
{
    public function createFromRequest(array $data, int $userId): Post
    {
        // TODO(migration): port from php/ad-post.php
        $data['user_id'] = $userId;
        $data['slug']    = $data['slug'] ?? Str::slug($data['product_name'] ?? '');
        $data['status']  = $data['status'] ?? 'pending';

        return Post::create($data);
    }

    public function expireDueAds(): int
    {
        // TODO(migration): port cron logic from admin/cron_logs.php + post_expire.php
        return Post::where('expire_date', '<', time())
                   ->where('status', 'active')
                   ->update(['status' => 'expire']);
    }
}
