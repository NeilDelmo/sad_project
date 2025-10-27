<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create all permissions first
        $permissions = [
            'view safety predictions',
            'create trip plan', 
            'view risk scores',
            'access weather data',
            'view marketplace',
            'buy products',
            'sell products',
            'manage listings',
            'view pricing suggestions',
            'view maintenance reminders',
            'log maintenance',
            'rent equipment',
            'manage rentals',
            'track equipment usage',
            'view own credit score',
            'view all credit scores',
            'update credit scores',
            'access regulator dashboard',
            'view heatmaps',
            'view compliance reports',
            'monitor fishing activities',
            'generate forecasts',
            'access community hub',
            'create posts',
            'comment on posts',
            'moderate content',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Clear cache so permissions are immediately available
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $regulator = Role::create(['name' => 'regulator']);
        $fisherman = Role::create(['name' => 'fisherman']);
        $buyer = Role::create(['name' => 'buyer']);
        $supplier = Role::create(['name' => 'supplier']);

        // Assign permissions to roles
        // Safety module permissions
        $admin->givePermissionTo(['view safety predictions', 'create trip plan', 'view risk scores', 'access weather data']);
        $fisherman->givePermissionTo(['view safety predictions', 'create trip plan', 'view risk scores', 'access weather data']);

        // Marketplace permissions
        $buyer->givePermissionTo(['view marketplace', 'buy products', 'view pricing suggestions']);
        $supplier->givePermissionTo(['view marketplace', 'sell products', 'manage listings', 'view pricing suggestions']);
        $fisherman->givePermissionTo(['view marketplace', 'sell products']); // Fishermen can also sell
        $admin->givePermissionTo(['view marketplace', 'buy products', 'sell products', 'manage listings', 'view pricing suggestions']);

        // Equipment management permissions
        $fisherman->givePermissionTo(['view maintenance reminders', 'log maintenance', 'rent equipment', 'track equipment usage']);
        $admin->givePermissionTo(['view maintenance reminders', 'log maintenance', 'rent equipment', 'manage rentals', 'track equipment usage']);
        $supplier->givePermissionTo(['manage rentals', 'track equipment usage']);

        // Credit scoring permissions
        $fisherman->givePermissionTo(['view own credit score']);
        $regulator->givePermissionTo(['view all credit scores']);
        $admin->givePermissionTo(['view all credit scores', 'update credit scores']);

        // Regulator dashboard permissions
        $regulator->givePermissionTo(['access regulator dashboard', 'view heatmaps', 'view compliance reports', 'monitor fishing activities', 'generate forecasts']);
        $admin->givePermissionTo(['access regulator dashboard', 'view heatmaps', 'view compliance reports']);

        // Community hub permissions
        $fisherman->givePermissionTo(['access community hub', 'create posts', 'comment on posts']);
        $buyer->givePermissionTo(['access community hub', 'create posts', 'comment on posts']);
        $supplier->givePermissionTo(['access community hub', 'create posts', 'comment on posts']);
        $regulator->givePermissionTo(['access community hub']);
        $admin->givePermissionTo(['access community hub', 'create posts', 'comment on posts', 'moderate content']);
    }

}

