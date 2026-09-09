<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PremiumUpgradeAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['user_type' => 'admin']);
    }

    public function test_index_reports_defaults_when_nothing_saved(): void
    {
        $this->actingAs($this->admin())
            ->getJson('/api/v1/admin/premium-upgrades')
            ->assertOk()
            ->assertJson([
                'data' => [
                    'prices' => ['featured' => 200, 'urgent' => 150, 'highlight' => 100],
                ],
            ]);
    }

    public function test_update_saves_prices_and_index_reports_them(): void
    {
        $this->actingAs($this->admin())
            ->putJson('/api/v1/admin/premium-upgrades', [
                'featured' => 250,
                'urgent' => 175,
                'highlight' => 0,
            ])
            ->assertOk()
            ->assertJsonPath('data.prices.featured', 250)
            ->assertJsonPath('data.prices.urgent', 175)
            ->assertJsonPath('data.prices.highlight', 0);

        $this->assertDatabaseHas('options', ['option_name' => 'upgrade_featured_price', 'option_value' => '250']);
        $this->assertDatabaseHas('options', ['option_name' => 'upgrade_highlight_price', 'option_value' => '0']);
    }

    public function test_blank_values_keep_existing_prices(): void
    {
        Option::updateOrCreate(['option_name' => 'upgrade_featured_price'], ['option_value' => '300']);

        $this->actingAs($this->admin())
            ->putJson('/api/v1/admin/premium-upgrades', ['featured' => ''])
            ->assertOk()
            ->assertJsonPath('data.prices.featured', 300);
    }

    public function test_negative_or_non_numeric_prices_are_rejected(): void
    {
        $this->actingAs($this->admin())
            ->putJson('/api/v1/admin/premium-upgrades', ['featured' => -5])
            ->assertStatus(422);

        $this->actingAs($this->admin())
            ->putJson('/api/v1/admin/premium-upgrades', ['urgent' => 'free'])
            ->assertStatus(422);
    }

    public function test_non_admins_are_forbidden(): void
    {
        $this->actingAs(User::factory()->create(['user_type' => 'seller']))
            ->getJson('/api/v1/admin/premium-upgrades')
            ->assertForbidden();
    }
}
