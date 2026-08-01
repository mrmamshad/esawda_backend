<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upgrade extends Model
{
    protected $table = 'upgrades';
    protected $primaryKey = 'upgrade_id';
    public $timestamps = false;
    protected $guarded = [];
}
