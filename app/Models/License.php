<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * A per-buyer license that gates source-code downloads.
 *
 * The connection applies an `ad_` table prefix, so the real table is
 * `ad_licenses` (we keep `$table = 'licenses'` and let the prefix apply).
 */
class License extends Model
{
    protected $table = 'licenses';

    protected $fillable = [
        'key', 'buyer_name', 'buyer_email', 'product_version',
        'status', 'max_downloads', 'downloads_used', 'expires_at',
    ];

    protected $casts = [
        'max_downloads' => 'integer',
        'downloads_used' => 'integer',
        'expires_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'active',
        'downloads_used' => 0,
        'product_version' => 'latest',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(DownloadEvent::class);
    }

    /** Generate a unique, human-readable key like ESW-9F3A-2B7C-4D18. */
    public static function generateKey(): string
    {
        do {
            $block = fn () => strtoupper(Str::random(4));
            $key = 'ESW-'.$block().'-'.$block().'-'.$block();
        } while (static::where('key', $key)->exists());

        return $key;
    }

    /** Active, not expired. */
    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    /** Valid AND still has download quota remaining. */
    public function canDownload(): bool
    {
        return $this->isValid() && $this->downloads_used < $this->max_downloads;
    }

    public function downloadsRemaining(): int
    {
        return max(0, $this->max_downloads - $this->downloads_used);
    }
}
