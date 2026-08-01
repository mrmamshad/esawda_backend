<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

/** Mobile ads API. */
class AdController extends Controller
{
    public function index(Request $request)
    {
        $q = Post::active();
        if ($cat  = $request->query('cat'))      $q->where('category', $cat);
        if ($city = $request->query('city'))     $q->where('city', $city);
        if ($kw   = $request->query('q'))        $q->where('product_name', 'like', "%$kw%");
        return response()->json($q->orderByDesc('id')->paginate(20));
    }

    public function show(int $id)
    {
        $post = Post::with(['user:id,username,name', 'category:cat_id,cat_name', 'reviews'])
                    ->find($id);
        if (! $post) return response()->json(['message' => 'Not found'], 404);
        $post->increment('view');
        return response()->json(['ad' => $post]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:150',
            'description'  => 'required|string',
            'category'     => 'required|integer',
            'price'        => 'nullable|integer|min:0',
            'phone'        => 'nullable|string|max:50',
            'city'         => 'nullable|string|max:50',
            'country'      => 'nullable|string|max:50',
            'image'        => 'nullable|image|max:5120',
        ]);
        $screenshot = null;
        if ($request->hasFile('image')) {
            $f = $request->file('image');
            $name = time() . '_' . uniqid() . '.' . $f->getClientOriginalExtension();
            $f->move(public_path('storage/products'), $name);
            $screenshot = json_encode([$name]);
        }
        $post = Post::create([
            'user_id'      => $request->user()->id,
            'status'       => 'pending',
            'product_name' => $data['product_name'],
            'slug'         => \Str::slug($data['product_name']),
            'description'  => $data['description'],
            'category'     => $data['category'],
            'price'        => $data['price'] ?? 0,
            'phone'        => $data['phone'] ?? null,
            'city'         => $data['city'] ?? null,
            'country'      => $data['country'] ?? null,
            'screen_shot'  => $screenshot,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
        return response()->json(['ad' => $post], 201);
    }

    public function update(Request $request, int $id)
    {
        $post = Post::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $post->fill($request->only([
            'product_name', 'description', 'category', 'price', 'phone', 'city', 'country',
        ]));
        $post->status = 'pending';
        $post->updated_at = now();
        $post->save();
        return response()->json(['ad' => $post]);
    }

    public function destroy(Request $request, int $id)
    {
        $deleted = Post::where('id', $id)->where('user_id', $request->user()->id)->delete();
        return response()->json(['deleted' => (bool) $deleted]);
    }

    public function mine(Request $request)
    {
        return response()->json(
            Post::where('user_id', $request->user()->id)->orderByDesc('id')->paginate(20)
        );
    }
}
