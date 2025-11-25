<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back();
    }

    public function markAllRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back();
    }

    // JSON: get unread count
    public function unreadCount(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }
        return response()->json(['count' => $user->unreadNotifications()->count()]);
    }

    // JSON: latest unread (top 5)
    public function latest(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'items' => []]);
        }
        $items = $user->unreadNotifications()->latest()->limit(5)->get()->map(function ($n) {
            $data = $n->data;
            $type = data_get($data, 'type', 'notification');
            
            // Fix for legacy NewCatchAvailable notifications which lacked type/title/message
            if ($type === 'notification' && isset($data['product_id']) && isset($data['name']) && !isset($data['title'])) {
                $type = 'new_catch_available';
                $data['title'] = 'New Catch Available';
                $data['message'] = sprintf('New catch available: %s', $data['name']);
                $data['link'] = route('vendor.products.index', ['q' => $data['name']]);
            }

            // Determine title if missing
            $title = data_get($data, 'title');
            if (!$title) {
                $title = match($type) {
                    'new_vendor_offer' => 'New Offer Received',
                    'counter_vendor_offer' => 'Counter Offer Received',
                    'vendor_offer_accepted' => 'Offer Accepted',
                    'vendor_accepted_counter' => 'Counter Offer Accepted',
                    'vendor_offer_rejected' => 'Offer Rejected',
                    'offer_expired' => 'Offer Expired',
                    'bidding_closed' => 'Bidding Closed',
                    'order_status' => 'Order Update',
                    'customer_order', 'customer_order_status' => 'New Order',
                    'rental_approved' => 'Rental Approved',
                    'rental_rejected' => 'Rental Rejected',
                    'rental_issue_reported' => 'Rental Issue',
                    'return_processed' => 'Return Processed',
                    'new_catch_available' => 'New Catch Available',
                    default => 'Notification'
                };
            }

            return [
                'id' => $n->id,
                'type' => $type,
                'title' => $title,
                'message' => data_get($data, 'message') ?? 'No details available.',
                'link' => data_get($data, 'link', route('notifications.index')),
                'created_at' => optional($n->created_at)->diffForHumans(),
            ];
        });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'items' => $items,
        ]);
    }
}
