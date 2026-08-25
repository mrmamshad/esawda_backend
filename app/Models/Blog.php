<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blog';

    protected $guarded = [];

    public function author()
    {
        return $this->belongsTo(User::class, 'author');
    }

    public function categories()
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_cat_relation', 'blog_id', 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id');
    }
}
