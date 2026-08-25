<?php

namespace App\Models;

use App\Jobs\SendMailJob;
use Illuminate\Database\Eloquent\Model;

class EmailQueue extends Model
{
    protected $table = 'emailq';

    protected $primaryKey = 'q_id';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * Dispatch a plain-text email. Replaces the legacy dead emailq table
     * insert (rows were written but never sent — no worker existed). With
     * QUEUE_CONNECTION=sync (dev) it sends inline; with database (prod) it
     * defers to a queue worker.
     */
    public static function enqueue(string $email, string $toname, string $subject, string $body): void
    {
        SendMailJob::dispatch($email, $toname, $subject, $body);
    }
}
