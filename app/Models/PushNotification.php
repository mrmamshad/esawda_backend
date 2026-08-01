<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    protected $table = 'push_notification';
    public $timestamps = false;
    protected $guarded = [];
}
