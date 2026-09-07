<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'status' => TransactionStatus::class,
        'amount_minor' => 'integer',
        'fulfilled_at' => 'datetime',
        'gateway_initiated_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'policy_accepted_at' => 'datetime',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'product_id');
    }
}
