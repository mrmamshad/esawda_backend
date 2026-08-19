<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\Mail\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdAdminController extends Controller
{
    public function __construct(private readonly MailService $mail) {}

    public function index(Request $request)
    {
        $q = Post::query()->with(['user:id,username,name', 'category']);

        if ($status = $request->query('status'))       $q->where('status', $status);
        if ($condition = $request->query('condition')) $q->where('condition', $condition);
        if ($request->boolean('featured'))             $q->where('featured', '1');
        if ($s = trim((string) $request->query('q', ''))) {
            $q->where(function ($sub) use ($s) {
                $sub->where('product_name', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $q->orderByDesc('id');
        return $this->ok($q->paginate((int) min(100, max(1, (int) $request->query('per_page', 20)))));
    }

    public function show(int $id)
    {
        return $this->ok(Post::with(['user', 'category', 'subCategory', 'customData'])->findOrFail($id));
    }

    public function approve(int $id)
    {
        $post = Post::findOrFail($id);
        $post->forceFill(['status' => 'active', 'hide' => '0', 'updated_at' => now()])->save();
        Cache::flush();
        $this->mail->adApprovedToSeller($post->load('user'));
        return $this->ok(['message' => 'Ad approved.', 'ad' => $post]);
    }

    public function reject(int $id, Request $request)
    {
        $reason = (string) $request->input('reason', '');
        $post   = Post::findOrFail($id);
        $post->forceFill(['status' => 'rejected', 'hide' => '1', 'reject_reason' => $reason, 'updated_at' => now()])->save();
        $this->mail->adRejectedToSeller($post->load('user'), $reason);
        return $this->ok(['message' => 'Ad rejected.', 'ad' => $post]);
    }

    public function feature(int $id)
    {
        $post = Post::findOrFail($id);
        $post->forceFill(['featured' => '1', 'updated_at' => now()])->save();
        return $this->ok($post);
    }

    public function unfeature(int $id)
    {
        $post = Post::findOrFail($id);
        $post->forceFill(['featured' => '0', 'updated_at' => now()])->save();
        return $this->ok($post);
    }

    public function destroy(int $id)
    {
        Post::findOrFail($id)->delete();
        return $this->ok(['message' => 'Ad deleted.']);
    }
}
