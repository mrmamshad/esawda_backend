<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $table = 'firebase_device_token';

    public $timestamps = false;

    protected $guarded = [];
}
