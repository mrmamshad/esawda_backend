<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'catagory_sub';
    protected $primaryKey = 'sub_cat_id';
    public $timestamps = false;
    protected $guarded = [];

    public function category() { return $this->belongsTo(Category::class, 'main_cat_id', 'cat_id'); }
    public function posts()    { return $this->hasMany(Post::class, 'sub_category', 'sub_cat_id'); }
}
