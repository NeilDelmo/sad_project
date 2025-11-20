<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users
        $fisherman1 = User::firstOrCreate(
            ['email' => 'fisherman@test.com'],
            [
                'username' => 'fisherman1',
                'email' => 'fisherman@test.com',
                'phone' => '0922-987-6543',
                'user_type' => 'fisherman',
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'username' => 'adminuser',
                'email' => 'admin@test.com',
                'phone' => '0916-777-8888',
                'user_type' => 'admin',
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        // Create product categories
        $fishCategory = ProductCategory::firstOrCreate(
            ['name' => 'Fresh Fish'],
            ['description' => 'Fresh live fish caught from local waters']
        );

        $shellfishCategory = ProductCategory::firstOrCreate(
            ['name' => 'Shellfish'],
            ['description' => 'Fresh shellfish, crabs, shrimps, and other seafood']
        );

        $gearCategory = ProductCategory::firstOrCreate(
            ['name' => 'Fishing Gear'],
            ['description' => 'Fishing equipment and tools']
        );

        $equipmentCategory = ProductCategory::firstOrCreate(
            ['name' => 'Equipment'],
            ['description' => 'Fishing equipment and supplies']
        );

        // Create sample fish products (all live fresh fish)
        $fishProducts = [
            [
                'name' => 'Fresh Tuna',
                'description' => 'Caught this morning, premium quality live tuna',
                'unit_price' => 450.00,
                'available_quantity' => 50.00,
                'freshness_metric' => 'Very Fresh',
                'category_id' => $fishCategory->id,
                'supplier_id' => $fisherman1->id,
            ],
            [
                'name' => 'Bangus (Milkfish)',
                'description' => 'Medium size, fresh live milkfish',
                'unit_price' => 220.00,
                'available_quantity' => 100.00,
                'freshness_metric' => 'Fresh',
                'category_id' => $fishCategory->id,
                'supplier_id' => $fisherman1->id,
            ],
            [
                'name' => 'Tilapia',
                'description' => 'Live tilapia, clean and fresh',
                'unit_price' => 150.00,
                'available_quantity' => 80.00,
                'freshness_metric' => 'Fresh',
                'category_id' => $fishCategory->id,
                'supplier_id' => $fisherman1->id,
            ],
            [
                'name' => 'Red Snapper',
                'description' => 'Fresh caught red snapper',
                'unit_price' => 380.00,
                'available_quantity' => 30.00,
                'freshness_metric' => 'Very Fresh',
                'category_id' => $fishCategory->id,
                'supplier_id' => $fisherman1->id,
            ],
            [
                'name' => 'Lapu-Lapu (Grouper)',
                'description' => 'Large size, freshly caught grouper',
                'unit_price' => 420.00,
                'available_quantity' => 25.00,
                'freshness_metric' => 'Very Fresh',
                'category_id' => $fishCategory->id,
                'supplier_id' => $fisherman1->id,
            ],
        ];

        // Create sample gear/equipment products
        $gearProducts = [
            [
                'name' => 'Fishing Rod Pro',
                'description' => 'Professional grade fishing rod, 2.4m length',
                'unit_price' => 1200.00,
                'available_quantity' => 15.00,
                'category_id' => $gearCategory->id,
                'supplier_id' => $admin->id,
            ],
            [
                'name' => 'Fishing Net Large',
                'description' => '5m x 5m fishing net, durable nylon',
                'unit_price' => 800.00,
                'available_quantity' => 10.00,
                'category_id' => $gearCategory->id,
                'supplier_id' => $admin->id,
            ],
            [
                'name' => 'Fishing Line 100m',
                'description' => 'High strength fishing line, 100 meter roll',
                'unit_price' => 250.00,
                'available_quantity' => 50.00,
                'category_id' => $gearCategory->id,
                'supplier_id' => $admin->id,
            ],
            [
                'name' => 'Cooler Box 50L',
                'description' => 'Insulated cooler box, keeps fish fresh',
                'unit_price' => 950.00,
                'available_quantity' => 8.00,
                'category_id' => $equipmentCategory->id,
                'supplier_id' => $admin->id,
            ],
            [
                'name' => 'Fishing Vest',
                'description' => 'Multi-pocket fishing vest with life jacket',
                'unit_price' => 650.00,
                'available_quantity' => 12.00,
                'category_id' => $equipmentCategory->id,
                'supplier_id' => $admin->id,
            ],
        ];

        // Insert fish products
        foreach ($fishProducts as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']],
                $product
            );
        }

        // Insert gear products
        foreach ($gearProducts as $product) {
            Product::firstOrCreate(
                ['name' => $product['name']],
                $product
            );
        }

        $this->command->info('Marketplace seeded successfully!');
        $this->command->info('Test accounts created:');
        $this->command->info('Supplier: supplier@test.com / password');
        $this->command->info('Fisherman: fisherman@test.com / password');
    }
}
