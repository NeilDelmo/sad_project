<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Rental;
use App\Models\RentalItem;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'fisherman']);
});

test('admin can approve rental and generate otp', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $fisherman = User::factory()->create();
    $fisherman->assignRole('fisherman');
    
    $product = Product::factory()->create([
        'is_rentable' => true,
        'rental_stock' => 10,
        'reserved_stock' => 0,
        'rental_price_per_day' => 100,
    ]);
    
    $rental = Rental::factory()->create([
        'user_id' => $fisherman->id,
        'status' => 'pending',
    ]);
    
    RentalItem::factory()->create([
        'rental_id' => $rental->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price_per_day' => 100,
    ]);
    
    $this->actingAs($admin)
        ->post(route('rentals.approve', $rental))
        ->assertRedirect()
        ->assertSessionHas('success');
    
    $rental->refresh();
    $product->refresh();
    
    expect($rental->status)->toBe('approved');
    expect($rental->pickup_otp)->not->toBeNull();
    expect(strlen($rental->pickup_otp))->toBe(6);
    expect($product->reserved_stock)->toBe(2);
    expect($product->rental_stock)->toBe(10);
});

test('admin can activate rental with valid otp', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $fisherman = User::factory()->create();
    $fisherman->assignRole('fisherman');
    
    $product = Product::factory()->create([
        'is_rentable' => true,
        'rental_stock' => 10,
        'reserved_stock' => 2,
        'rental_price_per_day' => 100,
    ]);
    
    $rental = Rental::factory()->create([
        'user_id' => $fisherman->id,
        'status' => 'approved',
        'pickup_otp' => '123456',
    ]);
    
    RentalItem::factory()->create([
        'rental_id' => $rental->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price_per_day' => 100,
    ]);
    
    $this->actingAs($admin)
        ->post(route('rentals.activate', $rental), ['otp' => '123456'])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    $rental->refresh();
    $product->refresh();
    
    expect($rental->status)->toBe('active');
    expect($rental->picked_up_at)->not->toBeNull();
    expect($product->reserved_stock)->toBe(0);
    expect($product->rental_stock)->toBe(8);
});

test('auto cancel releases reserved stock', function () {
    $fisherman = User::factory()->create();
    $fisherman->assignRole('fisherman');
    
    $product = Product::factory()->create([
        'is_rentable' => true,
        'rental_stock' => 10,
        'reserved_stock' => 3,
    ]);
    
    $rental = Rental::factory()->create([
        'user_id' => $fisherman->id,
        'status' => 'approved',
        'expires_at' => now()->subHour(),
        'pickup_otp' => '123456',
    ]);
    
    RentalItem::factory()->create([
        'rental_id' => $rental->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);
    
    $this->artisan('rentals:cancel-expired');
    
    $rental->refresh();
    $product->refresh();
    
    expect($rental->status)->toBe('cancelled');
    expect($product->reserved_stock)->toBe(0);
});
