<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOption extends Model
{
    protected $table = 'user_options';
    public $timestamps = false;
    protected $guarded = [];
}
