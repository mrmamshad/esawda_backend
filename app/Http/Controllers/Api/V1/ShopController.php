<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Services\Mail\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Shop-owner onboarding + subscription helpers.
 *
 *   POST /api/v1/me/shop/apply   — open a shop (Bikroy-style application:
 *                                  name/contact + shop details + documents).
 *                                  Completes instantly; `user_type` flips to
 *                                  `seller` so the /shop corporate panel
 *                                  unlocks immediately. No admin approval.
 *   GET  /api/v1/me/shop/status   — subscription snapshot for the panel UI.
 */
class ShopController extends Controller
{
    public function __construct(
        private readonly MailService $mail,
    ) {}

    /**
     * POST /me/shop/apply (multipart)
     *
     * Opens the shop panel instantly. Documents are stored for the record
     * (the platform may review them later); they never block access.
     */
    public function apply(Request $request)
    {
        $data = $request->validate([
            'owner_name' => ['required', 'string', 'max:150'],
            'owner_phone' => ['required', 'string', 'max:30'],
            'shop_name' => ['required', 'string', 'max:191'],
            'shop_address' => ['required', 'string', 'max:500'],
            'shop_category' => ['nullable', 'string', 'max:100'],
            'shop_description' => ['nullable', 'string', 'max:2000'],
            'documents' => ['nullable', 'array'],
            'documents.nid' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'documents.trade_licence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();

        // One shop per account — re-applying would silently overwrite the
        // existing shop identity, so stop with a clear message instead.
        if ($user->isShop()) {
            return $this->error('SHOP_ALREADY_OPEN', 'This account already has a shop.', 409);
        }

        // Store each verification document under its own labelled key so the
        // record is self-describing (e.g. { nid: ..., trade_licence: ... }).
        // Documents are optional at apply time — only store what was sent.
        $docPaths = [];
        foreach (['nid', 'trade_licence'] as $type) {
            if (isset($data['documents'][$type])) {
                $docPaths[$type] = $data['documents'][$type]->store('shop-documents/'.$user->id, 'public');
            }
        }

        $fill = [
            'user_type' => 'seller',
            'name' => $data['owner_name'],
            'phone' => $data['owner_phone'],
            // No username field on the form — the shop name IS the public
            // username. Derived here so every client (old or new) ends up
            // with the same unique, URL-safe value.
            'username' => self::uniqueUsernameFromShopName($data['shop_name'], (int) $user->id),
            'shop_name' => $data['shop_name'],
            'shop_category' => $data['shop_category'] ?? null,
            'shop_address' => $data['shop_address'],
            'shop_description' => $data['shop_description'] ?? null,
            'shop_documents' => $docPaths,
            // Shop owners must hold a paid subscription before they can list —
            // there is no free trial. Any complimentary guest quota granted at
            // signup is cleared the moment the account becomes a seller so the
            // /shop panel starts on the "choose a plan" gate. (Regular single
            // users keep their free quota; only sellers are gated here.)
            'plan_id' => null,
            'plan_expires_at' => null,
            'ads_remaining' => 0,
            'updated_at' => now(),
        ];

        // Optional profile picture / cover / banner — same pattern as the
        // dedicated upload endpoints, but bundled so the apply flow is one
        // round-trip.
        foreach (['avatar' => 'image', 'cover' => 'cover', 'banner' => 'shop_banner'] as $input => $column) {
            if ($request->hasFile($input)) {
                $name = Str::random(32).'.'.$data[$input]->getClientOriginalExtension();
                $data[$input]->storeAs('profile', $name, 'public');
                $fill[$column] = $name;
            }
        }

        $user->forceFill($fill)->save();

        $this->mail->shopOpenedToSeller($user);
        $this->mail->newShopToAdmin($user);

        return $this->ok([
            'message' => 'Your shop is open.',
            'user' => (new UserResource($user))->resolve(),
        ]);
    }

    /**
     * URL-safe username from a shop name. Matches the `user.username`
     * rules (letters/digits/dot/dash/underscore, 3–40 chars, unique):
     * non-latin or too-short names fall back to `shop_{id}`, and taken
     * names get a numeric suffix (`my_shop_2`).
     */
    private static function uniqueUsernameFromShopName(string $shopName, int $ignoreUserId): string
    {
        $base = (string) Str::slug($shopName, '_');
        $base = (string) preg_replace('/[^A-Za-z0-9_.-]+/', '', $base);
        $base = trim($base, '._-');
        if (strlen($base) < 3) {
            $base = 'shop_'.$ignoreUserId;
        }
        $base = substr($base, 0, 32);

        $candidate = $base;
        $i = 1;
        while (User::where('username', $candidate)->where('id', '!=', $ignoreUserId)->exists()) {
            $i++;
            $suffix = '_'.$i;
            $candidate = substr($base, 0, 40 - strlen($suffix)).$suffix;
        }

        return $candidate;
    }

    /**
     * GET /me/shop/status — subscription + shop snapshot.
     */
    public function status(Request $request)
    {
        $user = $request->user();

        $active = !empty($user->plan_expires_at)
            && $user->plan_expires_at->isFuture();

        return $this->ok([
            'is_shop' => $user->isShop(),
            'shop_name' => $user->shop_name,
            'shop_verified' => !empty($user->shop_verified_at),
            'plan_active' => $active,
            'plan_name' => $user->group_id ?? 'free',
            'plan_expires_at' => optional($user->plan_expires_at)->toIso8601String(),
            'ads_remaining' => (int) $user->ads_remaining,
        ]);
    }
}
