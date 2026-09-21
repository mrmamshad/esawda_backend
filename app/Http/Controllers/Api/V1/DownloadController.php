<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public, license-gated source-code delivery.
 *
 * Flow:
 *   1) POST /v1/download/validate  { key, email }
 *      → checks the license, returns product info + a signed, time-limited
 *        download URL (no file is served here).
 *   2) GET  /v1/download/file/{license}?signature=…&expires=…
 *      → verifies the signature + quota, records the event, increments the
 *        counter and streams the ZIP (via nginx X-Accel when configured).
 */
class DownloadController extends Controller
{
    use ApiResponses;

    /** Validate a license and hand back a signed download link. */
    public function validateLicense(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:64'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $license = License::where('key', trim($data['key']))->first();

        if (!$license || !hash_equals(
            strtolower($license->buyer_email),
            strtolower(trim($data['email']))
        )) {
            return $this->error('invalid_license', 'Invalid license key or email.', 404);
        }

        if (!$license->isValid()) {
            return $this->error('license_inactive', 'This license is inactive or has expired.', 403);
        }

        if (!$license->canDownload()) {
            return $this->error('quota_reached', 'Download limit reached for this license.', 403);
        }

        $file = $this->packageFile();
        $exists = is_file($file);

        $url = URL::temporarySignedRoute(
            'download.file',
            now()->addMinutes((int) config('download.link_ttl_minutes', 30)),
            ['license' => $license->id]
        );

        return $this->ok([
            'product' => config('download.name'),
            'version' => config('download.version'),
            'size_mb' => $exists ? round(filesize($file) / 1048576, 2) : null,
            'available' => $exists,
            'downloads_remaining' => $license->downloadsRemaining(),
            'expires_in_minutes' => (int) config('download.link_ttl_minutes', 30),
            'download_url' => $url,
        ]);
    }

    /** Serve the ZIP for a valid signed request. */
    public function file(Request $request, License $license)
    {
        // Signature is enforced by the 'signed' middleware on the route; we
        // re-check business rules here (quota may have changed).
        if (!$license->canDownload()) {
            return $this->error('link_invalid', 'This download link is no longer valid.', 403);
        }

        $file = $this->packageFile();
        if (!is_file($file)) {
            return $this->error('package_missing', 'The package is not available yet.', 404);
        }

        // Record + decrement atomically enough for our needs.
        $license->events()->create([
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
            'downloaded_at' => now(),
        ]);
        $license->increment('downloads_used');

        $downloadName = config('download.name').'-v'.config('download.version').'.zip';

        // Scale-safe path: let nginx stream the file.
        if (config('download.use_x_accel')) {
            $internal = rtrim((string) config('download.x_accel_location'), '/').'/'.basename($file);

            return response('', Response::HTTP_OK, [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
                'X-Accel-Redirect' => $internal,
            ]);
        }

        // Fallback: PHP streams it (local/dev).
        return response()->download($file, $downloadName, [
            'Content-Type' => 'application/zip',
        ])->setPrivate();
    }

    private function packageFile(): string
    {
        $name = config('download.name').'-v'.config('download.version').'.zip';

        return storage_path('app/releases/'.$name);
    }
}
