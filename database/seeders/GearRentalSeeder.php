<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GearRentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create Gear category
        $gearCategory = ProductCategory::firstOrCreate(
            ['name' => 'Gear'],
            ['description' => 'Fishing gear and equipment available for rental']
        );

        // Get organization user (or first user as fallback)
        $organization = User::where('email', 'organization@sealedger.com')->first() 
            ?? User::first();

        if (!$organization) {
            $this->command->error('No users found. Please create a user first.');
            return;
        }

        // Sample gear items
        $gearItems = [
            [
                'name' => 'Fishing Net (Large)',
                'description' => 'Professional grade fishing net, 50m length. Suitable for deep sea fishing.',
                'rental_price_per_day' => 500.00,
                'rental_stock' => 5,
            ],
            [
                'name' => 'Fishing Net (Medium)',
                'description' => 'Medium-sized fishing net, 30m length. Perfect for coastal fishing.',
                'rental_price_per_day' => 300.00,
                'rental_stock' => 8,
            ],
            [
                'name' => 'GPS Navigation System',
                'description' => 'Marine GPS with chartplotter and fish finder capabilities.',
                'rental_price_per_day' => 200.00,
                'rental_stock' => 3,
            ],
            [
                'name' => 'Life Jackets (Set of 5)',
                'description' => 'Coast guard approved life jackets. Set includes 5 adult-sized vests.',
                'rental_price_per_day' => 100.00,
                'rental_stock' => 10,
            ],
            [
                'name' => 'Cooler Box (Large)',
                'description' => 'Industrial cooler box, 200L capacity. Keeps catch fresh for up to 48 hours.',
                'rental_price_per_day' => 150.00,
                'rental_stock' => 6,
            ],
            [
                'name' => 'Marine Radio',
                'description' => 'VHF marine radio for communication and emergency broadcasts.',
                'rental_price_per_day' => 120.00,
                'rental_stock' => 4,
            ],
            [
                'name' => 'Fish Finder',
                'description' => 'Sonar fish finder with depth readings up to 200m.',
                'rental_price_per_day' => 180.00,
                'rental_stock' => 3,
            ],
            [
                'name' => 'Fishing Rods (Professional Set)',
                'description' => 'Set of 5 professional fishing rods with reels and tackle box.',
                'rental_price_per_day' => 250.00,
                'rental_stock' => 7,
            ],
            [
                'name' => 'Anchor System',
                'description' => 'Heavy-duty anchor with 50m chain and rope. Suitable for boats up to 30ft.',
                'rental_price_per_day' => 80.00,
                'rental_stock' => 5,
            ],
            [
                'name' => 'Bait Container',
                'description' => 'Aerated bait container, keeps live bait fresh for extended trips.',
                'rental_price_per_day' => 60.00,
                'rental_stock' => 12,
            ],
        ];

        foreach ($gearItems as $item) {
            Product::create([
                'supplier_id' => $organization->id,
                'category_id' => $gearCategory->id,
                'name' => $item['name'],
                'description' => $item['description'],
                'is_rentable' => true,
                'rental_price_per_day' => $item['rental_price_per_day'],
                'rental_stock' => $item['rental_stock'],
                'rental_available' => $item['rental_stock'], // Initially all stock is available
                'rental_condition' => 'good',
                'unit_price' => 0, // Not for sale
                'available_quantity' => 0,
                'freshness_metric' => null,
                'seasonality_factor' => 1.0,
                'quality_rating' => 5,
            ]);
        }

        $this->command->info('Successfully seeded ' . count($gearItems) . ' gear items!');
    }
}
