<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'status'       => \App\Enums\TransactionStatus::class,
        'fulfilled_at' => 'datetime',
        'meta'         => 'array',
    ];

    public function seller() { return $this->belongsTo(User::class, 'seller_id'); }
    public function post()   { return $this->belongsTo(Post::class, 'product_id'); }

    /**
     * Virtual FKs backed by the accessors below. The legacy `seller_id`
     * column actually stores the payer, while the real product owner lives
     * in `meta.seller_id` for purchases; ad upgrades / plan buys involve a
     * single account (the one that paid).
     */

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'id');
    }

    public function getBuyerIdAttribute(): ?int
    {
        return $this->meta['buyer_id'] ?? $this->seller_id;
    }

    public function sellerInfo()
    {
        return $this->belongsTo(User::class, 'seller_info_id', 'id');
    }

    public function getSellerInfoIdAttribute(): ?int
    {
        return $this->meta['seller_id'] ?? $this->seller_id;
    }
}
