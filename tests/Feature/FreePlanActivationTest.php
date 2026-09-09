<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreePlanActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_plan_activates_instantly_without_gateway(): void
    {
        $user = User::factory()->create(['user_type' => 'seller']);
        $plan = Plan::create([
            'name' => 'Early Bird',
            'monthly_price' => 0,
            'annual_price' => 0,
            'is_free' => 1,
            'settings' => json_encode(['ads_limit' => 20, 'featured_ads' => 5, 'duration_days' => 30]),
            'status' => 1,
            'date' => now(),
        ]);

        $this->actingAs($user)
            ->postJson("/api/v1/checkout/plan/{$plan->id}", [
                'policies_accepted' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.free_activation', true);

        $user->refresh();
        $this->assertSame('Early Bird', $user->group_id);
        $this->assertSame($plan->id, (int) $user->plan_id);
        $this->assertSame(20, (int) $user->ads_remaining);
        $this->assertTrue($user->plan_expires_at->isFuture());
        $this->assertDatabaseCount('transaction', 0);
    }

    public function test_paid_plans_still_require_gateway(): void
    {
        $user = User::factory()->create(['user_type' => 'seller']);
        $plan = Plan::create([
            'name' => 'Starter',
            'monthly_price' => 1100,
            'is_free' => 0,
            'settings' => json_encode(['ads_limit' => 10]),
            'status' => 1,
            'date' => now(),
        ]);

        $this->actingAs($user)
            ->postJson("/api/v1/checkout/plan/{$plan->id}", [
                'policies_accepted' => true,
                'payment_phone' => '01700000000',
            ])
            ->assertOk()
            ->assertJsonStructure(['data' => ['transaction_id', 'gateway_url']]);

        $this->assertDatabaseHas('transaction', ['plan_id' => $plan->id, 'status' => 'pending']);
    }

    public function test_admin_can_create_and_tune_free_plan(): void
    {
        $admin = User::factory()->create(['user_type' => 'admin']);

        $this->actingAs($admin)
            ->postJson('/api/v1/admin/plans', [
                'name' => 'Early Bird',
                'monthly_price' => 0,
                'annual_price' => 0,
                'is_free' => true,
                'ads_limit' => 20,
                'featured_ads' => 5,
                'duration_days' => 30,
            ])
            ->assertCreated();

        $plan = Plan::where('name', 'Early Bird')->firstOrFail();
        $settings = json_decode((string) $plan->settings, true);
        $this->assertSame(20, $settings['ads_limit']);
        $this->assertSame(5, $settings['featured_ads']);
        $this->assertSame(30, $settings['duration_days']);

        // Later tuning from the admin panel.
        $this->actingAs($admin)
            ->putJson("/api/v1/admin/plans/{$plan->id}", ['ads_limit' => 50])
            ->assertOk();

        $this->assertSame(50, json_decode((string) $plan->fresh()->settings, true)['ads_limit']);
    }
}
