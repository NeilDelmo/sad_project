<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        // Create test user and assign admin role
        $user = User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '+15555550123',
            'user_type' => 'admin',
            'status' => 'active',
        ]);
        
        $user->assignRole('admin');
    }
}
