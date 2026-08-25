<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomOption extends Model
{
    protected $table = 'custom_options';

    protected $primaryKey = 'option_id';

    public $timestamps = false;

    protected $guarded = [];
}
