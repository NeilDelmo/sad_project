<style>
  /* General layout */
  body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }

  /* Chat container */
  .chat-wrapper {
    width: 360px;
    height: 600px;
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }

  /* Header */
  .chat-header {
    background: #1B5E88;
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
    flex-shrink: 0;
  }

  .back-btn {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
  }

  .seller-info h3 {
    margin: 0;
    font-size: 16px;
  }

  .seller-info p {
    margin: 2px 0 0 0;
    font-size: 12px;
    opacity: 0.8;
  }

  /* Chat messages */
  .chat-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #f9f9f9;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }

  .message {
    max-width: 75%;
    padding: 10px 14px;
    border-radius: 18px;
    line-height: 1.4;
    word-wrap: break-word;
    overflow-wrap: break-word;
    font-size: 14px;
    white-space: pre-wrap;
  }

  .message.received {
    background: #E7FAFE;
    color: #1B5E88;
    border-bottom-left-radius: 5px;
    align-self: flex-start;
  }

  .message.sent {
    background: #0075B5;
    color: white;
    border-bottom-right-radius: 5px;
    align-self: flex-end;
  }

  /* Input area */
  .chat-input {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: #fff;
    border-top: 1px solid #ddd;
  }

  textarea {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 20px;
    outline: none;
    resize: none;
    min-height: 38px;
    max-height: 80px;
    overflow-y: auto;
    font-family: inherit;
    font-size: 14px;
  }

  .send-btn {
    background: #0075B5;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 20px;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.2s;
    margin-left: 8px;
  }

  .send-btn:hover {
    background: #1B5E88;
  }

  /* Scrollbar styling */
  .chat-messages::-webkit-scrollbar {
    width: 6px;
  }

  .chat-messages::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
  }

  /* Toast notification styling */
  .toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #16a34a;
    color: white;
    padding: 16px 24px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    font-weight: 500;
    z-index: 9999;
    animation: slideInRight 0.3s ease;
    max-width: 400px;
  }

  .toast.error {
    background: #dc2626;
  }

  .toast::before {
    content: '✓';
    font-size: 18px;
    font-weight: bold;
  }

  .toast.error::before {
    content: '✕';
  }

  @keyframes slideInRight {
    from {
      transform: translateX(400px);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
</style>
<style>
  /* Offer panel */
  .offer-panel {
    margin: 10px 12px 0 12px;
    background: #fff8e6;
    border: 1px solid #ffe2a8;
    border-radius: 10px;
    padding: 12px;
  }

  .offer-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .offer-title {
    font-weight: bold;
    color: #8a5a00;
    font-size: 14px;
  }

  .offer-status {
    font-size: 12px;
    padding: 3px 8px;
    border-radius: 12px;
    background: #ffe6b8;
    color: #8a5a00;
  }

  .offer-row {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    margin-top: 6px;
  }

  .offer-actions {
    display: flex;
    gap: 8px;
    margin-top: 10px;
  }

  .btn-offer {
    border: none;
    border-radius: 16px;
    padding: 6px 10px;
    font-size: 13px;
    cursor: pointer;
  }

  .btn-accept {
    background: #16a34a;
    color: #fff;
  }

  .btn-reject {
    background: #dc2626;
    color: #fff;
  }

  .btn-counter {
    background: #2563eb;
    color: #fff;
  }

  .counter-form {
    display: none;
    margin-top: 8px;
  }

  .counter-form.show {
    display: block;
  }

  .counter-input {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin: 6px 0;
    font-size: 13px;
  }

  .counter-textarea {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin: 6px 0;
    font-size: 13px;
    resize: vertical;
    min-height: 60px;
  }
</style>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @php $chatFavicon = asset('images/logo.png').'?v=marketplace-chat'; @endphp
  <link rel="icon" type="image/png" href="{{ $chatFavicon }}">
  <link rel="shortcut icon" type="image/png" href="{{ $chatFavicon }}">
  <title>SeaLedger - Chat with {{ $conversation->buyer_id === Auth::id() ? $conversation->seller->username : $conversation->buyer->username }}</title>
  <style>
    /* General layout */
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    /* Chat container */
    .chat-wrapper {
      width: 360px;
      height: 600px;
      display: flex;
      flex-direction: column;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    /* Header */
    .chat-header {
      background: #1B5E88;
      color: white;
      padding: 15px 20px;
      display: flex;
      align-items: center;
      gap: 15px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
      flex-shrink: 0;
    }

    .back-btn {
      background: none;
      border: none;
      color: white;
      font-size: 18px;
      cursor: pointer;
    }

    .seller-info h3 {
      margin: 0;
      font-size: 16px;
    }

    .seller-info p {
      margin: 2px 0 0 0;
      font-size: 12px;
      opacity: 0.8;
    }

    /* Chat messages */
    .chat-messages {
      flex: 1;
      padding: 15px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 10px;
      background: #f9f9f9;
      word-wrap: break-word;
      overflow-wrap: break-word;
    }

    .message {
      max-width: 75%;
      padding: 10px 14px;
      border-radius: 18px;
      line-height: 1.4;
      word-wrap: break-word;
      overflow-wrap: break-word;
      font-size: 14px;
      white-space: pre-wrap;
    }

    .message.received {
      background: #E7FAFE;
      color: #1B5E88;
      border-bottom-left-radius: 5px;
      align-self: flex-start;
    }

    .message.sent {
      background: #0075B5;
      color: white;
      border-bottom-right-radius: 5px;
      align-self: flex-end;
    }

    /* Input area */
    .chat-input {
      display: flex;
      align-items: center;
      padding: 10px;
      background-color: #fff;
      border-top: 1px solid #ddd;
    }

    textarea {
      flex: 1;
      padding: 8px 12px;
      border: 1px solid #ccc;
      border-radius: 20px;
      outline: none;
      resize: none;
      min-height: 38px;
      max-height: 80px;
      overflow-y: auto;
      font-family: inherit;
      font-size: 14px;
    }

    .send-btn {
      background: #0075B5;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 20px;
      cursor: pointer;
      white-space: nowrap;
      transition: background 0.2s;
      margin-left: 8px;
    }

    .send-btn:hover {
      background: #1B5E88;
    }

    /* Scrollbar styling */
    .chat-messages::-webkit-scrollbar {
      width: 6px;
    }

    .chat-messages::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 3px;
    }

    /* Modal */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.45);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 50;
    }

    .modal-overlay.show {
      display: flex;
    }

    .modal-card {
      width: 92%;
      max-width: 380px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      overflow: hidden;
    }

    .modal-header {
      padding: 12px 16px;
      background: #1B5E88;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-title {
      margin: 0;
      font-size: 16px;
    }

    .modal-close {
      background: transparent;
      border: none;
      color: #fff;
      font-size: 18px;
      cursor: pointer;
    }

    .modal-body {
      padding: 14px 16px;
    }

    .modal-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 14px;
    }

    .modal-actions {
      display: flex;
      gap: 8px;
      margin-top: 10px;
    }

    .btn {
      border: none;
      border-radius: 10px;
      padding: 8px 12px;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-accept {
      background: #16a34a;
      color: #fff;
    }

    .btn-reject {
      background: #dc2626;
      color: #fff;
    }

    .btn-secondary {
      background: #e5e7eb;
      color: #111827;
    }

    .modal-form-label {
      font-size: 12px;
      color: #555;
    }

    .modal-input {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-top: 4px;
      font-size: 14px;
    }

    .modal-textarea {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-top: 4px;
      font-size: 14px;
      resize: vertical;
      min-height: 70px;
    }
  </style>
</head>

<body>
  <div class="chat-wrapper">
    <audio id="notifSound" src="/audio/notify.mp3" preload="auto"></audio>
    @if(session('success'))
    <div class="toast" id="toastBox">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="toast error" id="toastBox">{{ $errors->first() }}</div>
    @endif
    <div class="chat-header">
      <button class="back-btn" onclick="window.location.href='{{ Auth::user()->user_type === 'fisherman' ? route('fisherman.dashboard') : (Auth::user()->user_type === 'vendor' ? route('vendor.dashboard') : route('marketplace.shop')) }}'">←</button>
      <div class="seller-info">
        <h3>{{ $conversation->buyer_id === Auth::id() ? $conversation->seller->username : $conversation->buyer->username }}</h3>
        @if($product)
        <p>{{ $product->name }}</p>
        @endif
      </div>
    </div>

    @if(isset($pendingOffer) && $pendingOffer && Auth::id() === $conversation->seller_id)
    <div class="offer-panel" style="margin: 8px 12px;">
      <div class="offer-header">
        <div class="offer-title">Vendor Offer for {{ $product?->name }}</div>
        @if($pendingOffer->status === 'pending')
        <button type="button" class="btn-offer btn-counter" onclick="openOfferModal()">Review Offer</button>
        @elseif($pendingOffer->status === 'countered')
        <span style="color: #2563eb; font-size: 13px; font-weight: 500;">Counter offer sent - Waiting for vendor response</span>
        @elseif($pendingOffer->status === 'accepted')
        <span style="color: #16a34a; font-size: 13px; font-weight: 500;">✓ Offer Accepted</span>
        @elseif($pendingOffer->status === 'rejected')
        <span style="color: #dc2626; font-size: 13px; font-weight: 500;">✗ Offer Rejected</span>
        @endif
      </div>
    </div>
    @endif

    @if(isset($pendingOffer) && $pendingOffer && Auth::id() === $conversation->buyer_id && $pendingOffer->status === 'countered')
    <div class="offer-panel" style="margin: 8px 12px; background: #fff8e6; border: 1px solid #ffe2a8;">
      <div class="offer-header">
        <div class="offer-title">Fisherman Counter Offer for {{ $product?->name }}</div>
        <button type="button" class="btn-offer btn-counter" onclick="openCounterOfferModal()">Review Counter</button>
      </div>
    </div>
    @endif

    <div class="chat-messages" id="chatMessages">
      <!-- Messages will be loaded via AJAX -->
      <div style="text-align: center; color: #999; padding: 20px;">
        <p>Loading messages...</p>
      </div>
    </div>

    <div class="chat-input">
      <textarea id="messageInput" placeholder="Type your message..."></textarea>
      <button class="send-btn" onclick="sendMessage()">Send</button>
    </div>
  </div>

  @if(isset($pendingOffer) && $pendingOffer && Auth::id() === $conversation->seller_id)
  <div id="offerModal" class="modal-overlay">
    <div class="modal-card">
      <div class="modal-header">
        <h4 class="modal-title">Review Vendor Offer</h4>
        <button class="modal-close" onclick="closeOfferModal()">×</button>
      </div>
      <div class="modal-body">
        <div class="modal-row"><span>Product</span><strong>{{ $product?->name }}</strong></div>
        <div class="modal-row"><span>Price</span><strong>₱{{ number_format($pendingOffer->offered_price, 2) }}</strong></div>
        <div class="modal-row"><span>Quantity</span><strong>{{ $pendingOffer->quantity }} {{ $product?->unit_of_measure }}</strong></div>
        @if($pendingOffer->vendor_message)
        <div class="modal-row" style="flex-direction: column; align-items: flex-start;">
          <span class="modal-form-label">Vendor message</span>
          <div style="background:#fff;border:1px solid #eee;border-radius:8px;padding:8px; width:100%; color:#444;">{{ $pendingOffer->vendor_message }}</div>
        </div>
        @endif

        @if($pendingOffer->status === 'pending')
        <div class="modal-actions">
          <form method="POST" action="{{ route('fisherman.offers.accept', $pendingOffer) }}">
            @csrf
            <button type="submit" class="btn btn-accept">Accept</button>
          </form>
          <button type="button" class="btn btn-secondary" onclick="toggleCounterForm('modal-counter')">Counter</button>
          <form method="POST" action="{{ route('fisherman.offers.reject', $pendingOffer) }}">
            @csrf
            <button type="submit" class="btn btn-reject">Reject</button>
          </form>
        </div>
        @elseif($pendingOffer->status === 'countered')
        <div style="text-align: center; padding: 12px; background: #eff6ff; border-radius: 8px; color: #2563eb;">
          <strong>Counter offer sent</strong><br>
          <small>Waiting for vendor to respond to your counter offer</small>
        </div>
        @elseif($pendingOffer->status === 'accepted')
        <div style="text-align: center; padding: 12px; background: #f0fdf4; border-radius: 8px; color: #16a34a;">
          <strong>✓ Offer Accepted</strong><br>
          <small>You have already accepted this offer</small>
        </div>
        @elseif($pendingOffer->status === 'rejected')
        <div style="text-align: center; padding: 12px; background: #fef2f2; border-radius: 8px; color: #dc2626;">
          <strong>✗ Offer Rejected</strong><br>
          <small>You have already rejected this offer</small>
        </div>
        @endif

        @if($pendingOffer->status === 'pending')
        <div id="modal-counter" class="counter-form" style="margin-top:10px;">
          <form method="POST" action="{{ route('fisherman.offers.counter', $pendingOffer) }}">
            @csrf
            <label class="modal-form-label">Counter Price (per {{ $product?->unit_of_measure }})</label>
            <input type="number" step="0.01" min="0" name="counter_price" class="modal-input" value="{{ $product?->unit_price }}" required>
            <label class="modal-form-label" style="margin-top:8px;">Message (optional)</label>
            <textarea name="message" class="modal-textarea" placeholder="Explain your counter offer..."></textarea>
            <div class="modal-actions" style="justify-content: flex-end;">
              <button type="submit" class="btn btn-secondary">Send Counter</button>
            </div>
          </form>
        </div>
        @endif
      </div>
    </div>
  </div>
  @endif

  @if(isset($pendingOffer) && $pendingOffer && Auth::id() === $conversation->buyer_id && $pendingOffer->status === 'countered')
  <div id="counterOfferModal" class="modal-overlay">
    <div class="modal-card">
      <div class="modal-header">
        <h4 class="modal-title">Review Fisherman Counter Offer</h4>
        <button class="modal-close" onclick="closeCounterOfferModal()">×</button>
      </div>
      <div class="modal-body">
        <div class="modal-row"><span>Product</span><strong>{{ $product?->name }}</strong></div>
        <div class="modal-row"><span>Your Original Offer</span><strong>₱{{ number_format($pendingOffer->offered_price, 2) }}</strong></div>
        <div class="modal-row" style="background: #fff8e6;"><span>Fisherman Counter Price</span><strong style="color: #d97706;">₱{{ number_format($pendingOffer->fisherman_counter_price, 2) }}</strong></div>
        <div class="modal-row"><span>Quantity</span><strong>{{ $pendingOffer->quantity }} {{ $product?->unit_of_measure }}</strong></div>
        <div class="modal-row"><span>Total</span><strong>₱{{ number_format($pendingOffer->fisherman_counter_price * $pendingOffer->quantity, 2) }}</strong></div>
        @if($pendingOffer->fisherman_message)
        <div class="modal-row" style="flex-direction: column; align-items: flex-start;">
          <span class="modal-form-label">Fisherman message</span>
          <div style="background:#fff;border:1px solid #eee;border-radius:8px;padding:8px; width:100%; color:#444;">{{ $pendingOffer->fisherman_message }}</div>
        </div>
        @endif

        <div class="modal-actions">
          <form method="POST" action="{{ route('vendor.offers.accept-counter', $pendingOffer) }}">
            @csrf
            <button type="submit" class="btn btn-accept">Accept Counter</button>
          </form>
          <form method="POST" action="{{ route('vendor.offers.decline-counter', $pendingOffer) }}">
            @csrf
            <button type="submit" class="btn btn-reject">Decline</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  @endif

  <script>
    const conversationId = {{ $conversation->id }};
    const currentUserId = {{ Auth::id() }};
    const input = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('chatMessages');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const notifAudio = document.getElementById('notifSound');
    let lastMessageId = 0;
    let isInitialLoad = true;
    let notificationPermission = 'default';

    // Request desktop notification permission
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission().then(permission => {
        notificationPermission = permission;
      });
    } else if ('Notification' in window) {
      notificationPermission = Notification.permission;
    }

    // Load messages on page load
    loadMessages();

    // Auto-refresh messages every 3 seconds
    setInterval(loadMessages, 3000);

    function loadMessages() {
      const url = lastMessageId > 0
        ? `/api/conversations/${conversationId}/messages?since_id=${lastMessageId}`
        : `/api/conversations/${conversationId}/messages`;
      fetch(url, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
          },
        })
        .then(response => response.json())
        .then(data => {
          appendMessages(data.messages || []);
          if (data.last_id) {
            lastMessageId = data.last_id;
          } else if ((data.messages || []).length) {
            lastMessageId = data.messages[data.messages.length - 1].id;
          }
          if (isInitialLoad) { isInitialLoad = false; }
        })
        .catch(error => {
          console.error('Error loading messages:', error);
        });
    }

    function appendMessages(messages) {
      if (!messages.length && messagesContainer.children.length === 0) {
        messagesContainer.innerHTML = '<div style="text-align: center; color: #999; padding: 20px;"><p>No messages yet. Start the conversation!</p></div>';
        return;
      }

      // Remove placeholder if present
      if (messagesContainer.children.length && messagesContainer.children[0].innerText.includes('Loading messages')) {
        messagesContainer.innerHTML = '';
      }

      // NEVER play sound when user is actively viewing this conversation
      // Sound only plays when new messages arrive during polling (handled by background polling on other pages)
      let shouldNotify = false;
      let latestSenderName = '';
      let latestMessage = '';
      
      messages.forEach(msg => {
        const messageDiv = document.createElement('div');
        messageDiv.className = msg.is_own ? 'message sent' : 'message received';
        messageDiv.textContent = msg.message;
        messagesContainer.appendChild(messageDiv);
        
        // Desktop notification only (no sound on conversation page)
        if (!isInitialLoad && lastMessageId > 0 && document.hidden && !msg.is_own) {
          shouldNotify = true;
          latestSenderName = msg.sender_name;
          latestMessage = msg.message;
        }
      });

      // If tab is hidden, optionally play a subtle sound and show desktop notification
      if (shouldNotify && document.hidden) {
        try { if (notifAudio) { notifAudio.currentTime = 0; notifAudio.play().catch(()=>{}); } } catch(e) {}
      }
      if (shouldNotify && notificationPermission === 'granted' && document.hidden) {
        const notification = new Notification(latestSenderName, {
          body: latestMessage.length > 60 ? latestMessage.substring(0, 60) + '...' : latestMessage,
          icon: '/images/logo.png', // Optional: add your logo
          tag: 'sealedger-message',
          requireInteraction: false
        });
        
        notification.onclick = function() {
          window.focus();
          notification.close();
        };
        
        setTimeout(() => notification.close(), 5000);
      }

      // Scroll to bottom
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function sendMessage() {
      const message = input.value.trim();
      if (!message) return;

      // Disable button while sending
      const sendBtn = document.querySelector('.send-btn');
      sendBtn.disabled = true;
      sendBtn.textContent = 'Sending...';

      fetch(`/api/conversations/${conversationId}/messages`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            message: message
          }),
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            input.value = '';
            input.style.height = '38px';
            // Optimistically append own message
            appendMessages([data.message]);
            lastMessageId = data.message.id;
          }
        })
        .catch(error => {
          console.error('Error sending message:', error);
          alert('Failed to send message. Please try again.');
        })
        .finally(() => {
          sendBtn.disabled = false;
          sendBtn.textContent = 'Send';
        });
    }

    // Auto resize textarea
    input.addEventListener('input', function() {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 80) + 'px';
    });

    // Send on Enter (without Shift)
    input.addEventListener('keypress', function(e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });

    function toggleCounterForm(id) {
      const el = document.getElementById(id);
      if (!el) return;
      el.classList.toggle('show');
    }

    function openOfferModal() {
      const m = document.getElementById('offerModal');
      if (m) m.classList.add('show');
    }

    function closeOfferModal() {
      const m = document.getElementById('offerModal');
      if (m) m.classList.remove('show');
    }

    function openCounterOfferModal() {
      const m = document.getElementById('counterOfferModal');
      if (m) m.classList.add('show');
    }

    function closeCounterOfferModal() {
      const m = document.getElementById('counterOfferModal');
      if (m) m.classList.remove('show');
    }

    // Auto-hide toast
    const toast = document.getElementById('toastBox');
    if (toast) {
      setTimeout(() => toast.style.display = 'none', 3000);
    }
  </script>
</body>

</html>