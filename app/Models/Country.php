<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $guarded = [];

    public function cities()  { return $this->hasMany(City::class, 'country_code', 'code'); }
    public function regions() { return $this->hasMany(Region::class, 'country_code', 'code'); }
}
