<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SubCategoryResource;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = SubCategory::query()->orderBy('cat_order');
        if ($cat = $request->query('category')) $q->where('main_cat_id', (int) $cat);
        return $this->ok(SubCategoryResource::collection($q->get()));
    }
}
