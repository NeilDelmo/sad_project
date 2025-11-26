<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceListing;
use App\Models\CustomerOrder;
use App\Models\VendorInventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MarketplaceCartController extends Controller
{
    private function ensureBuyer(): void
    {
        $user = Auth::user();
        if (!$user || $user->user_type !== 'buyer') {
            abort(403, 'Only buyers can manage carts.');
        }
    }

    public function index()
    {
        $this->ensureBuyer();
        $cart = session()->get('marketplace_cart', []);
        $cartItems = [];
        $total = 0;

        if (!empty($cart)) {
            $listings = MarketplaceListing::with(['product', 'seller'])
                ->whereIn('id', array_keys($cart))
                ->get();

            foreach ($listings as $listing) {
                if (isset($cart[$listing->id])) {
                    $qty = $cart[$listing->id];
                    $price = $listing->final_price ?? $listing->asking_price ?? 0;
                    $subtotal = $price * $qty;
                    
                    // Check stock availability for display
                    $stock = 0;
                    if ($listing->vendor_inventory_id) {
                        $inventory = VendorInventory::find($listing->vendor_inventory_id);
                        $stock = $inventory ? $inventory->quantity : 0;
                    }

                    $cartItems[] = [
                        'listing' => $listing,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal,
                        'stock' => $stock,
                        'max_quantity' => $stock // For UI limits
                    ];
                    $total += $subtotal;
                }
            }
        }

        return view('marketplaces.cart', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $this->ensureBuyer();
        $request->validate([
            'listing_id' => 'required|exists:marketplace_listings,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $listingId = $request->listing_id;
        $quantity = (int)$request->quantity;
        $listing = MarketplaceListing::findOrFail($listingId);

        // Prevent buying own item
        if (Auth::check() && Auth::id() === $listing->seller_id) {
            return back()->withErrors(['error' => 'You cannot add your own listing to cart.']);
        }

        // Check if seller is active
        if ($listing->seller && $listing->seller->account_status !== 'active') {
             return back()->withErrors(['error' => 'This seller is currently unavailable.']);
        }

        // Check stock
        $stock = 0;
        if ($listing->vendor_inventory_id) {
            $inventory = VendorInventory::find($listing->vendor_inventory_id);
            $stock = $inventory ? $inventory->quantity : 0;
        }

        if ($quantity > $stock) {
            return back()->withErrors(['quantity' => "Only {$stock} items available."]);
        }

        $cart = session()->get('marketplace_cart', []);

        if (isset($cart[$listingId])) {
            $newQty = $cart[$listingId] + $quantity;
            if ($newQty > $stock) {
                $cart[$listingId] = $stock;
                session()->put('marketplace_cart', $cart);
                return back()->with('warning', "Added to cart, but limited to available stock ({$stock}).");
            }
            $cart[$listingId] = $newQty;
        } else {
            $cart[$listingId] = $quantity;
        }

        session()->put('marketplace_cart', $cart);

        return back()->with('success', 'Added to cart!');
    }

    public function update(Request $request)
    {
        $this->ensureBuyer();
        $request->validate([
            'listing_id' => 'required|exists:marketplace_listings,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $listingId = $request->listing_id;
        $quantity = (int)$request->quantity;
        
        $listing = MarketplaceListing::findOrFail($listingId);
        
        // Check stock
        $stock = 0;
        if ($listing->vendor_inventory_id) {
            $inventory = VendorInventory::find($listing->vendor_inventory_id);
            $stock = $inventory ? $inventory->quantity : 0;
        }

        if ($quantity > $stock) {
            return back()->withErrors(['quantity' => "Only {$stock} items available."]);
        }

        $cart = session()->get('marketplace_cart', []);
        
        if (isset($cart[$listingId])) {
            $cart[$listingId] = $quantity;
            session()->put('marketplace_cart', $cart);
            return back()->with('success', 'Cart updated.');
        }

        return back()->withErrors(['error' => 'Item not found in cart.']);
    }

    public function remove(Request $request)
    {
        $this->ensureBuyer();
        $request->validate([
            'listing_id' => 'required',
        ]);

        $cart = session()->get('marketplace_cart', []);
        
        if (isset($cart[$request->listing_id])) {
            unset($cart[$request->listing_id]);
            session()->put('marketplace_cart', $cart);
        }

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        $this->ensureBuyer();
        session()->forget('marketplace_cart');
        return back()->with('success', 'Cart cleared.');
    }

    public function checkout(Request $request)
    {
        $this->ensureBuyer();
        $user = Auth::user();
        if (!$user) abort(401);

        $cart = session()->get('marketplace_cart', []);
        if (empty($cart)) {
            return back()->withErrors(['error' => 'Cart is empty.']);
        }

        $listings = MarketplaceListing::whereIn('id', array_keys($cart))->get();
        
        try {
            DB::transaction(function () use ($user, $cart, $listings) {
                foreach ($listings as $listing) {
                    if (!isset($cart[$listing->id])) continue;
                    
                    // Check if seller is active
                    if ($listing->seller && $listing->seller->account_status !== 'active') {
                        throw new \Exception("Seller for {$listing->product->name} is currently unavailable.");
                    }

                    $qty = $cart[$listing->id];
                    
                    // Re-check stock
                    $inventory = VendorInventory::where('id', $listing->vendor_inventory_id)->lockForUpdate()->first();
                    if (!$inventory || $inventory->quantity < $qty) {
                        throw new \Exception("Not enough stock for {$listing->product->name}. Available: " . ($inventory ? $inventory->quantity : 0));
                    }

                    // Prevent self-purchase
                    if ($user->id === $listing->seller_id) {
                        throw new \Exception("You cannot purchase your own listing: {$listing->product->name}");
                    }

                    $unitPrice = $listing->final_price ?? $listing->asking_price ?? 0;
                    $total = $unitPrice * $qty;
                    $platformUnitFee = $listing->platform_fee ?? 0;
                    $orderPlatformFee = $platformUnitFee * $qty;

                    $order = CustomerOrder::create([
                        'buyer_id' => $user->id,
                        'vendor_id' => $listing->seller_id,
                        'listing_id' => $listing->id,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total' => $total,
                        'platform_fee' => $orderPlatformFee,
                        'status' => 'pending_payment',
                    ]);

                    $inventory->decrement('quantity', $qty);
                    if ($inventory->quantity <= 0) {
                        $listing->update(['status' => 'inactive', 'unlisted_at' => now()]);
                    }

                    // Notify vendor
                    $vendor = User::find($listing->seller_id);
                    if ($vendor) $vendor->notify(new \App\Notifications\CustomerOrderStatusUpdated($order, 'New order placed'));
                }
            });

            session()->forget('marketplace_cart');
            return redirect()->route('marketplace.orders.index')->with('success', 'Orders placed successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
