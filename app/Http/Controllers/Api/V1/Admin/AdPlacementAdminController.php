<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPlacement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Admin CRUD for ad placements.
 *
 *   GET    /api/v1/admin/ads/placements          — list all
 *   POST   /api/v1/admin/ads/placements          — create (multipart: image)
 *   PUT    /api/v1/admin/ads/placements/{id}     — update (multipart: image)
 *   DELETE /api/v1/admin/ads/placements/{id}     — delete
 *
 * Every write busts the frontend AdSlot response cache so the change
 * applies immediately (within the 120s Cache TTL).
 */
class AdPlacementAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = AdPlacement::query()->orderBy('slug');

        if ($s = trim((string) $request->query('q', ''))) {
            $q->where(function ($sub) use ($s) {
                $sub->where('slug', 'like', "%{$s}%")
                    ->orWhere('title', 'like', "%{$s}%");
            });
        }

        return $this->ok($q->paginate((int) min(100, max(1, (int) $request->query('per_page', 50)))));
    }

    public function show(int $id)
    {
        return $this->ok(AdPlacement::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => ['required', 'string', 'max:100', 'unique:ad_placements,slug'],
            'title' => ['nullable', 'string', 'max:255'],
            'size' => ['sometimes', 'string', 'max:30'],
            'link_url' => ['nullable', 'url', 'max:2000'],
            'alt_text' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ]);

        $ad = new AdPlacement;
        $ad->slug = $data['slug'];
        $ad->title = $data['title'] ?? null;
        $ad->size = $data['size'] ?? 'wide';
        $ad->link_url = $data['link_url'] ?? null;
        $ad->alt_text = $data['alt_text'] ?? null;
        $ad->status = $data['status'] ?? true;
        $ad->starts_at = $data['starts_at'] ?? null;
        $ad->expires_at = $data['expires_at'] ?? null;

        if ($request->hasFile('image')) {
            $ad->image_path = $this->storeImage($request->file('image'));
        }

        $ad->save();

        Cache::flush();

        return $this->created($ad);
    }

    public function update(int $id, Request $request)
    {
        $ad = AdPlacement::findOrFail($id);

        $data = $request->validate([
            'slug' => ['sometimes', 'string', 'max:100', 'unique:ad_placements,slug,'.$id],
            'title' => ['nullable', 'string', 'max:255'],
            'size' => ['sometimes', 'string', 'max:30'],
            'link_url' => ['nullable', 'url', 'max:2000'],
            'alt_text' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ]);

        if (isset($data['slug'])) $ad->slug = $data['slug'];
        if (array_key_exists('title', $data)) $ad->title = $data['title'];
        if (isset($data['size'])) $ad->size = $data['size'];
        if (array_key_exists('link_url', $data)) $ad->link_url = $data['link_url'];
        if (array_key_exists('alt_text', $data)) $ad->alt_text = $data['alt_text'];
        if (isset($data['status'])) $ad->status = $data['status'];
        if (array_key_exists('starts_at', $data)) $ad->starts_at = $data['starts_at'];
        if (array_key_exists('expires_at', $data)) $ad->expires_at = $data['expires_at'];

        if ($request->hasFile('image')) {
            if ($ad->image_path) {
                Storage::disk('public')->delete('ads/'.$ad->image_path);
            }
            $ad->image_path = $this->storeImage($request->file('image'));
        }

        $ad->save();

        Cache::flush();

        return $this->ok($ad);
    }

    public function destroy(int $id)
    {
        $ad = AdPlacement::findOrFail($id);
        if ($ad->image_path) {
            Storage::disk('public')->delete('ads/'.$ad->image_path);
        }
        $ad->delete();

        Cache::flush();

        return $this->noContent();
    }

    private function storeImage($file): string
    {
        $name = 'placement_'.Str::random(20).'.'.$file->getClientOriginalExtension();
        $file->storeAs('ads', $name, 'public');
        return $name;
    }
}