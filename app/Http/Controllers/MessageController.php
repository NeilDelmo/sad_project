<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Models\VendorOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Show conversation page (by conversation ID)
     */
    public function show($conversationId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access messages');
        }

        $conversation = Conversation::with(['buyer', 'seller', 'product', 'messages.sender'])
            ->findOrFail($conversationId);
        
        // Ensure user is part of this conversation
        if ($conversation->buyer_id !== Auth::id() && $conversation->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized access to conversation');
        }

        // Mark messages as read
        $this->markMessagesAsRead($conversation);

        $product = $conversation->product;

        // Find a latest active offer between buyer (vendor) and seller (fisherman) for this product
        $pendingOffer = VendorOffer::where('vendor_id', $conversation->buyer_id)
            ->where('fisherman_id', $conversation->seller_id)
            ->where('product_id', $conversation->product_id)
            ->whereIn('status', ['pending', 'countered'])
            ->latest()
            ->first();

        return view('marketplaces.message', compact('conversation', 'product', 'pendingOffer'));
    }

    /**
     * Start conversation about a product
     */
    public function startConversation($productId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to message sellers');
        }

        $product = Product::with('supplier')->findOrFail($productId);
        
        // Check if user is trying to message themselves
        if ($product->supplier_id === Auth::id()) {
            return redirect()->route('marketplace.shop')->with('error', 'You cannot message yourself');
        }
        
        // Check if conversation already exists
        $conversation = Conversation::where('buyer_id', Auth::id())
            ->where('seller_id', $product->supplier_id)
            ->where('product_id', $productId)
            ->with(['messages.sender'])
            ->first();

        // Create new conversation if doesn't exist
        if (!$conversation) {
            $conversation = Conversation::create([
                'buyer_id' => Auth::id(),
                'seller_id' => $product->supplier_id,
                'product_id' => $productId,
                'last_message_at' => now(),
            ]);
        }

        return view('marketplaces.message', compact('conversation', 'product'));
    }

    /**
     * Get messages for a conversation
     */
    public function getMessages(Request $request, $conversationId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $conversation = Conversation::findOrFail($conversationId);

        // Ensure user is part of this conversation
        if ($conversation->buyer_id !== Auth::id() && $conversation->seller_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Optional incremental fetch using since_id (only return new messages)
        $sinceId = (int) $request->query('since_id', 0);

        $messagesQuery = Message::with('sender')
            ->where('conversation_id', $conversation->id)
            ->orderBy('id', 'asc');

        if ($sinceId > 0) {
            $messagesQuery->where('id', '>', $sinceId);
        }

        $messageModels = $messagesQuery->get();

        // Mark only other-party unread messages as read
        if ($messageModels->isNotEmpty()) {
            Message::where('conversation_id', $conversation->id)
                ->where('sender_id', '!=', Auth::id())
                ->whereIn('id', $messageModels->pluck('id'))
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        $messages = $messageModels->map(function ($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->username,
                'is_own' => $message->sender_id === Auth::id(),
                'created_at' => $message->created_at->format('h:i A'),
                'created_at_human' => $message->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'messages' => $messages,
            'current_user_id' => Auth::id(),
            'last_id' => $messages->last()['id'] ?? $sinceId,
        ]);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request, $conversationId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $conversation = Conversation::findOrFail($conversationId);

        // Ensure user is part of this conversation
        if ($conversation->buyer_id !== Auth::id() && $conversation->seller_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Update conversation last message time
        $conversation->update([
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'sender_name' => Auth::user()->username,
                'is_own' => true,
                'created_at' => $message->created_at->format('h:i A'),
                'created_at_human' => $message->created_at->diffForHumans(),
            ],
        ]);
    }

    /**
     * Mark messages as read
     */
    private function markMessagesAsRead(Conversation $conversation)
    {
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get unread message count for current user
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $unreadCount = Conversation::where(function($q) use ($user) {
            $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
        })->whereHas('messages', function($q) use ($user) {
            $q->where('is_read', false)->where('sender_id', '!=', $user->id);
        })->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
}
