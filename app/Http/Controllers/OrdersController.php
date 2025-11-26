<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Models\VendorInventory;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = Order::with(['product', 'fisherman', 'vendor']);
        if ($user->user_type === 'vendor') {
            $query->where('vendor_id', $user->id);
        } elseif ($user->user_type === 'fisherman') {
            $query->where('fisherman_id', $user->id);
        } else {
            // Fallback: show none or all depending on admin; here none
            $query->whereRaw('1=0');
        }
        if ($status) {
            $query->where('status', $status);
        }
        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function markInTransit(Order $order)
    {
        $user = Auth::user();
        if ($order->fisherman_id !== $user->id) {
            abort(403);
        }
        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            throw ValidationException::withMessages(['status' => 'Order cannot be marked in transit from current status.']);
        }
        $order->update(['status' => Order::STATUS_IN_TRANSIT]);

        $this->notifyAndMessage($order, "Order #{$order->id} is now in transit.");

        return back()->with('success', 'Order marked as in transit.');
    }

    public function markDelivered(Request $request, Order $order)
    {
        $user = Auth::user();
        if ($order->fisherman_id !== $user->id) {
            abort(403);
        }
        if ($order->status !== Order::STATUS_IN_TRANSIT) {
            throw ValidationException::withMessages(['status' => 'Order must be in transit before it can be marked delivered.']);
        }
        $data = $request->validate([
            'proof' => ['required','image','max:4096'],
            'notes' => ['nullable','string','max:500'],
        ]);

        $path = $request->file('proof')->store('orders/proofs', 'public');

        $order->update([
            'status' => Order::STATUS_DELIVERED,
            'proof_photo_path' => $path,
            'delivery_notes' => $data['notes'] ?? null,
            'delivered_at' => now(),
        ]);

        // DON'T update inventory yet - wait for vendor to confirm receipt
        // Inventory stays as 'pending_delivery' until vendor confirms

        $this->notifyAndMessage($order, "Order #{$order->id} has been delivered. Please confirm receipt.");

        return back()->with('success', 'Order marked as delivered.');
    }

    public function confirmReceived(Order $order)
    {
        $user = Auth::user();
        if ($order->vendor_id !== $user->id) {
            abort(403);
        }
        // Allow confirmation if delivered or refund was declined
        if (!in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_REFUND_DECLINED])) {
            throw ValidationException::withMessages(['status' => 'Order cannot be confirmed received from current status.']);
        }
        $order->update([
            'status' => Order::STATUS_RECEIVED,
            'received_at' => now(),
        ]);

        // NOW update inventory to in_stock when vendor confirms receipt
        $inventory = \App\Models\VendorInventory::where('order_id', $order->id)->first();
        if ($inventory && $inventory->status !== 'in_stock') {
            $inventory->update(['status' => 'in_stock']);
        }

        $this->notifyAndMessage($order, "Order #{$order->id} has been confirmed received.");

        return back()->with('success', 'Order confirmed as received. Products added to your inventory.');
    }

    public function requestRefund(Request $request, Order $order)
    {
        try {
            $user = Auth::user();
            if ($order->vendor_id !== $user->id) {
                abort(403);
            }
            if (!in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_RECEIVED])) {
                throw ValidationException::withMessages(['status' => 'Refunds can only be requested after delivery.']);
            }
            
            // Check if refund was already declined
            if ($order->status === Order::STATUS_REFUND_DECLINED) {
                throw ValidationException::withMessages(['refund' => 'Your refund request was already declined by the fisherman.']);
            }
            
            // Check 3-hour refund window
            if (!$order->isRefundWindowOpen()) {
                throw ValidationException::withMessages(['refund' => 'Refund window has closed. Refunds must be requested within 3 hours of delivery.']);
            }
            
            // Check if product has been sold on marketplace
            $inventory = VendorInventory::where('order_id', $order->id)->first();
            if ($inventory) {
                $hasCustomerOrders = \App\Models\CustomerOrder::whereHas('listing', function($q) use ($inventory) {
                    $q->where('vendor_inventory_id', $inventory->id);
                })->exists();
                
                if ($hasCustomerOrders) {
                    throw ValidationException::withMessages([
                        'refund' => 'Cannot request refund. This product has already been sold to customers on the marketplace. Please contact support for assistance.'
                    ]);
                }
            }
            
            $data = $request->validate([
                'reason' => ['required','in:bad_delivery,poor_quality,never_received,damaged_on_arrival'],
                'notes' => ['nullable','string','max:500'],
                'proof' => ['required','image','max:4096'],
            ]);

            $proof = $request->file('proof')->store('orders/refunds', 'public');

            $order->update([
                'status' => Order::STATUS_REFUND_REQUESTED,
                'refund_reason' => $data['reason'],
                'refund_notes' => $data['notes'] ?? null,
                'refund_proof_path' => $proof,
            ]);

            $inventory = VendorInventory::where('order_id', $order->id)->first();
            if ($inventory) {
                $inventory->update(['status' => 'refund_pending']);
                \App\Models\MarketplaceListing::where('vendor_inventory_id', $inventory->id)
                    ->where('status', 'active')
                    ->update([
                        'status' => 'inactive',
                        'unlisted_at' => now(),
                    ]);
            }

            $this->notifyAndMessage($order, "Refund requested for Order #{$order->id} (Reason: {$data['reason']}).");

            return back()->with('success', 'Refund requested. Fisherman will review your request.');
        } catch (\Exception $e) {
            Log::error('Refund Request Error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function approveRefund(Order $order)
    {
        $user = Auth::user();
        if ($order->fisherman_id !== $user->id) {
            abort(403);
        }
        if ($order->status !== Order::STATUS_REFUND_REQUESTED) {
            throw ValidationException::withMessages(['status' => 'Only requested refunds can be approved.']);
        }
        
        // Restore fisherman's product inventory
        $product = $order->product;
        if ($product) {
            $product->increment('available_quantity', $order->quantity);
        }

        // Handle vendor inventory - remove from their stock since it's being returned
        $vendorInventory = \App\Models\VendorInventory::where('order_id', $order->id)->first();
        if ($vendorInventory) {
            // If it was listed on marketplace, delist it
            \App\Models\MarketplaceListing::where('vendor_inventory_id', $vendorInventory->id)
                ->where('status', 'active')
                ->update(['status' => 'inactive']);
            
            // Mark inventory as refunded
            $vendorInventory->update(['status' => 'refunded']);
        }
        
        $order->update([
            'status' => Order::STATUS_REFUNDED,
            'refund_at' => now(),
        ]);

        $this->notifyAndMessage($order, "Refund approved for Order #{$order->id}.");
        return back()->with('success', 'Refund approved.');
    }

    public function declineRefund(Request $request, Order $order)
    {
        $user = Auth::user();
        if ($order->fisherman_id !== $user->id) {
            abort(403);
        }
        if ($order->status !== Order::STATUS_REFUND_REQUESTED) {
            throw ValidationException::withMessages(['status' => 'Only requested refunds can be declined.']);
        }
        $data = $request->validate([
            'notes' => ['nullable','string','max:500'],
        ]);
        $order->update([
            'status' => Order::STATUS_REFUND_DECLINED,
            'refund_notes' => $data['notes'] ?? $order->refund_notes,
            'refund_at' => now(),
        ]);

        $inventory = \App\Models\VendorInventory::where('order_id', $order->id)->first();
        if ($inventory && $inventory->status === 'refund_pending') {
            $inventory->update(['status' => 'in_stock']);
        }

        $this->notifyAndMessage($order, "Refund declined for Order #{$order->id}.");
        return back()->with('success', 'Refund declined.');
    }

    private function notifyAndMessage(Order $order, string $text): void
    {
        // Send database notification only
        $counterpartyId = Auth::id() === $order->vendor_id ? $order->fisherman_id : $order->vendor_id;
        $counterparty = User::find($counterpartyId);
        if ($counterparty) {
            $counterparty->notify(new \App\Notifications\OrderStatusUpdated($order, $text));
        }
    }
}
