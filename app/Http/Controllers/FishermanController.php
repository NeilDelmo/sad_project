<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Product;
use App\Models\Order;
use App\Models\Rental;
use App\Models\VendorOffer;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FishermanController extends Controller
{
    /**
     * Display the fisherman dashboard
     */
    public function dashboard()
    {
        $fisherman = Auth::user();

        // Get fisherman's products count
        $productsCount = Product::where('supplier_id', $fisherman->id)->count();

        // Get recent conversations (messages from buyers)
        $recentConversations = Conversation::where('seller_id', $fisherman->id)
            ->with(['buyer', 'product', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get();

        // Count unread messages
        $unreadCount = Conversation::where('seller_id', $fisherman->id)
            ->whereHas('messages', function($query) use ($fisherman) {
                $query->where('is_read', false)
                      ->where('sender_id', '!=', $fisherman->id);
            })
            ->count();

        // Get recent products
        $recentProducts = Product::where('supplier_id', $fisherman->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate total income from delivered orders (selling to vendors)
        $totalIncome = Order::where('fisherman_id', $fisherman->id)
            ->where('status', Order::STATUS_DELIVERED)
            ->sum('total');

        // Calculate total spending from rentals (equipment/gear rentals)
        $totalSpending = Rental::where('user_id', $fisherman->id)
            ->whereIn('status', ['completed', 'active', 'returned'])
            ->sum('total_charges');

        // Count accepted offers
        $acceptedOffersCount = VendorOffer::where('fisherman_id', $fisherman->id)
            ->where('status', 'accepted')
            ->count();

        // Get recent offers (all statuses)
        $recentAcceptedOffers = VendorOffer::where('fisherman_id', $fisherman->id)
            ->with(['vendor', 'product'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get rental statistics
        $activeRentalsCount = Rental::where('user_id', $fisherman->id)
            ->whereIn('status', ['pending', 'approved', 'active'])
            ->count();

        $pendingRentalsCount = Rental::where('user_id', $fisherman->id)
            ->where('status', 'pending')
            ->count();

        return view('fisherman.dashboard', compact(
            'productsCount',
            'recentConversations',
            'unreadCount',
            'recentProducts',
            'totalIncome',
            'totalSpending',
            'acceptedOffersCount',
            'recentAcceptedOffers',
            'activeRentalsCount',
            'pendingRentalsCount'
        ));
    }

    /**
     * Display inbox with all conversations
     */
    public function inbox()
    {
        $conversations = Conversation::where('seller_id', Auth::id())
            ->with(['buyer', 'product', 'latestMessage', 'messages'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Add unread count for each conversation
        $conversations->each(function($conversation) {
            $conversation->unread_count = $conversation->messages()
                ->where('is_read', false)
                ->where('sender_id', '!=', Auth::id())
                ->count();
        });

        return view('fisherman.messages.inbox', compact('conversations'));
    }
}
