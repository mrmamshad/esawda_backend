<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailQueue extends Model
{
    protected $table = 'emailq';
    protected $primaryKey = 'q_id';
    public $timestamps = false;
    protected $guarded = [];
}
