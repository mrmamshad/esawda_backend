<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    protected $primaryKey = 'message_id';

    public $timestamps = false;

    protected $guarded = [];

    public function sender()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
