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
            // Zero-price promo plans (Early Bird) activate without a gateway.
            'is_free' => ['nullable', 'boolean'],
            'ads_limit' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'featured_ads' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            // Offer bullets shown on /shop/plan, one line each in the admin UI.
            'features' => ['nullable', 'array', 'max:20'],
            'features.*' => ['nullable', 'string', 'max:200'],
        ]);

        $settings = [];
        foreach (['ads_limit', 'featured_ads', 'duration_days'] as $key) {
            if (isset($data[$key])) {
                $settings[$key] = (int) $data[$key];
            }
        }
        $features = self::normaliseFeatures($data['features'] ?? null);
        if ($features) {
            $settings['features'] = $features;
        }
        unset($data['ads_limit'], $data['featured_ads'], $data['duration_days'], $data['features']);

        return $this->created(Plan::create($data + [
            'status' => $data['status'] ?? '1',
            'settings' => json_encode($settings),
            'date' => now(),
        ]));
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
            'is_free' => ['sometimes', 'boolean'],
            'ads_limit' => ['sometimes', 'integer', 'min:0', 'max:100000'],
            'featured_ads' => ['sometimes', 'integer', 'min:0', 'max:100000'],
            'duration_days' => ['sometimes', 'integer', 'min:1', 'max:3650'],
            'features' => ['sometimes', 'nullable', 'array', 'max:20'],
            'features.*' => ['nullable', 'string', 'max:200'],
        ]);

        $settings = is_array($plan->settings)
            ? $plan->settings
            : (json_decode((string) $plan->settings, true) ?: []);
        foreach (['ads_limit', 'featured_ads', 'duration_days'] as $key) {
            if (array_key_exists($key, $data)) {
                $settings[$key] = (int) $data[$key];
                unset($data[$key]);
            }
        }
        // Present (even empty) = replace, so the admin can clear the list.
        if (array_key_exists('features', $data)) {
            $normalised = self::normaliseFeatures($data['features']);
            if ($normalised) {
                $settings['features'] = $normalised;
            } else {
                unset($settings['features']);
            }
            unset($data['features']);
        }
        $data['settings'] = json_encode($settings);

        $plan->fill($data)->save();

        return $this->ok($plan);
    }

    public function destroy(int $id)
    {
        Plan::findOrFail($id)->delete();

        return $this->ok(['message' => 'Plan deleted.']);
    }

    /**
     * Trimmed, non-empty lines capped at 20 — shared by store/update so the
     * settings JSON always holds a clean string list (or nothing).
     *
     * @return array<int, string>
     */
    private static function normaliseFeatures(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $out = [];
        foreach ($value as $line) {
            if (!is_scalar($line)) {
                continue;
            }
            $line = trim((string) $line);
            if ($line !== '') {
                $out[] = mb_substr($line, 0, 200);
            }
            if (count($out) >= 20) {
                break;
            }
        }

        return array_values($out);
    }
}
