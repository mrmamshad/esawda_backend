<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;

/**
 * Admin CRUD for source-code download licenses.
 * Mounted under the auth:sanctum + admin route group.
 */
class LicenseAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = License::query()->withCount('events')->orderByDesc('id');

        if ($term = trim((string) $request->query('q', ''))) {
            $q->where(function ($w) use ($term) {
                $w->where('key', 'like', "%{$term}%")
                    ->orWhere('buyer_email', 'like', "%{$term}%")
                    ->orWhere('buyer_name', 'like', "%{$term}%");
            });
        }
        if ($status = $request->query('status')) {
            $q->where('status', $status);
        }

        $rows = $q->paginate((int) min(100, max(1, (int) $request->query('per_page', 20))));

        return $this->ok($rows);
    }

    public function show(int $id)
    {
        $license = License::withCount('events')->findOrFail($id);
        $license->setAttribute(
            'recent_events',
            $license->events()->orderByDesc('downloaded_at')->limit(20)->get()
        );

        return $this->ok($license);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'buyer_name' => ['nullable', 'string', 'max:255'],
            'buyer_email' => ['required', 'email', 'max:255'],
            'product_version' => ['nullable', 'string', 'max:32'],
            'max_downloads' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $license = License::create([
            'key' => License::generateKey(),
            'buyer_name' => $data['buyer_name'] ?? null,
            'buyer_email' => $data['buyer_email'],
            'product_version' => $data['product_version'] ?? config('download.version', 'latest'),
            'max_downloads' => $data['max_downloads'] ?? 5,
            'expires_at' => $data['expires_at'] ?? null,
            'status' => 'active',
        ]);

        return $this->created($license);
    }

    public function update(Request $request, int $id)
    {
        $license = License::findOrFail($id);

        $data = $request->validate([
            'buyer_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'buyer_email' => ['sometimes', 'email', 'max:255'],
            'product_version' => ['sometimes', 'nullable', 'string', 'max:32'],
            'max_downloads' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'status' => ['sometimes', 'in:active,revoked'],
            'expires_at' => ['sometimes', 'nullable', 'date'],
        ]);

        $license->fill($data)->save();

        return $this->ok($license->fresh());
    }

    /** Convenience toggle: revoke a license immediately. */
    public function revoke(int $id)
    {
        $license = License::findOrFail($id);
        $license->update(['status' => 'revoked']);

        return $this->ok($license);
    }

    public function destroy(int $id)
    {
        License::findOrFail($id)->delete();

        return $this->noContent();
    }
}
