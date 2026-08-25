<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanAdminController extends Controller
{
    public function index()
    {
        return $this->ok(Plan::orderBy('id')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100'],
            'monthly_price' => ['nullable', 'numeric'],
            'annual_price' => ['nullable', 'numeric'],
            'ad_limit' => ['nullable', 'integer'],
            'featured' => ['nullable', 'integer'],
            'recommended' => ['nullable', 'boolean'],
            'badge' => ['nullable', 'string', 'max:60'],
            'status' => ['nullable', 'in:0,1'],
        ]);

        return $this->created(Plan::create($data + ['status' => $data['status'] ?? '1']));
    }

    public function show(int $id)
    {
        return $this->ok(Plan::findOrFail($id));
    }

    public function update(int $id, Request $request)
    {
        $plan = Plan::findOrFail($id);
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:100'],
            'monthly_price' => ['sometimes', 'numeric'],
            'annual_price' => ['sometimes', 'numeric'],
            'ad_limit' => ['sometimes', 'integer'],
            'featured' => ['sometimes', 'integer'],
            'recommended' => ['sometimes', 'boolean'],
            'badge' => ['sometimes', 'nullable', 'string', 'max:60'],
            'status' => ['sometimes', 'in:0,1'],
        ]);
        $plan->fill($data)->save();

        return $this->ok($plan);
    }

    public function destroy(int $id)
    {
        Plan::findOrFail($id)->delete();

        return $this->ok(['message' => 'Plan deleted.']);
    }
}
