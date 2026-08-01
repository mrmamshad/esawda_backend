<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'logs';
    protected $primaryKey = 'log_id';
    public $timestamps = false;
    protected $guarded = [];
}
