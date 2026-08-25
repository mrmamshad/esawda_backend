<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';

    protected $primaryKey = 'reviewID';

    public $timestamps = false;

    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class, 'productID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
