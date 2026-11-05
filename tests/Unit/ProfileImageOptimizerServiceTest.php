<?php

namespace Tests\Unit;

use App\Services\ProfileImageOptimizerService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileImageOptimizerServiceTest extends TestCase
{
    public function test_it_downscales_an_oversized_profile_upload(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD is required to create the fixture image.');
        }

        Storage::fake('public');
        $path = Storage::disk('public')->path('profile/oversized.jpg');
        @mkdir(dirname($path), 0775, true);

        $image = imagecreatetruecolor(1200, 600);
        imagejpeg($image, $path, 90);
        imagedestroy($image);

        app(ProfileImageOptimizerService::class)->optimize('profile/oversized.jpg', 512, 512);

        $info = getimagesize($path);

        $this->assertFileExists($path);
        $this->assertIsArray($info);
        $this->assertLessThanOrEqual(512, $info[0]);
        $this->assertLessThanOrEqual(512, $info[1]);
        $this->assertSame(512, $info[0]);
        $this->assertSame(256, $info[1]);
    }

    public function test_it_leaves_an_image_that_is_already_small_enough(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD is required to create the fixture image.');
        }

        Storage::fake('public');
        $path = Storage::disk('public')->path('profile/small.jpg');
        @mkdir(dirname($path), 0775, true);

        $image = imagecreatetruecolor(120, 120);
        imagejpeg($image, $path, 90);
        imagedestroy($image);

        app(ProfileImageOptimizerService::class)->optimize('profile/small.jpg', 512, 512);

        $info = getimagesize($path);

        $this->assertIsArray($info);
        $this->assertSame(120, $info[0]);
        $this->assertSame(120, $info[1]);
    }
}
