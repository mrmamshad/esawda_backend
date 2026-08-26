<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ChangePasswordRequest;
use App\Http\Requests\V1\UpdateProfileRequest;
use App\Http\Resources\V1\TransactionResource;
use App\Http\Resources\V1\UserResource;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * /api/v1/me/*  (authenticated)
 *
 * Handles account-level self-service: profile edit, password change and the
 * billing / transaction ledger surfaced under the Dashboard.
 */
class AccountController extends Controller
{
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $user->forceFill($request->validated() + ['updated_at' => now()])->save();

        return $this->ok(['user' => (new UserResource($user))->resolve()]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->string('current_password'), (string) $user->password_hash)) {
            return $this->error('INVALID_PASSWORD', 'Current password is incorrect.', 422,
                ['current_password' => ['Current password is incorrect.']]);
        }

        $user->forceFill([
            'password_hash' => Hash::make($request->string('password')),
            'updated_at' => now(),
        ])->save();

        // Revoke all *other* tokens; keep the caller's active session alive.
        $current = $request->user()->currentAccessToken();
        $user->tokens()->where('id', '!=', $current?->id)->delete();

        return $this->ok(['message' => 'Password updated.']);
    }

    public function transactions(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = max(1, min(50, (int) $request->query('per_page', 20)));

        $q = Transaction::query()
            ->where('seller_id', $userId)
            ->orderByDesc('id');

        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        return $this->ok(TransactionResource::collection($q->paginate($perPage)));
    }

    /**
     * GET /me/purchases — buyer-side order history for the lightweight
     * /dashboard. Only orders where this user is the buyer.
     */
    public function purchases(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = max(1, min(50, (int) $request->query('per_page', 20)));

        $q = Order::query()
            ->with(['product:id,product_name,slug,price,screen_shot', 'seller:id,username,name,image', 'transaction:id,status,amount,created_at'])
            ->where('buyer_id', $userId)
            ->orderByDesc('id');

        if ($status = $request->query('status')) {
            $q->where('shipping_status', $status);
        }

        $orders = $q->paginate($perPage);

        // Legacy `product.screen_shot` is a JSON array / comma list of
        // filenames. Flatten to a single absolute image URL the dashboard
        // can put straight in <img src>.
        $base = rtrim(config('app.url'), '/').'/storage/products/';
        $orders->getCollection()->transform(function (Order $order) use ($base) {
            $raw = $order->product?->screen_shot ?? null;
            $names = is_array($raw) ? $raw : (json_decode((string) $raw, true) ?: preg_split('/[,;\s]+/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY));
            $first = collect($names)->map(fn ($n) => trim((string) $n))
                ->filter(fn ($n) => $n !== '' && $n !== '[]' && $n !== '{}')
                ->first();
            $order->setAttribute('product_image',
                $first ? (preg_match('~^https?://~i', $first) ? $first : $base.ltrim($first, '/')) : null);

            return $order;
        });

        return $this->ok($orders);
    }

    /**
     * GET /me/orders — seller-side order history for the shop panel
     * (/shop/orders). Only orders where this user is the seller, newest
     * first, with shipping status + payment state for the table.
     */
    public function orders(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = max(1, min(50, (int) $request->query('per_page', 20)));

        $q = Order::query()
            ->with(['product:id,product_name,slug,price,screen_shot', 'buyer:id,username,name,email', 'transaction:id,status,amount,created_at'])
            ->where('seller_id', $userId)
            ->orderByDesc('id');

        if ($status = $request->query('status')) {
            $q->where('shipping_status', $status);
        }

        $orders = $q->paginate($perPage);

        $base = rtrim(config('app.url'), '/').'/storage/products/';
        $orders->getCollection()->transform(function (Order $order) use ($base) {
            $raw = $order->product?->screen_shot ?? null;
            $names = is_array($raw) ? $raw : (json_decode((string) $raw, true) ?: preg_split('/[,;\s]+/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY));
            $first = collect($names)->map(fn ($n) => trim((string) $n))
                ->filter(fn ($n) => $n !== '' && $n !== '[]' && $n !== '{}')
                ->first();
            $order->setAttribute('product_image',
                $first ? (preg_match('~^https?://~i', $first) ? $first : $base.ltrim($first, '/')) : null);

            return $order;
        });

        return $this->ok($orders);
    }

    /**
     * POST /me/avatar  (multipart)  { avatar: file }
     *
     * Stores the file on the public disk under profile/ and updates the
     * user's `image` column. Returns the fresh UserResource so the frontend
     * can swap the header/dropdown avatar without another fetch.
     */
    public function uploadAvatar(Request $request)
    {
        $data = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();
        // Store inside profile/; keep the bare filename in DB — resources
        // already prepend /storage/profile/ (storing "profile/…" in DB
        // double-prefixed and 404'd; storing at disk root 404'd too).
        $name = Str::random(32).'.'.$data['avatar']->getClientOriginalExtension();
        $data['avatar']->storeAs('profile', $name, 'public');

        // Best-effort cleanup of the previous file (skip default seeds).
        if ($user->image && !str_starts_with($user->image, 'default_')) {
            Storage::disk('public')->delete('profile/'.$user->image);
        }

        $user->forceFill([
            'image' => $name,
            'updated_at' => now(),
        ])->save();

        return response()->json([
            'data' => ['user' => (new UserResource($user))->resolve()],
        ]);
    }

    /**
     * POST /me/cover  (multipart)  { cover: file }
     *
     * Cover banner for the shop's public store page. Same storage pattern as
     * the avatar — profile/ on the public disk, mirroring the /storage/profile/
     * base every resource hardcodes.
     */
    public function uploadCover(Request $request)
    {
        $data = $request->validate([
            'cover' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $name = Str::random(32).'.'.$data['cover']->getClientOriginalExtension();
        // Store inside the profile/ folder; keep only the bare filename in DB
        // — resources already prepend /storage/profile/.
        $data['cover']->storeAs('profile', $name, 'public');

        if ($user->cover && !str_starts_with($user->cover, 'default_')) {
            Storage::disk('public')->delete('profile/'.$user->cover);
        }

        $user->forceFill([
            'cover' => $name,
            'updated_at' => now(),
        ])->save();

        return response()->json([
            'data' => ['user' => (new UserResource($user))->resolve()],
        ]);
    }

    /**
     * POST /me/shop-banner  (multipart)  { banner: file }
     *
     * Wide promotional banner shown at the top of the shop's public store
     * page and on the shop dashboard. Same storage pattern as avatar/cover —
     * profile/ on the public disk, bare filename in DB.
     */
    public function uploadShopBanner(Request $request)
    {
        $data = $request->validate([
            'banner' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $name = Str::random(32).'.'.$data['banner']->getClientOriginalExtension();
        $data['banner']->storeAs('profile', $name, 'public');

        if ($user->shop_banner) {
            Storage::disk('public')->delete('profile/'.$user->shop_banner);
        }

        $user->forceFill([
            'shop_banner' => $name,
            'updated_at' => now(),
        ])->save();

        return response()->json([
            'data' => ['user' => (new UserResource($user))->resolve()],
        ]);
    }
}
