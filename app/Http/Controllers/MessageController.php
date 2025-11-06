<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
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

        return view('marketplaces.message', compact('conversation', 'product'));
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
    public function getMessages($conversationId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $conversation = Conversation::with(['messages.sender'])
            ->findOrFail($conversationId);

        // Ensure user is part of this conversation
        if ($conversation->buyer_id !== Auth::id() && $conversation->seller_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mark messages as read
        $this->markMessagesAsRead($conversation);

        $messages = $conversation->messages->map(function ($message) {
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
}
