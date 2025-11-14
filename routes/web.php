<?php

use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiskPredictionController; 
use App\Http\Controllers\ForumController;
use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FishermanController;
use App\Http\Controllers\FishingSafetyController;
use App\Http\Controllers\VendorOnboardingController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Public fishing safety map (no authentication required)
Route::get('/fishing-safety', [RiskPredictionController::class, 'publicMap'])->name('fishing-safety.public');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dashboard/risk', [RiskPredictionController::class, 'showForm'])->name('risk-form');
    Route::post('/dashboard/risk', [RiskPredictionController::class, 'predict'])->name('predict-risk');
    Route::get('/dashboard/risk/history', [RiskPredictionController::class, 'history'])->name('risk-history');
    Route::get('/api/risk/latest', [RiskPredictionController::class, 'latest'])->name('risk-latest');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read.all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
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

Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/shop', [MarketplaceController::class, 'shop'])->name('marketplace.shop');

// Messaging routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/marketplace/message/{conversationId}', [MessageController::class, 'show'])->name('marketplace.message');
    Route::get('/marketplace/product/{productId}/message', [MessageController::class, 'startConversation'])->name('marketplace.message.product');
    Route::get('/api/conversations/{conversationId}/messages', [MessageController::class, 'getMessages']);
    Route::post('/api/conversations/{conversationId}/messages', [MessageController::class, 'sendMessage']);
});

// Fisherman routes (requires authentication + fisherman role)

Route::middleware(['auth'])->prefix('fisherman')->name('fisherman.')->group(function () {
    // Fisherman Dashboard (will include safety navigation/ML features later)
    Route::get('/dashboard', [FishermanController::class, 'dashboard'])->name('dashboard');
    
    // Product Management (CRUD)
    Route::resource('products', ProductController::class)->except(['show']);
    
    // Message Inbox
    Route::get('/messages', [FishermanController::class, 'inbox'])->name('messages');
});

// Vendor routes (requires authentication)
Route::middleware(['auth', 'vendor.onboarded'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/onboarding', [VendorOnboardingController::class, 'show'])->withoutMiddleware('vendor.onboarded')->name('onboarding');
    Route::post('/onboarding', [VendorOnboardingController::class, 'store'])->withoutMiddleware('vendor.onboarded')->name('onboarding.store');

    Route::get('/dashboard', [VendorOnboardingController::class, 'dashboard'])->name('dashboard');
});

// Forum routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');
    Route::get('/forums/category/{id}', [ForumController::class, 'showCategory'])->name('forums.category');
    Route::get('/forums/thread/{id}', [ForumController::class, 'showThread'])->name('forums.thread');
    Route::post('/forums/category/{category_id}/thread', [ForumController::class, 'storeThread'])->name('forums.thread.store');
    Route::post('/forums/thread/{thread_id}/reply', [ForumController::class, 'storeReply'])->name('forums.reply.store');
    Route::post('/forums/upload-image', [ForumController::class, 'uploadImage'])->name('forums.upload-image');
});

// Rental routes (requires authentication for rentals, public for browsing)
Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
Route::get('/rentals/{product}', [RentalController::class, 'show'])->name('rentals.show');

Route::middleware('auth')->group(function () {
    Route::get('/rentals/create', [RentalController::class, 'create'])->name('rentals.create');
    Route::post('/rentals', [RentalController::class, 'store'])->name('rentals.store');
    Route::get('/my-rentals', [RentalController::class, 'myRentals'])->name('rentals.myrentals');
    Route::post('/rentals/{rental}/cancel', [RentalController::class, 'cancel'])->name('rentals.cancel');
});

// Fishing Safety API routes (proxies to Flask)
// Health and setup check are public, actual API calls require auth
Route::prefix('api/fishing-safety')
    ->name('api.fishing-safety.')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {
    // Public endpoints for monitoring
    Route::get('/health', [FishingSafetyController::class, 'health'])->name('health');
    Route::get('/setup-check', [FishingSafetyController::class, 'setupCheck'])->name('setup-check');
    
    // Public endpoints (no auth needed for map functionality)
    Route::post('/', [FishingSafetyController::class, 'checkSafety'])->name('check');
    Route::post('/batch', [FishingSafetyController::class, 'checkBatch'])->name('batch');
    Route::post('/weather-map', [FishingSafetyController::class, 'weatherMap'])->name('weather-map');
    
    // Protected endpoint - history requires authentication
    Route::middleware('auth')->group(function () {
        Route::get('/history', [FishingSafetyController::class, 'history'])->name('history');
        Route::post('/record-outcome', [FishingSafetyController::class, 'recordOutcome'])->name('record-outcome');
    });
});
