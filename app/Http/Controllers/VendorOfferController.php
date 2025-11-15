<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\VendorOffer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorOfferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vendor makes an offer on a product
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'offered_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1|max:' . $product->available_quantity,
            'message' => 'nullable|string|max:500',
        ]);

        $offer = VendorOffer::create([
            'vendor_id' => Auth::id(),
            'fisherman_id' => $product->supplier_id,
            'product_id' => $product->id,
            'offered_price' => $request->offered_price,
            'quantity' => $request->quantity,
            'vendor_message' => $request->message,
            'status' => 'pending',
            'expires_at' => now()->addDays(3),
        ]);

        // TODO: Send notification to fisherman

        return redirect()
            ->route('vendor.products.index')
            ->with('success', 'Offer sent to fisherman! They have 3 days to respond.');
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

        $offers = $query->orderByRaw("FIELD(status, 'pending', 'countered', 'accepted', 'rejected', 'expired')")
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
            $offer->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // Create vendor inventory from accepted offer
            $inventory = \App\Models\VendorInventory::create([
                'vendor_id' => $offer->vendor_id,
                'product_id' => $offer->product_id,
                'purchase_price' => $offer->fisherman_counter_price ?? $offer->offered_price,
                'quantity' => $offer->quantity,
                'purchased_at' => now(),
                'status' => 'in_stock',
            ]);

            // Decrement product quantity
            $offer->product->decrement('available_quantity', $offer->quantity);

            DB::commit();

            // TODO: Notify vendor of acceptance

            return back()->with('success', 'Offer accepted! The vendor has been notified.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to accept offer: ' . $e->getMessage()]);
        }
    }

    /**
     * Fisherman rejects an offer
     */
    public function reject(Request $request, VendorOffer $offer)
    {
        $this->authorize('respond', $offer);

        if (!$offer->canRespond()) {
            return back()->withErrors(['error' => 'This offer has expired or already been responded to.']);
        }

        $offer->update([
            'status' => 'rejected',
            'responded_at' => now(),
            'fisherman_message' => $request->message,
        ]);

        // TODO: Notify vendor of rejection

        return back()->with('success', 'Offer rejected.');
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

        // TODO: Notify vendor of counter offer

        return back()->with('success', 'Counter offer sent! Vendor has 2 days to respond.');
    }

    /**
     * Vendor accepts fisherman's counter offer
     */
    public function acceptCounter(VendorOffer $offer)
    {
        if ($offer->vendor_id !== Auth::id() || $offer->status !== 'countered') {
            return back()->withErrors(['error' => 'Cannot accept this offer.']);
        }

        if ($offer->isExpired()) {
            $offer->update(['status' => 'expired']);
            return back()->withErrors(['error' => 'This counter offer has expired.']);
        }

        return $this->accept($offer);
    }
}
