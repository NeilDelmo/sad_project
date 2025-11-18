<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferNotificationController extends Controller
{
    /**
     * Get count of pending offers for the authenticated user
     */
    public function getPendingCount()
    {
        $user = Auth::user();
        
        if ($user->user_type === 'fisherman') {
            // Fisherman: count pending offers on their products
            $count = \App\Models\VendorOffer::where('fisherman_id', $user->id)
                ->where('status', 'pending')
                ->count();
        } elseif ($user->user_type === 'vendor') {
            // Vendor: count countered offers awaiting their response
            $count = \App\Models\VendorOffer::where('vendor_id', $user->id)
                ->where('status', 'countered')
                ->count();
        } else {
            $count = 0;
        }

        return response()->json(['pending_count' => $count]);
    }

    /**
     * Get latest unread offer notifications (shown once)
     */
    public function getLatestOffers()
    {
        $user = Auth::user();
        
        // Get unread database notifications about offers
        $notifications = $user->unreadNotifications()
            ->whereIn('type', [
                'App\Notifications\NewVendorOffer',
                'App\Notifications\CounterVendorOffer',
                'App\Notifications\VendorOfferAccepted',
                'App\Notifications\VendorAcceptedCounter',
            ])
            ->limit(5)
            ->get();

        $offers = $notifications->map(function ($notification) {
            $data = $notification->data;
            return [
                'id' => $notification->id,
                'type' => $data['type'] ?? 'offer',
                'title' => $this->getNotificationTitle($data),
                'message' => $this->getNotificationMessage($data),
                'link' => $data['link'] ?? $this->getNotificationLink($data),
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        });

        return response()->json(['offers' => $offers]);
    }

    /**
     * Mark offer notification as read
     */
    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->unreadNotifications()->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    private function getNotificationTitle($data)
    {
        return match($data['type'] ?? '') {
            'new_vendor_offer' => 'New Bid on ' . ($data['product_name'] ?? 'Your Product'),
            'counter_vendor_offer' => 'Counter Offer on ' . ($data['product_name'] ?? 'Product'),
            'vendor_offer_accepted' => 'Offer Accepted!',
            'vendor_accepted_counter' => 'Counter Offer Accepted!',
            default => 'New Offer Notification',
        };
    }

    private function getNotificationMessage($data)
    {
        return match($data['type'] ?? '') {
            'new_vendor_offer' => ($data['vendor_name'] ?? 'A vendor') . ' bid ₱' . number_format($data['offered_price'] ?? 0, 2) . ' for ' . ($data['quantity'] ?? 0) . ' units',
            'counter_vendor_offer' => 'Fisherman countered at ₱' . number_format($data['counter_price'] ?? 0, 2),
            'vendor_offer_accepted' => 'Your offer was accepted by the fisherman',
            'vendor_accepted_counter' => 'Vendor accepted your counter offer',
            default => 'You have a new offer notification',
        };
    }

    private function getNotificationLink($data)
    {
        $userType = Auth::user()->user_type;
        
        if ($userType === 'fisherman') {
            return '/fisherman/offers?status=pending';
        } elseif ($userType === 'vendor') {
            return '/vendor/offers?status=countered';
        }
        
        return '/dashboard';
    }
}
