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

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = Order::query();
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
        if (!in_array($order->status, [Order::STATUS_IN_TRANSIT, Order::STATUS_PENDING_PAYMENT])) {
            throw ValidationException::withMessages(['status' => 'Order cannot be marked delivered from current status.']);
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

        // Update vendor inventory status to in_stock now that it's delivered
        \App\Models\VendorInventory::where('order_id', $order->id)
            ->where('status', 'pending_delivery')
            ->update(['status' => 'in_stock']);

        $this->notifyAndMessage($order, "Order #{$order->id} has been delivered. Please confirm receipt.");

        return back()->with('success', 'Order marked as delivered.');
    }

    public function confirmReceived(Order $order)
    {
        $user = Auth::user();
        if ($order->vendor_id !== $user->id) {
            abort(403);
        }
        if ($order->status !== Order::STATUS_DELIVERED) {
            throw ValidationException::withMessages(['status' => 'Order cannot be confirmed received from current status.']);
        }
        $order->update([
            'status' => Order::STATUS_RECEIVED,
            'received_at' => now(),
        ]);

        $this->notifyAndMessage($order, "Order #{$order->id} has been confirmed received.");

        return back()->with('success', 'Order confirmed as received.');
    }

    public function requestRefund(Request $request, Order $order)
    {
        $user = Auth::user();
        if ($order->vendor_id !== $user->id) {
            abort(403);
        }
        if (!in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_RECEIVED])) {
            throw ValidationException::withMessages(['status' => 'Refunds can only be requested after delivery.']);
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

        $this->notifyAndMessage($order, "Refund requested for Order #{$order->id} (Reason: {$data['reason']}).");

        return back()->with('success', 'Refund requested. Fisherman will review your request.');
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
        $this->notifyAndMessage($order, "Refund declined for Order #{$order->id}.");
        return back()->with('success', 'Refund declined.');
    }

    private function notifyAndMessage(Order $order, string $text): void
    {
        // Post a message to conversation
        $conversation = Conversation::firstOrCreate(
            [
                'buyer_id' => $order->vendor_id,
                'seller_id' => $order->fisherman_id,
                'product_id' => $order->product_id,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $text,
            'is_read' => false,
        ]);
        $conversation->update(['last_message_at' => now()]);

        // Notify counterparty (database notification only)
        $counterpartyId = Auth::id() === $order->vendor_id ? $order->fisherman_id : $order->vendor_id;
        $counterparty = User::find($counterpartyId);
        if ($counterparty) {
            $counterparty->notify(new \App\Notifications\OrderStatusUpdated($order, $text));
        }
    }
}
