<?php

namespace Tests\Feature;

use App\Models\License;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SourceDownloadTest extends TestCase
{
    use RefreshDatabase;

    private function makePackage(): string
    {
        $dir = storage_path('app/releases');
        File::ensureDirectoryExists($dir);
        $file = $dir.'/'.config('download.name').'-v'.config('download.version').'.zip';
        File::put($file, 'PK-fake-zip-content');

        return $file;
    }

    public function test_validate_returns_signed_url_for_a_good_license(): void
    {
        $this->makePackage();
        $license = License::create([
            'key' => 'ESW-AAAA-BBBB-CCCC',
            'buyer_email' => 'buyer@example.com',
            'max_downloads' => 3,
        ]);

        $res = $this->postJson('/api/v1/download/validate', [
            'key' => $license->key,
            'email' => 'buyer@example.com',
        ]);

        $res->assertOk()
            ->assertJsonPath('data.downloads_remaining', 3)
            ->assertJsonStructure(['data' => ['download_url', 'version', 'available']]);
    }

    public function test_validate_rejects_wrong_email(): void
    {
        License::create(['key' => 'ESW-AAAA-BBBB-CCCC', 'buyer_email' => 'buyer@example.com']);

        $this->postJson('/api/v1/download/validate', [
            'key' => 'ESW-AAAA-BBBB-CCCC',
            'email' => 'wrong@example.com',
        ])->assertStatus(404);
    }

    public function test_revoked_license_cannot_download(): void
    {
        License::create([
            'key' => 'ESW-DEAD-DEAD-DEAD',
            'buyer_email' => 'x@example.com',
            'status' => 'revoked',
        ]);

        $this->postJson('/api/v1/download/validate', [
            'key' => 'ESW-DEAD-DEAD-DEAD',
            'email' => 'x@example.com',
        ])->assertStatus(403);
    }

    public function test_signed_file_route_serves_and_decrements_quota(): void
    {
        $this->makePackage();
        $license = License::create([
            'key' => 'ESW-1111-2222-3333',
            'buyer_email' => 'buyer@example.com',
            'max_downloads' => 2,
        ]);

        $url = URL::temporarySignedRoute('download.file', now()->addMinutes(10), ['license' => $license->id]);

        $this->get($url)->assertOk();

        $this->assertSame(1, $license->fresh()->downloads_used);
        $this->assertSame(1, $license->events()->count());
    }

    public function test_unsigned_file_route_is_forbidden(): void
    {
        $license = License::create(['key' => 'ESW-9999-9999-9999', 'buyer_email' => 'b@example.com']);

        $this->get('/api/v1/download/file/'.$license->id)->assertStatus(403);
    }
}
