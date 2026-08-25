<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'catagory_main';

    protected $primaryKey = 'cat_id';

    public $timestamps = false;

    protected $guarded = [];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'main_cat_id', 'cat_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'category', 'cat_id');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class, 'translation_id', 'cat_id')
            ->where('category_type', 'main');
    }
}
