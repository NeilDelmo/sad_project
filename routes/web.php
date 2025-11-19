<?php

use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiskPredictionController; 
use App\Http\Controllers\ForumController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\VendorInventoryController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\CustomerOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FishermanController;
use App\Http\Controllers\FishingSafetyController;
use App\Http\Controllers\VendorOnboardingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MLAnalyticsController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Fishing safety map (restricted; auth required). Public access removed.

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user) {
            switch ($user->user_type) {
                case 'vendor':
                    return redirect()->route('vendor.dashboard');
                case 'fisherman':
                    return redirect()->route('fisherman.dashboard');
                case 'buyer':
                    return redirect()->route('marketplace.shop');
            }
        }
        // Admin/regulator or fallback uses Breeze dashboard view
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dashboard/risk', [RiskPredictionController::class, 'showForm'])->name('risk-form');
    Route::post('/dashboard/risk', [RiskPredictionController::class, 'predict'])->name('predict-risk');
    Route::get('/dashboard/risk/history', [RiskPredictionController::class, 'history'])->name('risk-history');
    Route::get('/api/risk/latest', [RiskPredictionController::class, 'latest'])->name('risk-latest');

    // Auth-only map entrypoint (visible to fishermen only in controller)
    Route::get('/fishing-safety', [RiskPredictionController::class, 'publicMap'])->name('fishing-safety.public');

    // (moved below to auth-only group)
});

Route::middleware('auth')->group(function () {
    // Notifications API for navbar polling (auth only, not email-verified)
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.unread-count');
    Route::get('/api/notifications/latest', [NotificationController::class, 'latest'])->name('api.notifications.latest');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read.all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Orders
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/in-transit', [OrdersController::class, 'markInTransit'])->name('orders.in-transit');
    Route::post('/orders/{order}/delivered', [OrdersController::class, 'markDelivered'])->name('orders.delivered');
    Route::post('/orders/{order}/received', [OrdersController::class, 'confirmReceived'])->name('orders.received');
    Route::post('/orders/{order}/refund-request', [OrdersController::class, 'requestRefund'])->name('orders.refund.request');
    Route::post('/orders/{order}/refund-approve', [OrdersController::class, 'approveRefund'])->name('orders.refund.approve');
    Route::post('/orders/{order}/refund-decline', [OrdersController::class, 'declineRefund'])->name('orders.refund.decline');
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
// Marketplace Recommendations API (JSON)
Route::get('/api/recommendations', [MarketplaceController::class, 'recommendations'])->name('api.recommendations');
Route::middleware('auth')->group(function () {
    Route::post('/marketplace/listings/{listing}/buy', [CustomerOrderController::class, 'purchase'])->name('marketplace.buy');
    Route::get('/marketplace/orders', [CustomerOrderController::class, 'index'])->name('marketplace.orders.index');
    Route::post('/marketplace/orders/{order}/in-transit', [CustomerOrderController::class, 'markInTransit'])->name('marketplace.orders.intransit');
    Route::post('/marketplace/orders/{order}/delivered', [CustomerOrderController::class, 'vendorDelivered'])->name('marketplace.orders.delivered');
    Route::post('/marketplace/orders/{order}/received', [CustomerOrderController::class, 'buyerReceived'])->name('marketplace.orders.received');
    Route::post('/marketplace/orders/{order}/refund-request', [CustomerOrderController::class, 'requestRefund'])->name('marketplace.orders.refund.request');
    Route::post('/marketplace/orders/{order}/refund-approve', [CustomerOrderController::class, 'approveRefund'])->name('marketplace.orders.refund.approve');
    Route::post('/marketplace/orders/{order}/refund-decline', [CustomerOrderController::class, 'declineRefund'])->name('marketplace.orders.refund.decline');
});

// Messaging routes (requires authentication) - DISABLED
// Route::middleware('auth')->group(function () {
//     Route::get('/marketplace/message/{conversationId}', [MessageController::class, 'show'])->name('marketplace.message');
//     Route::get('/marketplace/product/{productId}/message', [MessageController::class, 'startConversation'])->name('marketplace.message.product');
//     Route::get('/marketplace/listing/{listingId}/message', [MessageController::class, 'startConversationForListing'])->name('marketplace.message.listing');
//     Route::get('/api/conversations/{conversationId}/messages', [MessageController::class, 'getMessages']);
//     Route::post('/api/conversations/{conversationId}/messages', [MessageController::class, 'sendMessage']);
//     Route::get('/api/messages/unread-count', [MessageController::class, 'getUnreadCount']);
//     Route::get('/api/messages/latest-unread', [MessageController::class, 'getLatestUnread']);
// });

// Offer Notification API (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/api/offers/pending-count', [\App\Http\Controllers\OfferNotificationController::class, 'getPendingCount']);
    Route::get('/api/offers/latest', [\App\Http\Controllers\OfferNotificationController::class, 'getLatestOffers']);
    Route::post('/api/offers/notifications/{id}/read', [\App\Http\Controllers\OfferNotificationController::class, 'markAsRead']);
});

// Fisherman routes (requires authentication + fisherman role)

Route::middleware(['auth'])->prefix('fisherman')->name('fisherman.')->group(function () {
    // Fisherman Dashboard (will include safety navigation/ML features later)
    Route::get('/dashboard', [FishermanController::class, 'dashboard'])->name('dashboard');
    
    // Product Management (CRUD)
    Route::resource('products', ProductController::class)->except(['show']);
    
    // Offers Management
    Route::get('/offers', [FishermanController::class, 'offers'])->name('offers.index');

    // Fisherman Offers actions (keep actions for integrations; index removed)
    Route::post('/offers/{offer}/accept', [\App\Http\Controllers\VendorOfferController::class, 'accept'])->name('offers.accept');
    Route::post('/offers/{offer}/reject', [\App\Http\Controllers\VendorOfferController::class, 'reject'])->name('offers.reject');
    Route::post('/offers/{offer}/counter', [\App\Http\Controllers\VendorOfferController::class, 'counter'])->name('offers.counter');
});

