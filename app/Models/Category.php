<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'catagory_main';

    protected $primaryKey = 'cat_id';

    public $timestamps = false;

    protected $guarded = [];

    protected $appends = ['picture_url'];

    public function getPictureUrlAttribute(): ?string
    {
        $picture = (string) ($this->picture ?? '');
        if ($picture === '') {
            return null;
        }
        if (preg_match('~^https?://~i', $picture)) {
            return $picture;
        }

        $relative = ltrim($picture, '/');
        $path = str_starts_with($relative, 'site/') ? $relative : 'site/'.$relative;

        return rtrim(config('app.url'), '/').'/storage/'.$path;
    }

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
