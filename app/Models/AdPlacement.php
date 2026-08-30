<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * An image-based ad for one placement slot (e.g. "home.after_categories").
 * Admins upload an image, optionally a click-through link, and schedule it
 * with start/expiry dates so campaigns rotate automatically.
 */
class AdPlacement extends Model
{
    protected $table = 'ad_placements';

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /** Active + currently within its scheduled window. */
    public function scopeLive(Builder $q): Builder
    {
        return $q->where('status', true)
            ->where(fn ($inner) => $inner
                ->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($inner) => $inner
                ->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        return preg_match('~^https?://~i', $this->image_path)
            ? $this->image_path
            : rtrim(config('app.url'), '/').'/storage/ads/'.ltrim($this->image_path, '/');
    }
}