// Vendor routes (requires authentication)
Route::middleware(['auth', 'vendor.onboarded'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/onboarding', [VendorOnboardingController::class, 'show'])->withoutMiddleware('vendor.onboarded')->name('onboarding');
    Route::post('/onboarding', [VendorOnboardingController::class, 'store'])->withoutMiddleware('vendor.onboarded')->name('onboarding.store');

    Route::get('/dashboard', [VendorOnboardingController::class, 'dashboard'])->name('dashboard');
    
    // Offers Management
    Route::get('/offers', [VendorOnboardingController::class, 'offers'])->name('offers.index');
    
    // Vendor Browse Products (see all products; optional filters)
    Route::get('/products', [VendorOnboardingController::class, 'browseProducts'])->name('products.index');
    
    // Vendor Inventory Management
    Route::get('/inventory', [VendorInventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/{inventory}', [VendorInventoryController::class, 'show'])->name('inventory.show');
    Route::post('/inventory/{product}/purchase', [VendorInventoryController::class, 'purchase'])->name('inventory.purchase');
    
    // Marketplace Listing Creation (ML Pricing)
    Route::get('/inventory/{inventory}/create-listing', [VendorInventoryController::class, 'createListing'])->name('inventory.create-listing');
    Route::post('/inventory/{inventory}/list', [VendorInventoryController::class, 'storeListing'])->name('inventory.store-listing');

    // Vendor Offers
    Route::post('/offers/{product}', [\App\Http\Controllers\VendorOfferController::class, 'store'])->name('offers.store');
    Route::post('/offers/{offer}/accept-counter', [\App\Http\Controllers\VendorOfferController::class, 'acceptCounter'])->name('offers.accept-counter');
    Route::post('/offers/{offer}/decline-counter', [\App\Http\Controllers\VendorOfferController::class, 'declineCounter'])->name('offers.decline-counter');
});

// Forum routes (requires authentication)
Route::middleware('auth')->group(function () {
    Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');
    Route::get('/forums/category/{id}', [ForumController::class, 'showCategory'])->name('forums.category');
    Route::get('/forums/thread/{id}', [ForumController::class, 'showThread'])->name('forums.thread');
    Route::post('/forums/category/{category_id}/thread', [ForumController::class, 'storeThread'])->name('forums.thread.store');
    Route::post('/forums/thread/{thread_id}/reply', [ForumController::class, 'storeReply'])->name('forums.reply.store');
    Route::post('/forums/thread/{thread_id}/vote', [ForumController::class, 'voteThread'])->name('forums.thread.vote');
    Route::post('/forums/reply/{reply_id}/vote', [ForumController::class, 'voteReply'])->name('forums.reply.vote');
    Route::post('/forums/upload-image', [ForumController::class, 'uploadImage'])->name('forums.upload-image');
});

// Rental routes (requires authentication for rentals, public for browsing)
Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
Route::get('/rentals/{product}', [RentalController::class, 'show'])->whereNumber('product')->name('rentals.show');

Route::middleware('auth')->group(function () {
    Route::get('/rentals/create', [RentalController::class, 'create'])->name('rentals.create');
    Route::post('/rentals', [RentalController::class, 'store'])->name('rentals.store');
    Route::get('/my-rentals', [RentalController::class, 'myRentals'])->name('rentals.myrentals');
    Route::get('/rentals/{rental}/report', [RentalController::class, 'reportForm'])->name('rentals.report.form');
    Route::post('/rentals/{rental}/report', [RentalController::class, 'submitReport'])->name('rentals.report.submit');
    Route::post('/rentals/{rental}/cancel', [RentalController::class, 'cancel'])->name('rentals.cancel');
    
    // Admin rental management
    Route::get('/admin/rentals', [RentalController::class, 'adminIndex'])->name('rentals.admin.index');
    Route::post('/rentals/{rental}/approve', [RentalController::class, 'approve'])->name('rentals.approve');
    Route::post('/rentals/{rental}/reject', [RentalController::class, 'reject'])->name('rentals.reject');
    Route::post('/rentals/{rental}/activate', [RentalController::class, 'activate'])->name('rentals.activate');
    Route::post('/rentals/{rental}/return', [RentalController::class, 'processReturn'])->name('rentals.return');
    
    // Equipment maintenance
    Route::get('/admin/maintenance', [RentalController::class, 'maintenanceDashboard'])->name('rentals.admin.maintenance');
    Route::get('/admin/reports', [RentalController::class, 'viewReports'])->name('rentals.admin.reports');
    Route::post('/admin/reports/{report}/mark-maintenance', [RentalController::class, 'markForMaintenance'])->name('reports.mark.maintenance');
    Route::post('/admin/reports/{report}/resolve', [RentalController::class, 'resolveReport'])->name('reports.resolve');
    Route::post('/admin/equipment/{product}/repair', [RentalController::class, 'markRepaired'])->name('equipment.repair');
    Route::post('/admin/equipment/{product}/retire', [RentalController::class, 'retireEquipment'])->name('equipment.retire');
    
    // ML Analytics Dashboard
    Route::get('/admin/ml-analytics', [MLAnalyticsController::class, 'index'])->name('admin.ml.analytics');
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
