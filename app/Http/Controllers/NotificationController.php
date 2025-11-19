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
        return back();
    }

    public function markAllRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
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
            return [
                'id' => $n->id,
                'title' => data_get($n->data, 'title', 'Notification'),
                'message' => data_get($n->data, 'message'),
                'link' => data_get($n->data, 'link', route('notifications.index')),
                'created_at' => optional($n->created_at)->diffForHumans(),
            ];
        });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'items' => $items,
        ]);
    }
}
