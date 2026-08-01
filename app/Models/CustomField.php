<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $table = 'custom_fields';
    protected $primaryKey = 'custom_id';
    public $timestamps = false;
    protected $guarded = [];
}
