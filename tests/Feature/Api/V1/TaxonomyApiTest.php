<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use App\Models\SubCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyApiTest extends TestCase
{
    use RefreshDatabase;

    private function seedCore(): void
    {
        Category::create(['cat_id' => 1, 'cat_order' => 1, 'cat_name' => 'Vehicles',  'slug' => 'vehicles',  'icon' => 'fa-car']);
        Category::create(['cat_id' => 2, 'cat_order' => 2, 'cat_name' => 'Mobiles',   'slug' => 'mobiles',   'icon' => 'fa-mobile']);
        SubCategory::create(['sub_cat_id' => 10, 'main_cat_id' => 1, 'cat_order' => 1, 'sub_cat_name' => 'Cars',  'slug' => 'cars']);
        SubCategory::create(['sub_cat_id' => 11, 'main_cat_id' => 1, 'cat_order' => 2, 'sub_cat_name' => 'Bikes', 'slug' => 'bikes']);
        Country::create(['id' => 1, 'code' => 'BD', 'iso3' => 'BGD', 'name' => 'Bangladesh', 'active' => 1]);
        City::create(['id' => 1, 'country_code' => 'BD', 'name' => 'Dhaka',     'active' => 1]);
        City::create(['id' => 2, 'country_code' => 'BD', 'name' => 'Chittagong','active' => 1]);
        Currency::create(['id' => 1, 'code' => 'USD', 'name' => 'US Dollar', 'html_entity' => '$', 'in_left' => 1, 'decimal_places' => 2]);
        Language::create(['id' => 1, 'code' => 'en',  'name' => 'English', 'active' => 1, 'default' => 1]);
    }

    public function test_categories_endpoint_returns_wrapped_tree(): void
    {
        $this->seedCore();
        $res = $this->getJson('/api/v1/categories')->assertOk();
        $res->assertJsonStructure(['data' => [['id','name','slug','sub_categories']]]);
        $this->assertSame('Vehicles', $res->json('data.0.name'));
        $this->assertCount(2, $res->json('data.0.sub_categories'));
    }

    public function test_single_category_by_slug(): void
    {
        $this->seedCore();
        $this->getJson('/api/v1/categories/vehicles')
            ->assertOk()
            ->assertJsonPath('data.slug', 'vehicles')
            ->assertJsonCount(2, 'data.sub_categories');
    }

    public function test_subcategories_scoped_by_category(): void
    {
        $this->seedCore();
        $this->getJson('/api/v1/subcategories?category=1')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_countries_and_cities(): void
    {
        $this->seedCore();
        $this->getJson('/api/v1/countries')
            ->assertOk()
            ->assertJsonPath('data.0.code', 'BD');

        $this->getJson('/api/v1/countries/BD/cities')
            ->assertOk()
            ->assertJsonStructure(['data', 'meta' => ['current_page','total']])
            ->assertJsonCount(2, 'data');
    }

    public function test_currencies_languages_settings(): void
    {
        $this->seedCore();
        $this->getJson('/api/v1/currencies')->assertOk()->assertJsonPath('data.0.code', 'USD');
        $this->getJson('/api/v1/languages') ->assertOk()->assertJsonPath('data.0.code', 'en');
        $this->getJson('/api/v1/settings')  ->assertOk()->assertJsonPath('data.settings.site_name', config('app.name'));
    }
}
