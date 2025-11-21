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
            
            // Determine title if missing
            $title = data_get($data, 'title');
            if (!$title) {
                $title = match($type) {
                    'new_vendor_offer' => 'New Offer Received',
                    'counter_vendor_offer' => 'Counter Offer Received',
                    'vendor_offer_accepted' => 'Offer Accepted',
                    'vendor_accepted_counter' => 'Counter Offer Accepted',
                    'order_status' => 'Order Update',
                    'customer_order' => 'New Order',
                    default => 'Notification'
                };
            }

            return [
                'id' => $n->id,
                'type' => $type,
                'title' => $title,
                'message' => data_get($data, 'message'),
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
