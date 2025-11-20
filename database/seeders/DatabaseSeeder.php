<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the permission seeder first
        $this->call(PermissionSeeder::class);

        // Create test admin user
        $admin = User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '+15555550123',
            'user_type' => 'admin',
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        // Create sample fisherman users
        $fisherman1 = User::factory()->create([
            'username' => 'fisherman_juan',
            'email' => 'juan@fishermen.com',
            'phone' => '+15555551111',
            'user_type' => 'fisherman',
            'status' => 'active',
        ]);

        $fisherman2 = User::factory()->create([
            'username' => 'fisherman_pedro',
            'email' => 'pedro@fishermen.com',
            'phone' => '+15555552222',
            'user_type' => 'fisherman',
            'status' => 'active',
        ]);

        // Create sample vendor users
        $vendor1 = User::factory()->create([
            'username' => 'vendor_maria',
            'email' => 'maria@vendors.com',
            'phone' => '+15555553333',
            'user_type' => 'vendor',
            'status' => 'active',
        ]);

        $vendor2 = User::factory()->create([
            'username' => 'vendor_jose',
            'email' => 'jose@vendors.com',
            'phone' => '+15555554444',
            'user_type' => 'vendor',
            'status' => 'active',
        ]);

        // Create sample buyer users
        $buyer1 = User::factory()->create([
            'username' => 'buyer_anna',
            'email' => 'anna@buyers.com',
            'phone' => '+15555555555',
            'user_type' => 'buyer',
            'status' => 'active',
        ]);

        // Create vendor-fisherman subscriptions
        \DB::table('vendor_fisherman_subscriptions')->insert([
            [
                'vendor_id' => $vendor1->id,
                'fisherman_id' => $fisherman1->id,
                'notification_preference' => 'app',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vendor_id' => $vendor1->id,
                'fisherman_id' => $fisherman2->id,
                'notification_preference' => 'email',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'vendor_id' => $vendor2->id,
                'fisherman_id' => $fisherman1->id,
                'notification_preference' => 'sms',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Call other seeders
        $this->call([
            MarketplaceSeeder::class,
            ForumCategorySeeder::class,
            GearRentalSeeder::class,
            TrustDemoSeeder::class,
        ]);
    }
}
