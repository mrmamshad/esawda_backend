<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = 'blog_categories';
    public $timestamps = false;
    protected $guarded = [];

    public function blogs() {
        return $this->belongsToMany(Blog::class, 'blog_cat_relation', 'category_id', 'blog_id');
    }
}
