<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\MarketplaceListing;
use App\Models\VendorInventory;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        $q = CustomerOrder::with(['listing.product','buyer','vendor']);
        if ($user->user_type === 'buyer') {
            $q->where('buyer_id', $user->id);
        } elseif ($user->user_type === 'vendor') {
            $q->where('vendor_id', $user->id);
        } else {
            $q->whereRaw('1=0');
        }
        if ($status) $q->where('status', $status);
        $orders = $q->latest()->paginate(15)->withQueryString();
        return view('marketplaces.orders', compact('orders'));
    }

    public function purchase(Request $request, MarketplaceListing $listing)
    {
        $user = Auth::user();
        if (!$user) abort(401);
        if ($user->id === $listing->seller_id) {
            throw ValidationException::withMessages(['quantity' => 'You cannot purchase your own listing.']);
        }
        $data = $request->validate([
            'quantity' => ['required','integer','min:1']
        ]);
        $qty = (int)$data['quantity'];

        return DB::transaction(function () use ($listing, $qty, $user) {
            // Lock vendor inventory
            $inventory = VendorInventory::where('id', $listing->vendor_inventory_id)->lockForUpdate()->first();
            if (!$inventory || $inventory->quantity < $qty) {
                throw ValidationException::withMessages(['quantity' => 'Not enough stock available.']);
            }
            $unitPrice = $listing->final_price ?? $listing->asking_price ?? $listing->dynamic_price ?? 0;
            $total = $unitPrice * $qty;

            $order = CustomerOrder::create([
                'buyer_id' => $user->id,
                'vendor_id' => $listing->seller_id,
                'listing_id' => $listing->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'total' => $total,
                'status' => 'pending_payment', // COD default
            ]);

            $inventory->decrement('quantity', $qty);
            if ($inventory->quantity <= 0) {
                $listing->update(['status' => 'inactive', 'unlisted_at' => now()]);
            }

            // Message + notify
            $conversation = Conversation::firstOrCreate([
                'buyer_id' => $user->id,
                'seller_id' => $listing->seller_id,
                'product_id' => $listing->product_id,
            ], ['last_message_at' => now()]);
            $conversation->messages()->create([
                'sender_id' => $user->id,
                'message' => "Placed order #{$order->id} for {$qty} kg.",
                'is_read' => false,
            ]);
            $conversation->update(['last_message_at' => now()]);

            $vendor = User::find($listing->seller_id);
            if ($vendor) $vendor->notify(new \App\Notifications\CustomerOrderStatusUpdated($order, 'New order placed'));

            return redirect()->route('marketplace.orders.index')->with('success', 'Order placed.');
        });
    }

    public function vendorDelivered(Request $request, CustomerOrder $order)
    {
        $user = Auth::user();
        if ($order->vendor_id !== $user->id) abort(403);
        $data = $request->validate([
            'proof' => ['required','image','max:4096'],
            'notes' => ['nullable','string','max:500']
        ]);
        $path = $request->file('proof')->store('customer_orders/proofs', 'public');
        $order->update([
            'status' => 'delivered',
            'proof_photo_path' => $path,
            'delivered_at' => now(),
        ]);
        $this->notify($order, "Order #{$order->id} delivered. Please confirm receipt.");
        return back()->with('success', 'Marked delivered.');
    }

    public function buyerReceived(CustomerOrder $order)
    {
        $user = Auth::user();
        if ($order->buyer_id !== $user->id) abort(403);
        if ($order->status !== 'delivered') {
            throw ValidationException::withMessages(['status' => 'Only delivered orders can be confirmed received.']);
        }
        $order->update(['status' => 'received', 'received_at' => now()]);
        $this->notify($order, "Order #{$order->id} confirmed received.");
        return back()->with('success', 'Order confirmed received.');
    }

    private function notify(CustomerOrder $order, string $text): void
    {
        $conversation = Conversation::firstOrCreate([
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->vendor_id,
            'product_id' => optional($order->listing)->product_id,
        ], ['last_message_at' => now()]);
        $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $text,
            'is_read' => false,
        ]);
        $conversation->update(['last_message_at' => now()]);

        $counterpartyId = Auth::id() === $order->buyer_id ? $order->vendor_id : $order->buyer_id;
        $counterparty = User::find($counterpartyId);
        if ($counterparty) $counterparty->notify(new \App\Notifications\CustomerOrderStatusUpdated($order, $text));
    }
}
