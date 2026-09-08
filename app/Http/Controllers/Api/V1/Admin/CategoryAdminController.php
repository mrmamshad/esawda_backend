<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\RevalidateFrontendJob;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;

/** Complete lifecycle management for the legacy product categories table. */
class CategoryAdminController extends Controller
{
    /**
     * The public taxonomy endpoints cache their responses (categories list
     * for 5 min, home payload for 2 min) and the Next.js homepage fetches
     * both through its own ISR cache. Drop every layer right after any
     * admin write so new categories appear immediately.
     */
    private function bustCategoryCaches(): void
    {
        foreach ([0, 1] as $counts) {
            foreach ([0, 1] as $subs) {
                Cache::forget("categories.{$counts}.{$subs}");
            }
        }
        Cache::forget('home.payload');
        RevalidateFrontendJob::dispatch();
    }

    public function index()
    {
        return $this->ok(
            Category::query()
                ->withCount(['posts', 'subCategories'])
                ->orderByRaw('cat_order IS NULL')
                ->orderBy('cat_order')
                ->orderBy('cat_name')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        unset($data['remove_picture']);
        $data['slug'] = !empty($data['slug']) ? $data['slug'] : Str::slug($data['cat_name']);
        $data['icon'] = !empty($data['icon']) ? $data['icon'] : 'fa-tag';
        $data['cat_order'] ??= ((int) Category::max('cat_order')) + 1;

        $storedPicture = null;
        if ($request->hasFile('picture')) {
            $storedPicture = $this->storePicture($request->file('picture'));
            $data['picture'] = $storedPicture;
        }

        try {
            $category = Category::create($data);
        } catch (\Throwable $e) {
            $this->deleteManagedPicture($storedPicture);
            throw $e;
        }

        $this->bustCategoryCaches();

        return $this->created($category->fresh());
    }

    public function show(int $id)
    {
        return $this->ok(Category::withCount(['posts', 'subCategories'])->findOrFail($id));
    }

    public function update(int $id, Request $request)
    {
        $category = Category::findOrFail($id);
        $data = $this->validated($request, $category);
        $removePicture = (bool) ($data['remove_picture'] ?? false);
        unset($data['remove_picture']);

        if (array_key_exists('slug', $data) && !$data['slug']) {
            $data['slug'] = Str::slug((string) ($data['cat_name'] ?? $category->cat_name));
        }

        $oldPicture = $category->picture;
        $storedPicture = null;
        if ($request->hasFile('picture')) {
            $storedPicture = $this->storePicture($request->file('picture'));
            $data['picture'] = $storedPicture;
        } elseif ($removePicture) {
            $data['picture'] = null;
        }

        try {
            $category->fill($data)->save();
        } catch (\Throwable $e) {
            $this->deleteManagedPicture($storedPicture);
            throw $e;
        }

        if ($oldPicture && $oldPicture !== $category->picture) {
            $this->deleteManagedPicture($oldPicture);
        }

        $this->bustCategoryCaches();

        return $this->ok($category->fresh()->loadCount(['posts', 'subCategories']));
    }

    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);
        if ($category->posts()->exists() || $category->subCategories()->exists()) {
            return $this->error(
                'CATEGORY_NOT_EMPTY',
                'Move or delete this category’s products and subcategories first.',
                409,
            );
        }

        $picture = $category->picture;
        $category->delete();
        $this->deleteManagedPicture($picture);
        $this->bustCategoryCaches();

        return $this->ok(['message' => 'Category deleted.']);
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $id = $category?->getKey();
        $pictureRules = $request->hasFile('picture')
            ? ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max('4mb')]
            : ['nullable', 'string', 'max:2048'];

        $rules = [
            'cat_name' => [$category ? 'sometimes' : 'required', 'string', 'max:100'],
            'slug' => [$category ? 'sometimes' : 'nullable', 'nullable', 'string', 'max:100', Rule::unique('catagory_main', 'slug')->ignore($id, 'cat_id')],
            'icon' => [$category ? 'sometimes' : 'nullable', 'nullable', 'string', 'max:100'],
            'picture' => $pictureRules,
            'cat_order' => [$category ? 'sometimes' : 'nullable', 'nullable', 'integer', 'min:0', 'max:9999'],
            'remove_picture' => ['sometimes', 'boolean'],
        ];
        $data = $request->validate($rules);

        if (!$request->hasFile('picture') && isset($data['picture'])) {
            $this->assertSafePictureReference((string) $data['picture']);
        }

        return $data;
    }

    private function storePicture(UploadedFile $file): string
    {
        $path = $file->storePubliclyAs(
            'site/categories',
            Str::uuid().'.'.strtolower($file->extension()),
            'public',
        );
        if (!$path) {
            throw ValidationException::withMessages(['picture' => ['The category image could not be stored.']]);
        }

        return Str::after($path, 'site/');
    }

    private function deleteManagedPicture(?string $picture): void
    {
        if (!$picture || preg_match('~^https?://~i', $picture)) {
            return;
        }
        $relative = ltrim($picture, '/');
        if (str_starts_with($relative, 'site/')) {
            $relative = Str::after($relative, 'site/');
        }
        if (str_starts_with($relative, 'categories/') && !str_contains($relative, '..')) {
            Storage::disk('public')->delete('site/'.$relative);
        }
    }

    private function assertSafePictureReference(string $picture): void
    {
        if (preg_match('~^https?://~i', $picture)) {
            if (!filter_var($picture, FILTER_VALIDATE_URL)) {
                throw ValidationException::withMessages(['picture' => ['The picture URL is invalid.']]);
            }

            return;
        }
        if (str_contains($picture, '..') || !preg_match('~^[A-Za-z0-9/_\-.]+$~', $picture)) {
            throw ValidationException::withMessages(['picture' => ['The picture reference is invalid.']]);
        }
    }
}
