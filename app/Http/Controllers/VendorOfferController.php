<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\VendorOffer;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Notifications\NewVendorOffer;
use App\Notifications\VendorOfferAccepted;
use App\Notifications\VendorOfferRejected;
use App\Notifications\VendorAcceptedCounter;
use App\Services\FishermanPricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VendorOfferController extends Controller
{
    use AuthorizesRequests;
    
    protected FishermanPricingService $fishermanPricing;
    
    public function __construct(FishermanPricingService $fishermanPricing)
    {
        $this->middleware('auth');
        $this->fishermanPricing = $fishermanPricing;
    }

    /**
     * Vendor makes an offer on a product
     */
    public function store(Request $request, Product $product)
    {
        // Check if vendor already has a pending or countered offer for this product
        $existingOffer = VendorOffer::where('vendor_id', Auth::id())
            ->where('product_id', $product->id)
            ->whereIn('status', ['pending', 'countered'])
            ->first();

        if ($existingOffer) {
            return back()->withErrors(['error' => 'You already have a pending offer for this product. Please wait for the fisherman to respond.']);
        }

        $request->validate([
            'offered_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1|max:' . $product->available_quantity,
            'message' => 'nullable|string|max:500',
        ]);

        // Calculate fair market price for fisherman's reference
        $fairPricing = $this->fishermanPricing->calculateFairPrice($product);
        $suggestedPrice = $fairPricing['suggested_price'] ?? null;
        $mlConfidence = $fairPricing['confidence'] ?? null;

        $offer = VendorOffer::create([
            'vendor_id' => Auth::id(),
            'fisherman_id' => $product->supplier_id,
            'product_id' => $product->id,
            'offered_price' => $request->offered_price,
            'quantity' => $request->quantity,
            'vendor_message' => $request->message,
            'status' => 'pending',
            'expires_at' => now()->addDays(3),
            'suggested_price_fisherman' => $suggestedPrice,
            'ml_confidence_fisherman' => $mlConfidence,
        ]);

        // Messaging removed; notification sent below

        // Send a database notification to the fisherman
        $fisherman = User::find($product->supplier_id);
        if ($fisherman) {
            $fisherman->notify(new NewVendorOffer($offer));
        }

        return back()->with('success', 'Offer sent successfully! The fisherman has been notified and has 3 days to respond.');
    }

    /**
     * Fisherman views their offers
     */
    public function fishermanIndex(Request $request)
    {
        $status = $request->input('status', 'pending');

        $query = VendorOffer::with(['vendor', 'product', 'product.category'])
            ->where('fisherman_id', Auth::id());

        // Filter by status if specified
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $offers = $query->orderByRaw("FIELD(status, 'pending', 'countered', 'accepted', 'auto_rejected', 'withdrawn', 'expired')")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['status' => $status]);

        return view('fisherman.offers.index', compact('offers'));
    }

    /**
     * Fisherman accepts an offer
     */
    public function accept(VendorOffer $offer)
    {
        $this->authorize('respond', $offer);

        if (!$offer->canRespond()) {
            return back()->withErrors(['error' => 'This offer has expired or already been responded to.']);
        }

        DB::beginTransaction();
        try {
            // Lock product row to prevent oversell
            $product = \App\Models\Product::where('id', $offer->product_id)->lockForUpdate()->first();
            if ($product->available_quantity < $offer->quantity) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Insufficient stock available. Only ' . $product->available_quantity . 'kg remaining.']);
            }
            $offer->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // Create vendor inventory from accepted counter offer (pending delivery)
            $inventory = \App\Models\VendorInventory::create([
                'vendor_id' => $offer->vendor_id,
                'product_id' => $offer->product_id,
                'purchase_price' => $offer->fisherman_counter_price ?? $offer->offered_price,
                'quantity' => $offer->quantity,
                'purchased_at' => now(),
                'status' => 'pending_delivery',
            ]);

            // Decrement product quantity
            $offer->product->decrement('available_quantity', $offer->quantity);

            // Create order (pending_payment) and link to inventory
            $unitPrice = (float) ($offer->fisherman_counter_price ?? $offer->offered_price);
            $order = \App\Models\Order::create([
                'vendor_id' => $offer->vendor_id,
                'fisherman_id' => $offer->fisherman_id,
                'product_id' => $offer->product_id,
                'offer_id' => $offer->id,
                'quantity' => (int) $offer->quantity,
                'unit_price' => $unitPrice,
                'total' => $unitPrice * (int) $offer->quantity,
                'status' => 'pending_payment',
            ]);

            // Link inventory to order
            $inventory->update(['order_id' => $order->id]);

            // Auto-reject offers that can no longer be fulfilled
            $rejectedCount = VendorOffer::autoRejectInsufficientStock($offer->product_id);

            DB::commit();

            // Notify vendor of acceptance
            $vendor = \App\Models\User::find($offer->vendor_id);
            if ($vendor) {
                $vendor->notify(new \App\Notifications\VendorOfferAccepted($offer));
            }

            $remainingStock = $product->available_quantity - $offer->quantity;
            $message = 'Offer accepted! The vendor has been notified.';
            if ($rejectedCount > 0) {
                $message .= " {$rejectedCount} bid(s) auto-rejected due to insufficient stock.";
            }
            if ($remainingStock > 0) {
                $message .= " {$remainingStock}kg remaining.";
            } else {
                $message .= ' All stock allocated!';
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to accept offer: ' . $e->getMessage()]);
        }
    }

    /**
     * Fisherman counters an offer
     */
    public function counter(Request $request, VendorOffer $offer)
    {
        $this->authorize('respond', $offer);

        if (!$offer->canRespond()) {
            return back()->withErrors(['error' => 'This offer has expired or already been responded to.']);
        }

        $request->validate([
            'counter_price' => 'required|numeric|min:0',
            'message' => 'nullable|string|max:500',
        ]);

        $offer->update([
            'status' => 'countered',
            'fisherman_counter_price' => $request->counter_price,
            'fisherman_message' => $request->message,
            'responded_at' => now(),
            'expires_at' => now()->addDays(2), // Reset expiration
        ]);

        // Notify vendor (database notification)
        $vendor = \App\Models\User::find($offer->vendor_id);
        if ($vendor) {
            $vendor->notify(new \App\Notifications\CounterVendorOffer($offer));
        }

        return back()->with('success', 'Counter offer sent! Vendor has 2 days to respond.');
    }

    /**
     * Vendor accepts fisherman's counter offer
     */
    public function acceptCounter(VendorOffer $offer)
    {
        $this->authorize('acceptCounter', $offer);
        // Vendor accepts a fisherman's counter offer
        if ($offer->vendor_id !== Auth::id() || $offer->status !== 'countered') {
            return back()->withErrors(['error' => 'Cannot accept this counter offer.']);
        }
        if ($offer->isExpired()) {
            $offer->update(['status' => 'expired']);
            return back()->withErrors(['error' => 'This counter offer has expired.']);
        }

        DB::beginTransaction();
        try {
            // Lock product row to prevent oversell
            $product = \App\Models\Product::where('id', $offer->product_id)->lockForUpdate()->first();
            if ($product->available_quantity < $offer->quantity) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Insufficient stock available for this product.']);
            }
            $offer->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // Create vendor inventory from accepted counter offer
            \App\Models\VendorInventory::create([
                'vendor_id' => $offer->vendor_id,
                'product_id' => $offer->product_id,
                'purchase_price' => $offer->fisherman_counter_price ?? $offer->offered_price,
                'quantity' => $offer->quantity,
                'purchased_at' => now(),
                'status' => 'in_stock',
            ]);

            // Decrement product quantity
            $offer->product->decrement('available_quantity', $offer->quantity);

            // Create order (pending_payment)
            $unitPriceCounter = (float) ($offer->fisherman_counter_price ?? $offer->offered_price);
            \App\Models\Order::create([
                'vendor_id' => $offer->vendor_id,
                'fisherman_id' => $offer->fisherman_id,
                'product_id' => $offer->product_id,
                'offer_id' => $offer->id,
                'quantity' => (int) $offer->quantity,
                'unit_price' => $unitPriceCounter,
                'total' => $unitPriceCounter * (int) $offer->quantity,
                'status' => 'pending_payment',
            ]);

            DB::commit();
            
            // Notify fisherman of vendor acceptance of counter
            $fisherman = \App\Models\User::find($offer->fisherman_id);
            if ($fisherman) {
                $fisherman->notify(new \App\Notifications\VendorAcceptedCounter($offer));
            }
            return back()->with('success', 'Counter offer accepted!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to accept counter: ' . $e->getMessage()]);
        }
    }

    /**
     * Vendor declines a fisherman's counter offer
     */
    public function declineCounter(VendorOffer $offer)
    {
        $this->authorize('declineCounter', $offer);
        if ($offer->vendor_id !== Auth::id() || $offer->status !== 'countered') {
            return back()->withErrors(['error' => 'Cannot decline this counter offer.']);
        }
        if ($offer->isExpired()) {
            $offer->update(['status' => 'expired']);
            return back()->withErrors(['error' => 'This counter offer has expired.']);
        }
        $offer->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);

        // Messaging removed

        return back()->with('success', 'Counter offer declined.');
    }

    /**
     * Vendor modifies their pending bid
     */
    public function modifyBid(Request $request, VendorOffer $offer)
    {
        if ($offer->vendor_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Unauthorized.']);
        }

        if (!$offer->canModify()) {
            return back()->withErrors(['error' => 'Cannot modify this bid. It may have expired or been responded to.']);
        }

        $request->validate([
            'offered_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1|max:' . $offer->product->available_quantity,
        ]);

        $oldPrice = $offer->offered_price;
        $offer->update([
            'offered_price' => $request->offered_price,
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Bid updated from ₱' . number_format($oldPrice, 2) . ' to ₱' . number_format($request->offered_price, 2) . '/kg');
    }

    /**
     * Vendor withdraws their pending bid
     */
    public function withdrawBid(VendorOffer $offer)
    {
        if ($offer->vendor_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Unauthorized.']);
        }

        if (!$offer->canWithdraw()) {
            return back()->withErrors(['error' => 'Cannot withdraw this bid. It may have expired or been responded to.']);
        }

        $offer->update([
            'status' => 'withdrawn',
            'responded_at' => now(),
        ]);

        return back()->with('success', 'Bid withdrawn successfully.');
    }

    /**
     * Fisherman closes bidding on a product (auto-rejects all pending bids)
     */
    public function closeBidding(Product $product)
    {
        if ($product->supplier_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Unauthorized.']);
        }

        $rejectedCount = VendorOffer::where('product_id', $product->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'closed',
                'fisherman_message' => 'Bidding closed by fisherman.',
                'responded_at' => now(),
            ]);

        // Notify all affected vendors
        $offers = VendorOffer::where('product_id', $product->id)
            ->where('status', 'closed')
            ->with('vendor')
            ->get();

        foreach ($offers as $offer) {
            if ($offer->vendor) {
                $offer->vendor->notify(new \App\Notifications\BiddingClosed($offer));
            }
        }

        return back()->with('success', "Bidding closed. {$rejectedCount} pending bid(s) rejected.");
    }
}

