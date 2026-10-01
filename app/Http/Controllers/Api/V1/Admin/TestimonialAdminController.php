<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\RevalidateFrontendJob;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestimonialAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = Testimonial::query()->orderByDesc('id');
        if ($s = trim((string) $request->query('q', ''))) {
            $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")
                ->orWhere('designation', 'like', "%{$s}%")
                ->orWhere('content', 'like', "%{$s}%"));
        }

        return $this->ok($q->paginate((int) min(100, max(1, (int) $request->query('per_page', 20)))));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'designation' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $testimonial = Testimonial::create([
            'name' => $data['name'],
            'designation' => $data['designation'] ?? null,
            'content' => $data['content'],
            'image' => $this->storeImageFile($request) ?? $this->cleanImage($data['image'] ?? null),
        ]);

        $this->bustHomeCaches();

        return $this->created($testimonial->fresh());
    }

    public function show(int $id)
    {
        return $this->ok(Testimonial::findOrFail($id));
    }

    public function update(int $id, Request $request)
    {
        $testimonial = Testimonial::findOrFail($id);
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'designation' => ['sometimes', 'nullable', 'string', 'max:100'],
            'content' => ['sometimes', 'required', 'string'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'image_file' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $fill = [];
        foreach (['name', 'designation', 'content'] as $key) {
            if (array_key_exists($key, $data)) {
                $fill[$key] = $data[$key];
            }
        }

        $newFile = $this->storeImageFile($request);
        if ($newFile !== null) {
            $this->deleteLocalImage($testimonial->image);
            $fill['image'] = $newFile;
        } elseif (array_key_exists('image', $data)) {
            $clean = $this->cleanImage($data['image']);
            if ($clean === null) {
                $this->deleteLocalImage($testimonial->image);
            }
            $fill['image'] = $clean;
        }

        if ($fill !== []) {
            $testimonial->fill($fill)->save();
        }

        $this->bustHomeCaches();

        return $this->ok($testimonial->fresh());
    }

    public function destroy(int $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $this->deleteLocalImage($testimonial->image);
        $testimonial->delete();

        $this->bustHomeCaches();

        return $this->ok(['message' => 'Testimonial deleted.']);

    }

    /**
     * Homepage shows testimonials from the cached `home.payload` plus
     * Next.js ISR — bust both immediately so admin edits appear at once
     * instead of waiting out the cache timers.
     */
    private function bustHomeCaches(): void
    {
        Cache::forget('home.payload');
        RevalidateFrontendJob::dispatch();
    }

    /** Store an uploaded author photo under public/testimonials, returning the bare filename (legacy format). */
    private function storeImageFile(Request $request): ?string
    {
        if (!$request->hasFile('image_file')) {
            return null;
        }
        $file = $request->file('image_file');
        if (!$file || !$file->isValid()) {
            return null;
        }
        $name = Str::random(32).'.'.$file->getClientOriginalExtension();
        $file->storeAs('testimonials', $name, 'public');

        return $name;
    }

    /** Accept a remote URL as-is; a bare filename stays a filename. */
    private function cleanImage(mixed $image): ?string
    {
        $image = trim((string) ($image ?? ''));
        if ($image === '') {
            return null;
        }
        // Guard against a full storage URL being pasted — keep only the file part.
        if (preg_match('~^https?://~i', $image) && str_contains($image, '/storage/testimonials/')) {
            $image = substr($image, (int) strpos($image, '/storage/testimonials/') + strlen('/storage/testimonials/'));
        }

        return mb_substr(ltrim($image, '/'), 0, 100);
    }

    /** Remove a previously uploaded local photo (remote URLs untouched). */
    private function deleteLocalImage(mixed $image): void
    {
        if (!is_string($image) || $image === '' || preg_match('~^https?://~i', $image)) {
            return;
        }
        try {
            Storage::disk('public')->delete('testimonials/'.ltrim($image, '/'));
        } catch (\Throwable) {
            // Orphaned file — the record update still stands.
        }
    }
}
