<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonials';

    public $timestamps = false;

    protected $guarded = [];

    protected $appends = ['image_url'];

    /**
     * Public URL of the author's photo. The legacy `image` column stores a
     * bare filename (e.g. `rubaiya.jpg`) served from
     * `public/storage/testimonials/`; Filament uploads store the
     * disk-relative path (e.g. `testimonials/rubaiya.jpg`). Both resolve here.
     */
    public function getImageUrlAttribute(): ?string
    {
        $image = ltrim((string) ($this->image ?? ''), '/');
        if ($image === '') {
            return null;
        }
        if (preg_match('~^https?://~i', $image)) {
            return $image;
        }

        $relative = str_starts_with($image, 'testimonials/') ? $image : 'testimonials/'.$image;

        return rtrim(config('app.url'), '/').'/storage/'.$relative;
    }
}
