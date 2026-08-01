<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
    public $timestamps = false;
    protected $guarded = [];

    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }
    public function post()   { return $this->belongsTo(Post::class, 'product_id'); }
}
