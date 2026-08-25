<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CityResource;
use App\Http\Resources\V1\CountryResource;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Cache::remember('countries', 300, fn () => Country::where('active', 1)->orderBy('name')->get());

        return $this->ok(CountryResource::collection($countries));
    }

    public function cities(string $code, Request $request)
    {
        $perPage = max(1, min(200, (int) $request->query('per_page', 100)));
        $needle = trim((string) $request->query('q', ''));
        $key = 'cities.'.strtoupper($code).".$perPage.".md5(mb_strtolower($needle));

        $cities = Cache::remember($key, 300, function () use ($code, $perPage, $needle) {
            $q = City::where('country_code', strtoupper($code))
                ->where('active', 1)
                ->orderBy('name');
            if ($needle) {
                $q->where('name', 'like', "%{$needle}%");
            }

            return $q->paginate($perPage);
        });

        return $this->ok(CityResource::collection($cities));
    }
}
