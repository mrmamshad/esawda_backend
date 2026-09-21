<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = Blog::query()->orderByDesc('id');
        if ($s = trim((string) $request->query('q', ''))) {
            $q->where('title', 'like', "%{$s}%");
        }

        $rows = $q->paginate((int) min(100, max(1, (int) $request->query('per_page', 20))));
        // Ensure a slug is always present for the admin table (older rows may
        // predate the slug column) — fall back to a title-derived slug.
        $rows->getCollection()->transform(
            fn ($b) => tap($b, fn ($b) => $b->setAttribute(
                'slug',
                $b->slug ?: Str::slug((string) $b->title)
            ))
        );

        return $this->ok($rows);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            // Matches the ad_blog.status enum column.
            'status' => ['nullable', 'in:draft,published,unpublished'],
        ]);

        $blog = Blog::create([
            'title' => $data['title'],
            'slug' => $this->resolveSlug($data['slug'] ?? null, $data['title']),
            'description' => $data['description'] ?? $data['body'] ?? null,
            'tags' => $data['tags'] ?? null,
            'image' => $this->storeImageFile($request) ?? $this->cleanImageUrl($data['image'] ?? null),
            'status' => $data['status'] ?? 'published',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->created($blog);
    }

    public function show(int $id)
    {
        return $this->ok(Blog::findOrFail($id));
    }

    public function update(int $id, Request $request)
    {
        $blog = Blog::findOrFail($id);
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:200'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:200'],
            'description' => ['sometimes', 'nullable', 'string'],
            'body' => ['sometimes', 'nullable', 'string'],
            'tags' => ['sometimes', 'nullable', 'string', 'max:500'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'image_file' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            // Matches the ad_blog.status enum column.
            'status' => ['sometimes', 'in:draft,published,unpublished'],
        ]);

        $fill = [];
        if (array_key_exists('title', $data)) {
            $fill['title'] = $data['title'];
        }
        if (array_key_exists('slug', $data)) {
            $fill['slug'] = $this->resolveSlug($data['slug'], $data['title'] ?? $blog->title);
        }
        if (array_key_exists('description', $data) || array_key_exists('body', $data)) {
            $fill['description'] = $data['description'] ?? $data['body'] ?? null;
        }
        if (array_key_exists('tags', $data)) {
            $fill['tags'] = $data['tags'];
        }
        if (array_key_exists('status', $data)) {
            $fill['status'] = $data['status'];
        }

        $newFile = $this->storeImageFile($request);
        if ($newFile !== null) {
            $this->deleteLocalImage($blog->image);
            $fill['image'] = $newFile;
        } elseif (array_key_exists('image', $data)) {
            $clean = $this->cleanImageUrl($data['image']);
            if ($clean === null) {
                $this->deleteLocalImage($blog->image);
            }
            $fill['image'] = $clean;
        }

        if ($fill !== []) {
            $fill['updated_at'] = now();
            $blog->fill($fill)->save();
        }

        return $this->ok($blog->fresh());
    }

    public function destroy(int $id)
    {
        $blog = Blog::findOrFail($id);
        $this->deleteLocalImage($blog->image);
        $blog->delete();

        return $this->ok(['message' => 'Blog deleted.']);
    }

    /** Use the supplied slug when present, otherwise derive one from the title. */
    private function resolveSlug(?string $slug, ?string $title): string
    {
        $slug = Str::slug((string) ($slug ?? ''));

        return $slug !== '' ? $slug : Str::slug((string) $title);
    }

    /** Store an uploaded cover under public/blog, returning the filename. */
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
        $file->storeAs('blog', $name, 'public');

        return $name;
    }

    private function cleanImageUrl(mixed $url): ?string
    {
        $url = trim((string) ($url ?? ''));

        return $url !== '' ? mb_substr($url, 0, 255) : null;
    }

    /** Remove a previously uploaded local cover (remote URLs untouched). */
    private function deleteLocalImage(mixed $image): void
    {
        if (!is_string($image) || $image === '' || preg_match('~^https?://~i', $image)) {
            return;
        }
        try {
            Storage::disk('public')->delete('blog/'.ltrim($image, '/'));
        } catch (\Throwable) {
            // Orphaned file — the record update still stands.
        }
    }
}
