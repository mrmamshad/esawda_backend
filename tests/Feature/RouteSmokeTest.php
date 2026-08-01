<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Post-migration smoke tests. The Blade storefront routes were commented
 * out on 2026-07-22 in favour of the Next.js frontend hitting /api/v1.
 * These tests lock in the new surface:
 *
 *   - / returns an API-only JSON stub
 *   - /sitemap.xml is preserved for SEO continuity
 *   - Legacy Blade routes now return 404
 *   - /api/v1 endpoints are reachable and return the standard envelope
 */
class RouteSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_returns_api_stub_json(): void
    {
        $this->getJson('/')
            ->assertOk()
            ->assertJsonStructure(['name', 'status', 'api', 'admin']);
    }

    public function test_sitemap_still_served(): void
    {
        $status = $this->get('/sitemap.xml')->getStatusCode();
        $this->assertLessThan(500, $status, 'sitemap.xml unexpectedly errored');
    }

    public function test_legacy_blade_routes_are_gone(): void
    {
        foreach ([
            '/login', '/signup', '/forgot', '/dashboard', '/myads',
            '/message', '/account-setting', '/blog', '/faq', '/contact',
            '/countries', '/testimonials', '/advertise-here', '/membership',
            '/transaction', '/listing',
        ] as $p) {
            $this->get($p)->assertStatus(404);
        }
    }

    public function test_public_api_v1_endpoints_reachable(): void
    {
        // Ads listing works without auth and returns Laravel's default
        // pagination shape from the legacy stub controller (v1 rewrite in 4d).
        $this->getJson('/api/v1/ads')->assertStatus(200);
        $this->getJson('/api/v1/categories')->assertStatus(200);
    }

    public function test_protected_api_v1_endpoints_require_auth(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }
}
