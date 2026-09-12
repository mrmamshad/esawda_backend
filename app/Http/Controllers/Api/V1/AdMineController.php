<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\Filterable;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreAdRequest;
use App\Http\Requests\V1\UpdateAdRequest;
use App\Http\Resources\V1\AdDetailResource;
use App\Http\Resources\V1\AdResource;
use App\Jobs\RevalidateFrontendJob;
use App\Models\Favourite;
use App\Models\Option;
use App\Models\Post;
use App\Models\User;
use App\Services\AdMutationService;
use App\Services\AdStatsService;
use App\Services\Mail\MailService;
use App\Services\PostingPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        private readonly MailService $mail,
    ) {}

    public function store(StoreAdRequest $request)
    {
        $isBundle = !empty($request->input('bundle_items'));

        // Admin listing-type switches (Settings → Premium upgrades). The
        // frontend hides the disabled radio, but the API re-checks so old
        // clients can't bypass it. Bundles group existing products and stay
        // exempt. Missing `listing_type` means an old free-path client.
        if (!$isBundle) {
            $listingType = (string) ($request->input('listing_type', 'free') ?: 'free');
            if ($listingType === 'free' && !self::listingTypeEnabled('listing_free_enabled')) {
                return $this->error(
                    'FREE_LISTING_DISABLED',
                    'Free listing is currently disabled. Please choose Premium listing.',
                    403
                );
            }
            if ($listingType === 'premium' && !self::listingTypeEnabled('listing_premium_enabled')) {
                return $this->error(
                    'PREMIUM_LISTING_DISABLED',
                    'Premium listing is currently disabled. Please choose Free listing.',
                    403
                );
            }
        }

        $post = DB::transaction(function () use ($request, $isBundle) {
            $user = User::query()->lockForUpdate()->findOrFail($request->user()->id);

            // Bundles group existing active products, so they are free to create
            // and do not consume subscription listing slots.
            if (!$isBundle && !PostingPolicy::canPostFree($user)) {
                return null;
            }

            $post = $this->svc->create(
                $user->id,
                $request->validated(),
                (array) $request->file('images', [])
            );

            if (!$isBundle && PostingPolicy::consumesQuota($user)) {
                $user->forceFill([
                    'ads_remaining' => (int) $user->ads_remaining - 1,
                    'updated_at' => now(),
                ])->save();
            }

            return $post;
        });

        if (!$post) {
            $blocked = PostingPolicy::isBlocked($request->user());

            return $this->error(
                $blocked ? 'POSTING_BLOCKED' : 'SUBSCRIPTION_REQUIRED',
                $blocked
                    ? 'Posting is disabled for this account. Please contact support.'
                    : 'No subscription listing slots remain. Choose pay per listing or renew your plan.',
                $blocked ? 403 : 402
            );
        }

        $this->mail->pendingAdToAdmin($post->load('user'));

        return $this->created(new AdDetailResource(
            $this->attachBundleItems($post->load(['category', 'subCategory', 'user', 'customData']))
        ));
    }

    /**
     * True when the user holds an unexpired plan and still has quota.
     *
     * @deprecated Use PostingPolicy::canPostFree() — kept for callers that
     * still reference the old helper.
     */
    private function canPostFree($user): bool
    {
        return PostingPolicy::canPostFree($user);
    }

    /**
     * Listing-type switch state. ON unless the stored option is exactly
     * `'0'`; missing keys default to ON so old installs keep working.
     */
    private static function listingTypeEnabled(string $key): bool
    {
        return (string) (Option::where('option_name', $key)->value('option_value') ?? '1') !== '0';
    }

    public function update(int $id, UpdateAdRequest $request)
    {
        $post = Post::findOrFail($id);
        $this->authorize('update', $post);
        $post = $this->svc->update($post, $request->validated(), (array) $request->file('images', []));
        $this->mail->pendingAdToAdmin($post->load('user'));

        return $this->ok(new AdDetailResource(
            $this->attachBundleItems($post->load(['category', 'subCategory', 'user', 'customData']))
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
        $maxImageKb = (int) config('quickad.ads.max_image_kb', 25600);
        $request->validate(['images' => ['required', 'array', 'min:1', 'max:4'], 'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', "max:{$maxImageKb}"]]);
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
            'hide' => $post->forceFill(['hide' => '1', 'updated_at' => now()])->save(),
            'unhide' => $post->forceFill(['hide' => '0', 'updated_at' => now()])->save(),
            'resubmit' => $post->forceFill(['status' => 'pending', 'hide' => '0', 'updated_at' => now(),
                'duration_days' => $days = (int) ($request->input('duration_days', 30) ?? 30),
                'expire_date' => now()->addDays($days)->timestamp])->save(),
            'sold-out' => $post->forceFill(['status' => 'sold_out',  'updated_at' => now()])->save(),
            'restock' => $post->forceFill(['status' => 'active',    'updated_at' => now()])->save(),
            'remove' => $post->forceFill(['status' => 'removed', 'hide' => '1', 'updated_at' => now()])->save(),
            'publish' => $post->forceFill(['status' => 'pending', 'updated_at' => now()])->save(),
            default => abort(422, 'Unknown action.'),
        };

        // Revalidate the frontend homepage so visibility changes (hide,
        // unhide, restock, sold-out, remove) reflect immediately.
        if (in_array($action, ['hide', 'unhide', 'restock', 'sold-out', 'remove'], true)) {
            RevalidateFrontendJob::dispatch();
        }

        return $this->ok(new AdDetailResource($post->fresh()));
    }

    public function mine(Request $request)
    {
        $q = Post::query()->where('user_id', $request->user()->id);

        // ?status=all|active|pending|sold_out|removed|draft|expire|hidden
        match ((string) $request->query('status', '')) {
            'active' => $q->where('status', 'active')->where('hide', '0'),
            'pending' => $q->where('status', 'pending'),
            'expire' => $q->where('status', 'expire'),
            'sold_out' => $q->where('status', 'sold_out'),
            'removed' => $q->where('status', 'removed'),
            'draft' => $q->where('status', 'draft'),
            'hidden' => $q->where('hide', '1'),
            default => null,
        };

        // ?condition=new|used
        if ($c = $request->query('condition')) {
            $q->where('condition', $c);
        }

        $q->with(['category', 'subCategory'])->orderByDesc('id');
        $rows = $q->paginate($this->perPage($request, 12));
        $rows->getCollection()->map(fn (Post $p) => $this->attachBundleItems($p));

        return $this->ok(AdResource::collection($rows));
    }

    /** Preload a post's bundle members so resources can render items. */
    private function attachBundleItems(Post $post): Post
    {
        if ($post->bundle_items) {
            $post->setRelation('bundleItems', Post::whereIn('id', $post->bundle_items)->get());
        }

        return $post;
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

        $rows = Favourite::with(['user:id,username,name,image', 'post:id,product_name,slug,price,screen_shot'])
            ->whereIn('product_id', $mineIds)
            ->orderByDesc('id')
            ->paginate($this->perPage($request, 20));

        return $this->ok($rows);
    }

    /* --------------------------------------------------------------- */

}
