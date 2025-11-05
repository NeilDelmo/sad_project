<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/test-profile', function () {
    $user = User::first();

    if (! $user) {
        return response()->json(['error' => 'No users found'], 404);
    }

    // Create a profile if none exists; otherwise return the existing one
    $profile = $user->fishermanProfile;
    if (! $profile) {
        $profile = $user->fishermanProfile()->create([
            'vessel_name' => 'Blue Dolphin',
            'vessel_type' => 'bangka',
        ]);
    }

    return response()->json($profile);
});

// Marketplace routes (public - no authentication required)
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MessageController;

Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/shop', [MarketplaceController::class, 'shop'])->name('marketplace.shop');

// Messaging routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/marketplace/message/{conversationId?}', [MessageController::class, 'show'])->name('marketplace.message');
    Route::get('/marketplace/product/{productId}/message', [MessageController::class, 'show'])->name('marketplace.message.product');
    Route::get('/api/conversations/{conversationId}/messages', [MessageController::class, 'getMessages']);
    Route::post('/api/conversations/{conversationId}/messages', [MessageController::class, 'sendMessage']);
});
