<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductExpirationTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_expire_after_3_days()
    {
        // Create a fisherman
        $fisherman = User::factory()->create(['user_type' => 'fisherman']);
        $category = ProductCategory::factory()->create(['name' => 'Fish']);

        // Create an old product (4 days ago)
        $oldProduct = Product::create([
            'supplier_id' => $fisherman->id,
            'category_id' => $category->id,
            'name' => 'Old Fish',
            'description' => 'Very old',
            'unit_price' => 100,
            'available_quantity' => 10,
            'status' => 'active',
        ]);
        // Manually update created_at to simulate old product
        $oldProduct->created_at = now()->subDays(4);
        $oldProduct->save();

        // Create a new product (1 day ago)
        $newProduct = Product::create([
            'supplier_id' => $fisherman->id,
            'category_id' => $category->id,
            'name' => 'Fresh Fish',
            'description' => 'Very fresh',
            'unit_price' => 100,
            'available_quantity' => 10,
            'status' => 'active',
        ]);
        $newProduct->created_at = now()->subDays(1);
        $newProduct->save();

        // Run the expiration command
        $this->artisan('products:expire')
             ->assertExitCode(0);

        // Verify old product is expired
        $this->assertEquals('expired', $oldProduct->fresh()->status);

        // Verify new product is still active
        $this->assertEquals('active', $newProduct->fresh()->status);
    }

    public function test_vendor_dashboard_filters_expired_products()
    {
        $fisherman = User::factory()->create(['user_type' => 'fisherman']);
        $vendor = User::factory()->create(['user_type' => 'vendor']);
        
        // Create vendor preferences to bypass onboarding middleware
        \App\Models\VendorPreference::create([
            'user_id' => $vendor->id,
            'onboarding_completed_at' => now(),
            'notify_on' => 'all',
        ]);

        $category = ProductCategory::factory()->create(['name' => 'Fish']);

        // Create expired product
        $expired = Product::create([
            'supplier_id' => $fisherman->id,
            'category_id' => $category->id,
            'name' => 'Expired Fish',
            'status' => 'expired',
            'unit_price' => 100,
            'available_quantity' => 10,
        ]);
        $expired->created_at = now()->subDays(4);
        $expired->save();

        // Create active product
        $active = Product::create([
            'supplier_id' => $fisherman->id,
            'category_id' => $category->id,
            'name' => 'Active Fish',
            'status' => 'active',
            'unit_price' => 100,
            'available_quantity' => 10,
        ]);
        $active->created_at = now()->subDays(1);
        $active->save();

        $response = $this->actingAs($vendor)->get(route('vendor.products.index'));

        $response->assertOk();
        $response->assertSee('Active Fish');
        $response->assertDontSee('Expired Fish');
    }
}
