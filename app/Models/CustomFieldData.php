<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldData extends Model
{
    protected $table = 'custom_data';

    public $timestamps = false;

    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class, 'product_id');
    }

    public function field()
    {
        return $this->belongsTo(CustomField::class, 'field_id', 'custom_id');
    }
}
