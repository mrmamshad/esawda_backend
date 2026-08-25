<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CustomFieldData;
use App\Models\Post;
use App\Models\SubCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Create/update logic for classified ads, isolated so the controller stays
 * thin and the same code can be reused by admin flows (Filament) later.
 */
class AdMutationService
{
    public function create(int $userId, array $data, array $images = []): Post
    {
        return DB::transaction(function () use ($userId, $data, $images) {
            $post = Post::create($this->attrs($data, isNew: true, userId: $userId));
            $this->syncImages($post, $images);
            $this->syncCustom($post, $data['custom'] ?? []);

            return $post->fresh();
        });
    }

    public function update(Post $post, array $data, array $images = []): Post
    {
        return DB::transaction(function () use ($post, $data, $images) {
            $post->fill($this->attrs($data, isNew: false, userId: (int) $post->user_id));
            $post->status = 'pending'; // any edit re-enters moderation
            $post->save();
            if ($images) {
                $this->syncImages($post, $images, append: true);
            }
            if (array_key_exists('custom', $data)) {
                $this->syncCustom($post, $data['custom']);
            }

            return $post->fresh();
        });
    }

    public function deleteImage(Post $post, string $filename): void
    {
        $imgs = $this->currentImages($post);
        $imgs = array_values(array_filter($imgs, fn ($n) => $n !== $filename));
        Storage::disk('public')->delete("products/{$filename}");
        $post->screen_shot = json_encode($imgs);
        $post->save();
    }

    /* --------------------------------------------------------------- */

    private function attrs(array $d, bool $isNew, ?int $userId = null): array
    {
        $slug = isset($d['title']) ? Str::slug($d['title']) : null;
        $out = array_filter([
            'product_name' => $d['title'] ?? null,
            'slug' => $slug,
            'condition' => $d['condition'] ?? null,
            'description' => $d['description'] ?? null,
            'category' => $d['category'] ?? null,
            'sub_category' => $d['sub_category'] ?? null,
            'price' => $d['price'] ?? null,
            'negotiable' => isset($d['negotiable']) ? ($d['negotiable'] ? '1' : '0') : null,
            'phone' => $d['phone'] ?? null,
            'whatsapp' => $d['whatsapp'] ?? null,
            'bundle_items' => array_key_exists('bundle_items', $d)
                              ? $this->resolveBundleItems($d['bundle_items'], (int) $userId)
                              : null,
            'hide_phone' => isset($d['hide_phone']) ? ($d['hide_phone'] ? '1' : '0') : null,
            'location' => $d['address'] ?? null,
            'city' => $d['city'] ?? null,
            'state' => $d['state'] ?? null,
            'country' => $d['country'] ?? null,
            'latlong' => isset($d['lat'], $d['lng']) ? "{$d['lat']},{$d['lng']}" : null,
            'tag' => $this->buildTags($d),
        ], fn ($v) => $v !== null);

        if ($isNew) {
            $out += [
                'user_id' => $userId,
                'status' => 'pending',
                'featured' => '0', 'urgent' => '0', 'highlight' => '0', 'hide' => '0',
                'view' => 0,
                'created_at' => now(),
                'updated_at' => now(),
                'duration_days' => (int) ($d['duration_days'] ?? 30),
                'expire_date' => now()->addDays((int) ($d['duration_days'] ?? 30))->timestamp,
            ];
        } else {
            $out['updated_at'] = now();
        }

        return $out;
    }

