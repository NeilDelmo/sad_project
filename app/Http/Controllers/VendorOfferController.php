<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\VendorOffer;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\NewVendorOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VendorOfferController extends Controller
{
    use AuthorizesRequests;
    
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

        // Create or find a conversation between this vendor (buyer) and fisherman (seller) for this product
        $conversation = Conversation::firstOrCreate(
            [
                'buyer_id' => Auth::id(),
                'seller_id' => $product->supplier_id,
                'product_id' => $product->id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        // Post a message with offer details so the fisherman sees it in their inbox
        $details = sprintf(
            'New offer: ₱%s per unit for %d %s on %s.%s',
            number_format((float) $offer->offered_price, 2),
            (int) $offer->quantity,
            $product->unit_of_measure ?? 'units',
            $product->name,
            $offer->vendor_message ? ' Message: '.$offer->vendor_message : ''
        );

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'message' => $details,
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Send a database notification to the fisherman
        $fisherman = User::find($product->supplier_id);
        if ($fisherman) {
            $fisherman->notify(new NewVendorOffer($offer));
        }

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

        // Create or update a conversation message so vendor sees it in chat
        $conversation = Conversation::firstOrCreate([
            'buyer_id' => $offer->vendor_id,
            'seller_id' => $offer->fisherman_id,
            'product_id' => $offer->product_id,
        ], [
            'last_message_at' => now(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $offer->fisherman_id,
            'body' => 'Fisherman sent a counter offer: ₱' . number_format($offer->fisherman_counter_price, 2) . ' per unit. ' . ($request->message ? 'Message: ' . $request->message : ''),
        ]);

        $conversation->update(['last_message_at' => now()]);

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

            // Post acceptance message in conversation
            $conversation = Conversation::firstOrCreate([
                'buyer_id' => $offer->vendor_id,
                'seller_id' => $offer->fisherman_id,
                'product_id' => $offer->product_id,
            ], [ 'last_message_at' => now() ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'message' => 'Vendor accepted the counter offer at ₱' . number_format($offer->fisherman_counter_price ?? $offer->offered_price, 2) . '.',
                'is_read' => false,
            ]);
            $conversation->update(['last_message_at' => now()]);

            DB::commit();
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

        // Post decline message
        $conversation = Conversation::firstOrCreate([
            'buyer_id' => $offer->vendor_id,
            'seller_id' => $offer->fisherman_id,
            'product_id' => $offer->product_id,
        ], [ 'last_message_at' => now() ]);
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'message' => 'Vendor declined the counter offer.',
            'is_read' => false,
        ]);
        $conversation->update(['last_message_at' => now()]);

        return back()->with('success', 'Counter offer declined.');
    }
}
