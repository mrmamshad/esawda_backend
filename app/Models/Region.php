<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Legacy table: subadmin1 — 1st-level admin division (state/province). */
class Region extends Model
{
    protected $table = 'subadmin1';

    public $timestamps = false;

    protected $guarded = [];
}
