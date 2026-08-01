<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = Blog::query()->orderByDesc('id');
        if ($s = trim((string) $request->query('q', ''))) {
            $q->where('title', 'like', "%{$s}%");
        }
        return $this->ok($q->paginate((int) min(100, max(1, (int) $request->query('per_page', 20)))));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'body'        => ['required', 'string'],
            'category_id' => ['nullable', 'integer'],
            'image'       => ['nullable', 'string'],
            'status'      => ['nullable', 'in:0,1'],
        ]);
        $data['slug']       = Str::slug($data['title']);
        $data['status']    ??= '1';
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return $this->created(Blog::create($data));
    }

    public function show(int $id) { return $this->ok(Blog::findOrFail($id)); }

    public function update(int $id, Request $request)
    {
        $blog = Blog::findOrFail($id);
        $data = $request->validate([
            'title'       => ['sometimes', 'string', 'max:200'],
            'body'        => ['sometimes', 'string'],
            'category_id' => ['sometimes', 'nullable', 'integer'],
            'image'       => ['sometimes', 'nullable', 'string'],
            'status'      => ['sometimes', 'in:0,1'],
        ]);
        if (isset($data['title'])) $data['slug'] = Str::slug($data['title']);
        $data['updated_at'] = now();
        $blog->fill($data)->save();
        return $this->ok($blog);
    }

    public function destroy(int $id)
    {
        Blog::findOrFail($id)->delete();
        return $this->ok(['message' => 'Blog deleted.']);
    }
}
