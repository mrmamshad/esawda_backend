<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Legacy table: subadmin2 — 2nd-level admin division (county/district). */
class District extends Model
{
    protected $table = 'subadmin2';

    public $timestamps = false;

    protected $guarded = [];
}
