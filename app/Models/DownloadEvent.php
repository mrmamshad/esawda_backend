<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit record for each successful (attempted) source download.
 * Real table: `ad_download_events` (via the connection's `ad_` prefix).
 */
class DownloadEvent extends Model
{
    protected $table = 'download_events';

    public $timestamps = false;

    protected $fillable = ['license_id', 'ip', 'user_agent', 'downloaded_at'];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }
}
