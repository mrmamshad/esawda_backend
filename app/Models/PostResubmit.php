<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostResubmit extends Model
{
    protected $table = 'product_resubmit';
    protected $guarded = [];

    public function post() { return $this->belongsTo(Post::class, 'product_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
}
