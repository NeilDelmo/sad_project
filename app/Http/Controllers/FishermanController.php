<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Product;
use App\Models\VendorOffer;
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

        // Calculate total income from accepted vendor offers
        $totalIncome = VendorOffer::where('fisherman_id', $fisherman->id)
            ->where('status', 'accepted')
            ->sum(DB::raw('offered_price * quantity'));

        // Count accepted offers
        $acceptedOffersCount = VendorOffer::where('fisherman_id', $fisherman->id)
            ->where('status', 'accepted')
            ->count();

        // Get recent accepted offers
        $recentAcceptedOffers = VendorOffer::where('fisherman_id', $fisherman->id)
            ->where('status', 'accepted')
            ->with(['vendor', 'product'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('fisherman.dashboard', compact(
            'productsCount',
            'recentConversations',
            'unreadCount',
            'recentProducts',
            'totalIncome',
            'acceptedOffersCount',
            'recentAcceptedOffers'
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
