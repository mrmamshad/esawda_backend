<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ChangePasswordRequest;
use App\Http\Requests\V1\UpdateProfileRequest;
use App\Http\Resources\V1\TransactionResource;
use App\Http\Resources\V1\UserResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

        if (! Hash::check($request->string('current_password'), (string) $user->password_hash)) {
            return $this->error('INVALID_PASSWORD', 'Current password is incorrect.', 422,
                ['current_password' => ['Current password is incorrect.']]);
        }

        $user->forceFill([
            'password_hash' => Hash::make($request->string('password')),
            'updated_at'    => now(),
        ])->save();

        // Revoke all *other* tokens; keep the caller's active session alive.
        $current = $request->user()->currentAccessToken();
        $user->tokens()->where('id', '!=', $current?->id)->delete();

        return $this->ok(['message' => 'Password updated.']);
    }

    public function transactions(Request $request)
    {
        $userId  = $request->user()->id;
        $perPage = max(1, min(50, (int) $request->query('per_page', 20)));

        $q = Transaction::query()
            ->where('seller_id', $userId)
            ->orderByDesc('id');

        if ($status = $request->query('status')) $q->where('status', $status);

        return $this->ok(TransactionResource::collection($q->paginate($perPage)));
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
        // Store under profile/ so the web URL /storage/profile/<file> matches
        // the base every UserResource/SellerResource hardcodes. (Previously
        // uploaded to users/avatars/, which 404'd against that base.)
        $path = $data['avatar']->store('profile', 'public');

        // Best-effort cleanup of the previous file (skip default seeds).
        if ($user->image && ! str_starts_with($user->image, 'default_')) {
            Storage::disk('public')->delete($user->image);
        }

        $user->forceFill([
            'image'      => $path,
            'updated_at' => now(),
        ])->save();

        return response()->json([
            'data' => ['user' => (new UserResource($user))->resolve()],
        ]);
    }
}
