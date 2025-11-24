<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\VendorInventory;
use App\Models\MarketplaceListing;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VendorInventoryController extends Controller
{
    use AuthorizesRequests;
    protected $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->middleware('auth');
        $this->pricingService = $pricingService;
    }

    /**
     * Show vendor's inventory
     */
    public function index()
    {
        $inventory = VendorInventory::with(['product', 'product.category', 'order'])
            ->where('vendor_id', auth()->id())
            ->orderByRaw("FIELD(status, 'pending_delivery', 'in_stock', 'listed')")
            ->orderBy('purchased_at', 'desc')
            ->paginate(20);

        return view('vendor.inventory.index', compact('inventory'));
    }

    /**
     * Purchase product from fisherman (add to inventory)
     */
    public function purchase(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->available_quantity,
            'purchase_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Lock product to prevent oversell between concurrent purchases/offers
            $productLocked = Product::where('id', $product->id)->lockForUpdate()->first();
            if ($productLocked->available_quantity < (int) $request->quantity) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Insufficient stock available.']);
            }
            // Create inventory entry
            $inventory = VendorInventory::create([
                'vendor_id' => auth()->id(),
                'product_id' => $product->id,
                'purchase_price' => $request->purchase_price,
                'quantity' => $request->quantity,
                'purchased_at' => now(),
                'status' => 'in_stock',
            ]);

            // Update product quantity
            $product->decrement('available_quantity', $request->quantity);

            DB::commit();

            return redirect()
                ->route('vendor.inventory.show', $inventory)
                ->with('success', 'Product purchased successfully! You can now list it on the marketplace.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to purchase product: ' . $e->getMessage()]);
        }
    }

    /**
     * Show inventory item details
     */
    public function show(VendorInventory $inventory)
    {
        $this->authorize('view', $inventory);
        
        $inventory->load(['product', 'product.category', 'marketplaceListings']);

        return view('vendor.inventory.show', compact('inventory'));
    }

    /**
     * Create marketplace listing from inventory with ML pricing
     */
    public function createListing(VendorInventory $inventory)
    {
        $this->authorize('view', $inventory);

        if ($inventory->status === 'pending_delivery') {
            return back()->withErrors(['error' => 'Cannot list this product yet. The fisherman must deliver the order first.']);
        }

        if ($inventory->status !== 'in_stock') {
            return back()->withErrors(['error' => 'This inventory item is not available for listing.']);
        }

        // Get ML dynamic pricing (anchored on purchase cost)
        $pricingResult = $this->pricingService->calculateDynamicPrice(
            $inventory->product,
            auth()->user(),
            [
                'log_context' => 'vendor_inventory_preview',
                'base_price' => (float) $inventory->purchase_price,
            ]
        );
        
        $baseCost = $inventory->purchase_price;
        $dynamicPrice = $pricingResult['final_price'];
        $mlMultiplier = $pricingResult['effective_multiplier'] ?? $pricingResult['market_multiplier'] ?? 1.0;
        $mlConfidence = $pricingResult['confidence'];
        
        // Calculate commission breakdown (10% platform fee)
        $platformFee = $dynamicPrice * config('marketplace.platform_commission_rate', 0.10);
        $vendorProfit = $dynamicPrice - $baseCost - $platformFee;

        return view('vendor.inventory.create-listing', compact(
            'inventory',
            'baseCost',
            'dynamicPrice',
            'mlMultiplier',
            'mlConfidence',
            'platformFee',
            'vendorProfit',
            'pricingResult'
        ));
    }

    /**
     * Store marketplace listing with ML pricing
     */
    public function storeListing(Request $request, VendorInventory $inventory)
    {
        $this->authorize('view', $inventory);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $inventory->quantity,
        ]);

        if ($inventory->status !== 'in_stock') {
            return back()->withErrors(['error' => 'This inventory item is not available for listing.']);
        }

        DB::beginTransaction();
        try {
            // Get ML dynamic pricing anchored on purchase cost
            $pricingResult = $this->pricingService->calculateDynamicPrice(
                $inventory->product,
                auth()->user(),
                [
                    'log_context' => 'vendor_inventory_listing',
                    'base_price' => (float) $inventory->purchase_price,
                ]
            );
            
            $baseCost = $inventory->purchase_price;
            $mlMultiplier = $pricingResult['effective_multiplier'] ?? $pricingResult['market_multiplier'] ?? 1.0;
            $dynamicPrice = $pricingResult['final_price'];
            $mlConfidence = $pricingResult['confidence'];
            
            // Calculate 10% platform commission
            $platformFee = $dynamicPrice * config('marketplace.platform_commission_rate', 0.10);
            $vendorProfit = $dynamicPrice - $baseCost - $platformFee;

            // Create marketplace listing
            $listing = MarketplaceListing::create([
                'product_id' => $inventory->product_id,
                'vendor_inventory_id' => $inventory->id,
                'seller_id' => auth()->id(),
                'base_price' => $baseCost,
                'ml_multiplier' => $mlMultiplier,
                'dynamic_price' => $dynamicPrice,
                'platform_fee' => $platformFee,
                'vendor_profit' => $vendorProfit,
                'final_price' => $dynamicPrice,
                'ml_confidence' => $mlConfidence,
                'asking_price' => $dynamicPrice, // Legacy compatibility
                'demand_factor' => $pricingResult['signals']['demand']['score'] ?? $pricingResult['features']['demand_factor'] ?? null,
                'freshness_score' => $pricingResult['features']['freshness_score'] ?? null,
                'listing_date' => now(),
                'status' => 'active',
            ]);

            // Update inventory status
            $inventory->update(['status' => 'listed']);

            DB::commit();

            return redirect()
                ->route('marketplace.shop')
                ->with('success', 'Your listing is live in the Marketplace.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create listing: ' . $e->getMessage()]);
        }
    }
}
