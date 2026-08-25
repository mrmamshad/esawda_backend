<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    protected $table = 'time_zones';

    public $timestamps = false;

    protected $guarded = [];
}
