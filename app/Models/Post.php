<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent replacement for the legacy `ad_product` table (classified ads).
 */
class Post extends Model
{
    protected $table = 'product';
    protected $guarded = [];
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'price'      => 'integer',
        'view'       => 'integer',
        'status'     => \App\Enums\PostStatus::class,
    ];

    public function scopeActive($q)    { return $q->where('status', 'active')->where('hide', '0'); }
    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeExpired($q)   { return $q->where('status', 'expire'); }
    public function scopeFeatured($q)  { return $q->where('featured', '1'); }
    public function scopeSoldOut($q)   { return $q->where('status', 'sold_out'); }
    public function scopeRemoved($q)   { return $q->where('status', 'removed'); }
    public function scopeDraft($q)     { return $q->where('status', 'draft'); }
    public function scopeBrandNew($q)  { return $q->where('condition', 'new'); }
    public function scopeUsed($q)      { return $q->where('condition', 'used'); }

    public function user()       { return $this->belongsTo(User::class, 'user_id'); }
    public function category()   { return $this->belongsTo(Category::class, 'category', 'cat_id'); }
    public function subCategory(){ return $this->belongsTo(SubCategory::class, 'sub_category', 'sub_cat_id'); }
    public function reviews()    { return $this->hasMany(Review::class, 'productID'); }
    public function customData() { return $this->hasMany(CustomFieldData::class, 'product_id'); }
    public function favouritedBy(){return $this->hasMany(Favourite::class, 'product_id'); }
    public function resubmit()   { return $this->hasOne(PostResubmit::class, 'product_id'); }
}