    /**
     * Derive listing tags automatically from its category, sub-category,
     * title and description — the seller never enters them by hand. Category
     * names carry the most weight, followed by distinctive words pulled from
     * the title and description. Returns a comma-separated string (or null
     * when there is nothing to build from, e.g. a partial update).
     */
    private function buildTags(array $d): ?string
    {
        $seeds = [];

        if (!empty($d['category'])) {
            $seeds[] = Category::where('cat_id', (int) $d['category'])->value('cat_name');
        }
        if (!empty($d['sub_category'])) {
            $seeds[] = SubCategory::where('sub_cat_id', (int) $d['sub_category'])->value('sub_cat_name');
        }

        $seeds[] = $d['title'] ?? null;
        $seeds[] = $d['description'] ?? null;

        // Nothing to derive from — leave the column untouched.
        if (!array_filter($seeds, fn ($v) => filled($v))) {
            return null;
        }

        $tags = [];
        foreach ($seeds as $seed) {
            foreach ($this->keywords((string) $seed) as $word) {
                $tags[$word] = true;                 // dedupe, preserve order
            }
        }

        // Cap the list so the legacy `tag` column stays lean and search-friendly.
        $tags = array_slice(array_keys($tags), 0, 12);

        return $tags ? implode(',', $tags) : null;
    }

    /**
     * Break a phrase into lower-cased, meaningful keywords: drops stop-words,
     * short tokens and anything non-alphanumeric so tags stay clean.
     *
     * @return list<string>
     */
    private function keywords(string $text): array
    {
        static $stop = [
            'the', 'and', 'for', 'with', 'this', 'that', 'you', 'your', 'are',
            'was', 'has', 'have', 'from', 'not', 'but', 'all', 'any', 'can',
            'new', 'used', 'good', 'best', 'sale', 'buy', 'sell', 'item', 'items',
        ];

        $text = strip_tags($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text) ?? '';
        $words = preg_split('/\s+/u', mb_strtolower(trim($text)), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_filter(
            $words,
            fn ($w) => mb_strlen($w) >= 3 && !in_array($w, $stop, true),
        ));
    }

    /**
     * Only keep bundle ids the user actually owns and that are currently live —
     * never trust the form for ownership/status. Returns null if any member
     * fails, so a bad bundle is simply not persisted.
     */
    private function resolveBundleItems(mixed $items, int $ownerId): ?array
    {
        $ids = collect((array) $items)->filter(fn ($v) => is_numeric($v))->map('intval')->all();
        if (count($ids) < 2) {
            return null;
        }

        $owned = Post::whereIn('id', $ids)
            ->where('user_id', $ownerId)
            ->active()                               // live + not expired + not hidden
            ->whereNotIn('id', function ($q) {       // bundle members can't themselves be bundles
                $q->select('id')->from('product')->whereNotNull('bundle_items');
            })
            ->pluck('id')->all();

        return count($owned) === count($ids) ? $ids : null;
    }

    private function currentImages(Post $post): array
    {
        $raw = $post->screen_shot;
        if (empty($raw)) {
            return [];
        }

        return is_array($raw)
            ? $raw
            : (json_decode((string) $raw, true) ?: preg_split('/[,;]+/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY));
    }

    /** @param UploadedFile[] $files */
    private function syncImages(Post $post, array $files, bool $append = false): void
    {
        // No-op when there are no incoming files and nothing to reset.
        if (!$files && $append) {
            return;
        }

        $existing = $append ? $this->currentImages($post) : [];
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                continue;
            }
            $name = 'ad_'.$post->id.'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
            $file->storeAs('products', $name, 'public');
            $existing[] = $name;
        }
        // Persist only when we actually have image names — never write an
        // empty "[]" placeholder into the legacy `screen_shot` column.
        if ($existing) {
            $post->screen_shot = json_encode(array_values($existing));
            $post->save();
        }
    }

    private function syncCustom(Post $post, array $values): void
    {
        // Legacy custom_data has (product_id, field_id, field_type, field_data).
        // Simple wipe-and-replace strategy.
        CustomFieldData::where('product_id', $post->id)->delete();
        foreach ($values as $fieldId => $val) {
            CustomFieldData::create([
                'product_id' => $post->id,
                'field_id' => (int) $fieldId,
                'field_type' => is_bool($val) ? 'bool' : (is_numeric($val) ? 'number' : 'text'),
                'field_data' => is_array($val) ? json_encode($val) : (string) $val,
            ]);
        }
    }
}
