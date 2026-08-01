<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CityResource;
use App\Http\Resources\V1\CountryResource;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::where('active', 1)->orderBy('name')->get();
        return $this->ok(CountryResource::collection($countries));
    }

    public function cities(string $code, Request $request)
    {
        $perPage = max(1, min(200, (int) $request->query('per_page', 100)));
        $q = City::where('country_code', strtoupper($code))
                 ->where('active', 1)
                 ->orderBy('name');
        if ($needle = trim((string) $request->query('q', ''))) {
            $q->where('name', 'like', "%{$needle}%");
        }
        return $this->ok(CityResource::collection($q->paginate($perPage)));
    }
}
