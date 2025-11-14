<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\MarketplaceListing;
use Illuminate\Support\Facades\Hash;

class FreshnessTestSeeder extends Seeder
{
    /**
     * Seed marketplace listings with various freshness levels for testing.
     */
    public function run(): void
    {
        // Create test users if they don't exist
        $fisherman = User::firstOrCreate(
            ['email' => 'fisherman@test.com'],
            [
                'username' => 'test_fisherman',
                'password' => Hash::make('password'),
                'user_type' => 'fisherman',
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subMinutes(2), // Online
            ]
        );

        $fisherman2 = User::firstOrCreate(
            ['email' => 'fisherman2@test.com'],
            [
                'username' => 'offline_fisherman',
                'password' => Hash::make('password'),
                'user_type' => 'fisherman',
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subHours(2), // Offline
            ]
        );

        $vendor = User::firstOrCreate(
            ['email' => 'vendor@test.com'],
            [
                'username' => 'test_vendor',
                'password' => Hash::make('password'),
                'user_type' => 'vendor',
                'status' => 'active',
                'is_active' => true,
                'last_seen_at' => now()->subMinutes(3), // Online
            ]
        );

        // Get or create fish category
        $fishCategory = ProductCategory::firstOrCreate(
            ['name' => 'Fish'],
            ['description' => 'Fresh seafood']
        );

        // Create products with different freshness scenarios
        $scenarios = [
            [
                'name' => 'Fresh Tuna Catch',
                'hours_ago' => 2,
                'expected' => 'Fresh',
                'supplier' => $fisherman,
            ],
            [
                'name' => 'Morning Mackerel',
                'hours_ago' => 8,
                'expected' => 'Good',
                'supplier' => $fisherman,
            ],
            [
                'name' => 'Yesterday\'s Sardines',
                'hours_ago' => 18,
                'expected' => 'Aging',
                'supplier' => $fisherman2,
            ],
            [
                'name' => 'Day-Old Bangus',
                'hours_ago' => 26,
                'expected' => 'Stale',
                'supplier' => $fisherman2,
            ],
            [
                'name' => 'Old Stock Tilapia',
                'hours_ago' => 30,
                'expected' => 'Spoiled',
                'supplier' => $fisherman2,
            ],
        ];

        foreach ($scenarios as $scenario) {
            $product = Product::create([
                'supplier_id' => $scenario['supplier']->id,
                'category_id' => $fishCategory->id,
                'name' => $scenario['name'],
                'description' => 'Test product for ' . $scenario['expected'] . ' freshness',
                'freshness_metric' => rand(70, 95),
                'unit_price' => rand(200, 800),
                'available_quantity' => rand(5, 50),
                'seasonality_factor' => rand(8, 12) / 10,
                'quality_rating' => rand(3, 5),
            ]);

            // Create marketplace listing with specific timestamp
            $listingDate = now()->subHours($scenario['hours_ago']);
            
            MarketplaceListing::create([
                'product_id' => $product->id,
                'seller_id' => $scenario['supplier']->id,
                'asking_price' => $product->unit_price * 1.2,
                'suggested_price' => $product->unit_price * 1.15,
                'demand_factor' => rand(8, 15) / 10,
                'freshness_score' => max(10, 100 - ($scenario['hours_ago'] * 3)),
                'listing_date' => $listingDate,
                'status' => $scenario['expected'] === 'Spoiled' ? 'inactive' : 'active',
                'freshness_level' => null, // Will be computed
                'unlisted_at' => $scenario['expected'] === 'Spoiled' ? now() : null,
            ]);

            $this->command->info("Created: {$scenario['name']} (listed {$scenario['hours_ago']}h ago, expecting {$scenario['expected']})");
        }

        $this->command->info('Running freshness update command...');
        \Artisan::call('fish:freshness-update');
        $this->command->info(\Artisan::output());
    }
}
