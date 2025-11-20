<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap5/css/bootstrap.min.css') }}" />
    <script src="https://kit.fontawesome.com/19696dbec5.js" crossorigin="anonymous"></script>
    <title>SeaLedger - Messages</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Koulen&display=swap');

        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-brand {
            color: white;
            font-size: 28px;
            font-weight: bold;
            font-family: "Koulen", sans-serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-logo {
            height: 40px;
            width: auto;
        }

        .nav-links {
            display: flex;
            gap: 10px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: white;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.15);
        }

        .nav-link:hover::before {
            transform: translateX(0);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .container-main {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        .page-title {
            font-family: "Koulen", sans-serif;
            font-size: 42px;
            color: #1B5E88;
            margin-bottom: 10px;
        }

        .page-subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }

        .conversations-list {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
        }

        .conversation-item:last-child {
            border-bottom: none;
        }

        .conversation-item:hover {
            background: #f8f9fa;
        }

        .conversation-item.unread {
            background: #E7FAFE;
        }

        .conversation-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0075B5, #1B5E88);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .conversation-content {
            flex-grow: 1;
            min-width: 0;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .buyer-name {
            font-size: 18px;
            font-weight: bold;
            color: #1B5E88;
        }

        .conversation-time {
            font-size: 13px;
            color: #999;
        }

        .product-name {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .last-message {
            font-size: 14px;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .unread-badge {
            background: #dc3545;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 25px;
        }

        .empty-state h2 {
            font-family: "Koulen", sans-serif;
            font-size: 36px;
            color: #1B5E88;
            margin-bottom: 15px;
        }

        .empty-state p {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }

        .btn-back {
            background: white;
            color: #0075B5;
            padding: 12px 25px;
            border: 2px solid #0075B5;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            margin-bottom: 30px;
        }

        .btn-back:hover {
            background: #E7FAFE;
        }
    </style>
</head>
<body>

    @include('fisherman.partials.nav')

    @include('partials.toast-notifications')

    <div class="container-main">
        <a href="{{ route('fisherman.dashboard') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <h1 class="page-title">Messages</h1>
        <p class="page-subtitle">Conversations with buyers about your products</p>

        <div class="conversations-list">
            @if($conversations->count() > 0)
                @foreach($conversations as $conversation)
                <a href="{{ route('marketplace.message', $conversation->id) }}" 
                   class="conversation-item {{ $conversation->unread_count > 0 ? 'unread' : '' }}">
                    <div class="conversation-avatar">
                        {{ strtoupper(substr($conversation->buyer->username ?? $conversation->buyer->email, 0, 1)) }}
                    </div>
                    <div class="conversation-content">
                        <div class="conversation-header">
                            <span class="buyer-name">
                                {{ $conversation->buyer->username ?? $conversation->buyer->email }}
                            </span>
                            <div class="d-flex align-items-center">
                                <span class="conversation-time">
                                    {{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : 'No messages yet' }}
                                </span>
                                @if($conversation->unread_count > 0)
                                    <span class="unread-badge">{{ $conversation->unread_count }} new</span>
                                @endif
                            </div>
                        </div>
                        <div class="product-name">
                            <i class="fa-solid fa-fish" style="color: #0075B5; margin-right: 5px;"></i>
                            About: {{ $conversation->product->name ?? 'Product' }}
                        </div>
                        @if($conversation->latestMessage)
                        <div class="last-message">
                            <strong>{{ $conversation->latestMessage->sender_id == Auth::id() ? 'You' : $conversation->buyer->username ?? 'Buyer' }}:</strong>
                            {{ Str::limit($conversation->latestMessage->message, 60) }}
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-envelope"></i>
                    <h2>No Messages Yet</h2>
                    <p>When buyers message you about your products, conversations will appear here.</p>
                    <a href="{{ route('fisherman.products.index') }}" class="btn-back" style="margin: 0;">
                        View My Products
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Refresh conversation list when returning from conversation page
        window.addEventListener('focus', function() {
            // Reload to get fresh unread counts (simple solution)
            // Only reload if we came from a conversation (check referrer or use session flag)
            const lastPath = document.referrer;
            if (lastPath && lastPath.includes('/marketplace/message/')) {
                window.location.reload();
            }
        });
    </script>

    @include('partials.message-notification')

</body>
</html>
