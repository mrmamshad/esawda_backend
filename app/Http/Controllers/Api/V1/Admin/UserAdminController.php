<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();

        if ($s = trim((string) $request->query('q', ''))) {
            $q->where(function ($sub) use ($s) {
                $sub->where('username', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('name', 'like', "%{$s}%");
            });
        }
        if ($type = $request->query('user_type')) {
            $q->where('user_type', $type);
        }
        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        $q->orderByDesc('id');

        return $this->ok($q->withCount([
            'posts as listings_total',
            'posts as listings_active' => fn ($a) => $a->where('status', 'active'),
            'posts as listings_pending' => fn ($a) => $a->where('status', 'pending'),
        ])->paginate((int) min(100, max(1, (int) $request->query('per_page', 20)))));
    }

    public function show(int $id)
    {
        return $this->ok(User::findOrFail($id));
    }

    public function update(int $id, Request $request)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:150'],
            'email' => ['sometimes', 'email', 'max:150'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'user_type' => ['sometimes', 'in:user,admin'],
            'group_id' => ['sometimes', 'string', 'max:60'],
            'status' => ['sometimes', 'in:0,1'],
            // Per-account posting override (also the per-shop switch):
            // inherit = global rules, free = post without subscription,
            // blocked = cannot post at all.
            'post_policy' => ['sometimes', 'in:inherit,free,blocked'],
        ]);
        $user = User::findOrFail($id);

        // Explicit, allow-listed field mapping. `user_type` is gated to the
        // enum already validated above (`in:user,admin`); `group_id`/`status`
        // are pinned to the small validated set — never a raw passthrough.
        $safe = array_intersect_key($data, array_flip(['name', 'email', 'phone', 'user_type', 'group_id', 'status', 'post_policy']));
        $user->fill($safe)->save();

        return $this->ok($user->fresh());
    }

    public function ban(int $id)
    {
        $user = User::findOrFail($id);
        $user->forceFill(['status' => '0', 'updated_at' => now()])->save();

        return $this->ok(['message' => 'User banned.', 'user' => $user]);
    }

    public function unban(int $id)
    {
        $user = User::findOrFail($id);
        $user->forceFill(['status' => '1', 'updated_at' => now()])->save();

        return $this->ok(['message' => 'User un-banned.', 'user' => $user]);
    }

    public function verifyShop(int $id)
    {
        $user = User::findOrFail($id);
        $user->forceFill(['shop_verified_at' => now(), 'updated_at' => now()])->save();

        return $this->ok(['message' => 'Shop verified.', 'user' => $user]);
    }

    public function unverifyShop(int $id)
    {
        $user = User::findOrFail($id);
        $user->forceFill(['shop_verified_at' => null, 'updated_at' => now()])->save();

        return $this->ok(['message' => 'Shop verification removed.', 'user' => $user]);
    }

    /**
     * Shop Activate / Deactivate — controls only whether the shop appears on
     * the public Shops page (shop_status). Unlike ban, it never blocks the
     * owner from logging in.
     */
    public function activateShop(int $id)
    {
        $user = User::findOrFail($id);
        $user->forceFill(['shop_status' => 'active', 'updated_at' => now()])->save();
        Cache::forget('home.payload');

        return $this->ok(['message' => 'Shop activated.', 'user' => $user]);
    }

    public function deactivateShop(int $id)
    {
        $user = User::findOrFail($id);
        $user->forceFill(['shop_status' => 'inactive', 'updated_at' => now()])->save();
        Cache::forget('home.payload');

        return $this->ok(['message' => 'Shop deactivated.', 'user' => $user]);
    }

    /**
     * Admin-reset a shop/user password. Unlike the self-service flow, no
     * current_password is needed — the admin simply sets a new one.
     */
    public function resetPassword(int $id, Request $request)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:191'],
        ]);

        $user = User::findOrFail($id);
        $user->forceFill([
            'password_hash' => Hash::make($data['password']),
            'updated_at' => now(),
        ])->save();

        return $this->ok(['message' => 'Password updated.', 'user' => $user]);
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        try {
            $user->delete();

            return $this->ok(['message' => 'User deleted.']);
        } catch (QueryException $e) {
            // Some legacy records are referenced by FK-constrained tables
            // (e.g. orders.seller_id / buyer_id). When physical delete is
            // blocked, archive the account and release login identifiers so
            // the same email/phone can be registered again.
            $stamp = now()->format('YmdHis');
            $suffix = 'deleted_'.$user->id.'_'.$stamp;
            $username = Str::limit($suffix, 40, '');
            $placeholderEmail = Str::limit($suffix, 120, '').'@deleted.local';

            $user->tokens()->delete();
            $user->forceFill([
                'username' => $username,
                'email' => $placeholderEmail,
                'phone' => null,
                'status' => '0',
                'user_type' => 'user',
                'group_id' => 'free',
                'post_policy' => 'blocked',
                'plan_id' => null,
                'plan_expires_at' => null,
                'ads_remaining' => 0,
                'forgot' => null,
                'forgot_expires_at' => null,
                'shop_status' => 'inactive',
                'shop_verified_at' => null,
                'shop_name' => null,
                'shop_category' => null,
                'shop_address' => null,
                'shop_description' => null,
                'shop_documents' => null,
                'shop_banner' => null,
                'updated_at' => now(),
            ])->save();

            Log::warning('User hard-delete blocked; archived account and released identifiers', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return $this->ok([
                'message' => 'User archived (hard delete blocked by related records). Email/phone have been released for reuse.',
            ]);
        }
    }
}
