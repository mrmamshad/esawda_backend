<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    public $timestamps = true;
    protected $guarded = [];

    protected $casts = [
        'amount'        => 'float',
        'seller_paid'   => 'boolean',
    ];

    public function product() { return $this->belongsTo(Post::class, 'product_id'); }
    public function buyer()   { return $this->belongsTo(User::class, 'buyer_id'); }
    public function seller()  { return $this->belongsTo(User::class, 'seller_id'); }
    public function transaction() { return $this->belongsTo(Transaction::class, 'transaction_id'); }
}