<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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
        if ($type = $request->query('user_type')) $q->where('user_type', $type);
        if ($status = $request->query('status'))  $q->where('status', $status);

        $q->orderByDesc('id');
        return $this->ok($q->paginate((int) min(100, max(1, (int) $request->query('per_page', 20)))));
    }

    public function show(int $id)
    {
        return $this->ok(User::findOrFail($id));
    }

    public function update(int $id, Request $request)
    {
        $data = $request->validate([
            'name'      => ['sometimes', 'string', 'max:150'],
            'email'     => ['sometimes', 'email', 'max:150'],
            'phone'     => ['sometimes', 'nullable', 'string', 'max:30'],
            'user_type' => ['sometimes', 'in:user,admin'],
            'group_id'  => ['sometimes', 'string', 'max:60'],
            'status'    => ['sometimes', 'in:0,1'],
        ]);
        $user = User::findOrFail($id);

        // Explicit, allow-listed field mapping. `user_type` is gated to the
        // enum already validated above (`in:user,admin`); `group_id`/`status`
        // are pinned to the small validated set — never a raw passthrough.
        $safe = array_intersect_key($data, array_flip(['name', 'email', 'phone', 'user_type', 'group_id', 'status']));
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

    public function destroy(int $id)
    {
        User::findOrFail($id)->delete();
        return $this->ok(['message' => 'User deleted.']);
    }
}
