<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\Filterable;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreAdRequest;
use App\Http\Requests\V1\UpdateAdRequest;
use App\Http\Resources\V1\AdDetailResource;
use App\Http\Resources\V1\AdResource;
use App\Models\Post;
use App\Services\AdMutationService;
use App\Services\AdStatsService;
use Illuminate\Http\Request;

/**
 * Owner-side write and management endpoints:
 *   POST   /api/v1/ads                          create
 *   PUT    /api/v1/ads/{id}                     update
 *   DELETE /api/v1/ads/{id}                     soft-delete (status=expire, hide=1)
 *   POST   /api/v1/ads/{id}/images              append images
 *   DELETE /api/v1/ads/{id}/images/{filename}   remove one image
 *   POST   /api/v1/ads/{id}/{action}            hide/unhide/resubmit
 *   GET    /api/v1/me/ads?status=active|pending|expire|hidden
 */
class AdMineController extends Controller
{
    use Filterable;

    public function __construct(
        private readonly AdMutationService $svc,
        private readonly AdStatsService $stats,
    ) {}

    public function store(StoreAdRequest $request)
    {
        $post = $this->svc->create(
            $request->user()->id,
            $request->validated(),
            (array) $request->file('images', [])
        );
        return $this->created(new AdDetailResource(
            $post->load(['category', 'subCategory', 'user', 'customData'])
        ));
    }

    public function update(int $id, UpdateAdRequest $request)
    {
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);
        $post = $this->svc->update($post, $request->validated(), (array) $request->file('images', []));
        return $this->ok(new AdDetailResource(
            $post->load(['category', 'subCategory', 'user', 'customData'])
        ));
    }

    public function destroy(int $id, Request $request)
    {
        $post = Post::findOrFail($id);
        $this->authorize('delete', $post);
        $post->forceFill(['status' => 'expire', 'hide' => '1', 'updated_at' => now()])->save();
        return $this->ok(['message' => 'Ad removed.']);
    }

    public function addImages(int $id, Request $request)
    {
        $request->validate(['images' => ['required','array','max:8'], 'images.*' => ['image','mimes:jpg,jpeg,png,webp','max:5120']]);
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);
        $this->svc->update($post, [], (array) $request->file('images', []));
        return $this->ok(new AdDetailResource($post->fresh()));
    }

    public function deleteImage(int $id, string $filename, Request $request)
    {
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);
        $this->svc->deleteImage($post, basename($filename));
        return $this->noContent();
    }

    public function action(int $id, string $action, Request $request)
    {
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);
        match ($action) {
            'hide'     => $post->forceFill(['hide' => '1', 'updated_at' => now()])->save(),
            'unhide'   => $post->forceFill(['hide' => '0', 'updated_at' => now()])->save(),
            'resubmit' => $post->forceFill(['status' => 'pending', 'hide' => '0', 'updated_at' => now(),
                                            'expire_date' => now()->addDays(60)->timestamp])->save(),
            'sold-out' => $post->forceFill(['status' => 'sold_out',  'updated_at' => now()])->save(),
            'restock'  => $post->forceFill(['status' => 'active',    'updated_at' => now()])->save(),
            'remove'   => $post->forceFill(['status' => 'removed', 'hide' => '1', 'updated_at' => now()])->save(),
            'publish'  => $post->forceFill(['status' => 'pending', 'updated_at' => now()])->save(),
            default    => abort(422, 'Unknown action.'),
        };
        return $this->ok(new AdDetailResource($post->fresh()));
    }

    public function mine(Request $request)
    {
        $q = Post::query()->where('user_id', $request->user()->id);

        // ?status=all|active|pending|sold_out|removed|draft|expire|hidden
        match ((string) $request->query('status', '')) {
            'active'   => $q->where('status', 'active')->where('hide', '0'),
            'pending'  => $q->where('status', 'pending'),
            'expire'   => $q->where('status', 'expire'),
            'sold_out' => $q->where('status', 'sold_out'),
            'removed'  => $q->where('status', 'removed'),
            'draft'    => $q->where('status', 'draft'),
            'hidden'   => $q->where('hide',   '1'),
            default    => null,
        };

        // ?condition=new|used
        if ($c = $request->query('condition')) $q->where('condition', $c);

        $q->with(['category', 'subCategory'])->orderByDesc('id');
        return $this->ok(AdResource::collection($q->paginate($this->perPage($request, 12))));
    }

    /**
     * Shop-side aggregated stats for /shop dashboard header + charts.
     *
     *   GET /api/v1/me/shop/stats
     */
    public function shopStats(Request $request)
    {
        return $this->ok($this->stats->shopStats($request->user()->id));
    }

    /**
     * GET /api/v1/me/wishlisted — buyers who favourited my ads.
     * Returns { data: [{ user, ad }], meta }.
     */
    public function wishlisted(Request $request)
    {
        $userId = $request->user()->id;
        $mineIds = Post::where('user_id', $userId)->pluck('id');

        $rows = \App\Models\Favourite::with(['user:id,username,name,image', 'post:id,product_name,slug,price,screen_shot'])
            ->whereIn('product_id', $mineIds)
            ->orderByDesc('id')
            ->paginate($this->perPage($request, 20));

        return $this->ok($rows);
    }

    /* --------------------------------------------------------------- */

}
