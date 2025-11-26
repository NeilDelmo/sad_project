<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\MarketplaceListing;
use App\Models\VendorInventory;
use App\Models\Conversation;
use App\Models\User;
use App\Models\OrganizationRevenue;
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
        if ($user->user_type !== 'buyer') {
            abort(403, 'Only buyers can place orders.');
        }
        if ($user->id === $listing->seller_id) {
            throw ValidationException::withMessages(['quantity' => 'You cannot purchase your own listing.']);
        }
        
        // Check if seller is active
        if ($listing->seller && $listing->seller->account_status !== 'active') {
            throw ValidationException::withMessages(['error' => 'This seller is currently unavailable.']);
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
            // Use final_price as authoritative price (the AI-optimized buyer-facing price)
            $unitPrice = $listing->final_price ?? $listing->asking_price ?? 0;
            if ($unitPrice <= 0) {
                throw ValidationException::withMessages(['price' => 'Invalid listing price.']);
            }
            $total = $unitPrice * $qty;

            $platformUnitFee = $listing->platform_fee ?? 0; // fee per unit stored when listing created
            $orderPlatformFee = $platformUnitFee * $qty; // total fee for this order (recorded but not yet collected)

            $order = CustomerOrder::create([
                'buyer_id' => $user->id,
                'vendor_id' => $listing->seller_id,
                'listing_id' => $listing->id,
                'quantity' => $qty,
                'unit_price' => $unitPrice,
                'total' => $total,
                'platform_fee' => $orderPlatformFee,
                'status' => 'pending_payment', // COD default
            ]);

            $inventory->decrement('quantity', $qty);
            
            // Keep listing active even if stock is 0 so it shows as "Sold Out"
            // if ($inventory->quantity <= 0) {
            //     $listing->update(['status' => 'inactive', 'unlisted_at' => now()]);
            // }

            // Notify vendor
            $vendor = User::find($listing->seller_id);
            if ($vendor) $vendor->notify(new \App\Notifications\CustomerOrderStatusUpdated($order, 'New order placed'));

            return redirect()->route('marketplace.orders.index')->with('success', 'Order placed.');
        });
    }

    public function markInTransit(CustomerOrder $order)
    {
        $user = Auth::user();
        if ($order->vendor_id !== $user->id) abort(403);
        if ($order->status !== CustomerOrder::STATUS_PENDING_PAYMENT) {
            throw ValidationException::withMessages(['status' => 'Only pending orders can be marked in transit.']);
        }
        $order->update(['status' => CustomerOrder::STATUS_IN_TRANSIT]);
        $this->notify($order, "Order #{$order->id} is now in transit.");
        return back()->with('success', 'Order marked as in transit.');
    }

    public function vendorDelivered(Request $request, CustomerOrder $order)
    {
        $user = Auth::user();
        if ($order->vendor_id !== $user->id) abort(403);
        if ($order->status !== CustomerOrder::STATUS_IN_TRANSIT) {
            throw ValidationException::withMessages(['status' => 'Order must be marked in transit before it can be delivered.']);
        }
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

        // Trust score: reward vendor for successful completed order
        $vendor = User::find($order->vendor_id);
        if ($vendor && method_exists($vendor, 'adjustTrustScore')) {
            $vendor->adjustTrustScore(2, 'order_completed', $order, 'Order received without issues');
        }

        // Record platform fee revenue once order is successfully received (finalized)
        if ($order->platform_fee && $order->platform_fee > 0) {
            $existing = \App\Models\OrganizationRevenue::where('order_id', $order->id)->where('type', 'platform_fee')->first();
            if (!$existing) {
                \App\Models\OrganizationRevenue::create([
                    'order_id' => $order->id,
                    'listing_id' => $order->listing_id,
                    'vendor_id' => $order->vendor_id,
                    'buyer_id' => $order->buyer_id,
                    'amount' => $order->platform_fee,
                    'type' => 'platform_fee',
                    'collected_at' => now(),
                ]);
            }
        }
        $this->notify($order, "Order #{$order->id} confirmed received.");
        return back()->with('success', 'Order confirmed received.');
    }

    public function requestRefund(Request $request, CustomerOrder $order)
    {
        $user = Auth::user();
        if ($order->buyer_id !== $user->id) abort(403);
        if (!in_array($order->status, ['delivered', 'received'])) {
            throw ValidationException::withMessages(['status' => 'Refunds can only be requested after delivery.']);
        }
        
        // Check if refund was already declined
        if ($order->status === CustomerOrder::STATUS_REFUND_DECLINED) {
            throw ValidationException::withMessages(['refund' => 'Your refund request was already declined by the vendor.']);
        }
        
        // Check 3-hour refund window
        if (!$order->isRefundWindowOpen()) {
            throw ValidationException::withMessages(['refund' => 'Refund window has closed. Refunds must be requested within 3 hours of delivery.']);
        }
        $data = $request->validate([
            'reason' => ['required','in:bad_delivery,poor_quality,never_received,damaged_on_arrival'],
            'notes' => ['nullable','string','max:500'],
            'proof' => ['required','image','max:4096'],
        ]);
        $proof = $request->file('proof')->store('customer_orders/refunds', 'public');
        $order->update([
            'status' => CustomerOrder::STATUS_REFUND_REQUESTED,
            'refund_reason' => $data['reason'],
            'refund_notes' => $data['notes'] ?? null,
            'refund_proof_path' => $proof,
        ]);
        $this->notify($order, "Refund requested for Order #{$order->id} (Reason: {$data['reason']}).");
        return back()->with('success', 'Refund requested. Vendor will review.');
    }

    public function approveRefund(CustomerOrder $order)
    {
        $user = Auth::user();
        if ($order->vendor_id !== $user->id) abort(403);
        if ($order->status !== CustomerOrder::STATUS_REFUND_REQUESTED) {
            throw ValidationException::withMessages(['status' => 'Only requested refunds can be approved.']);
        }
        
        // Restore vendor inventory
        $listing = $order->listing;
        if ($listing && $listing->vendor_inventory_id) {
            VendorInventory::where('id', $listing->vendor_inventory_id)
                ->increment('quantity', $order->quantity);
        }
        
        $order->update([
            'status' => CustomerOrder::STATUS_REFUNDED,
            'refund_at' => now(),
        ]);
        // Trust penalty: refund approved against vendor
        $vendor = User::find($order->vendor_id);
        if ($vendor && method_exists($vendor, 'adjustTrustScore')) {
            $vendor->adjustTrustScore(-10, 'refund_penalty', $order, 'Refund approved against vendor');
        }
        // Optional: future logic could reverse revenue; currently fee only recognized upon receipt.
        $this->notify($order, "Refund approved for Order #{$order->id}.");
        return back()->with('success', 'Refund approved.');
    }

    public function declineRefund(Request $request, CustomerOrder $order)
    {
        $user = Auth::user();
        if ($order->vendor_id !== $user->id) abort(403);
        if ($order->status !== CustomerOrder::STATUS_REFUND_REQUESTED) {
            throw ValidationException::withMessages(['status' => 'Only requested refunds can be declined.']);
        }
        $data = $request->validate(['notes' => ['nullable','string','max:500']]);
        
        $newNotes = $order->refund_notes;
        if (!empty($data['notes'])) {
            $newNotes .= "\n\n[Vendor Rejection Reason]: " . $data['notes'];
        }

        $order->update([
            'status' => CustomerOrder::STATUS_REFUND_DECLINED,
            'refund_notes' => $newNotes,
            'refund_at' => now(),
        ]);
        // No revenue reversal; order not refunded.
        $this->notify($order, "Refund declined for Order #{$order->id}.");
        return back()->with('success', 'Refund declined.');
    }

    private function notify(CustomerOrder $order, string $text): void
    {
        // Send database notification only
        $counterpartyId = Auth::id() === $order->buyer_id ? $order->vendor_id : $order->buyer_id;
        $counterparty = User::find($counterpartyId);
        if ($counterparty) $counterparty->notify(new \App\Notifications\CustomerOrderStatusUpdated($order, $text));
    }
}
