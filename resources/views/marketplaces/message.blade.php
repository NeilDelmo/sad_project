<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
  </style>
</head>

<body>
  <div class="chat-wrapper">
    <div class="chat-header">
      <button class="back-btn" onclick="window.history.back()">←</button>
      <div class="seller-info">
        <h3>{{ $conversation->buyer_id === Auth::id() ? $conversation->seller->username : $conversation->buyer->username }}</h3>
        @if($product)
        <p>{{ $product->name }}</p>
        @endif
      </div>
    </div>

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

  <script>
    const conversationId = {{ $conversation->id }};
    const currentUserId = {{ Auth::id() }};
    const input = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('chatMessages');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Load messages on page load
    loadMessages();

    // Auto-refresh messages every 3 seconds
    setInterval(loadMessages, 3000);

    function loadMessages() {
      fetch(`/api/conversations/${conversationId}/messages`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
      })
      .then(response => response.json())
      .then(data => {
        displayMessages(data.messages);
      })
      .catch(error => {
        console.error('Error loading messages:', error);
      });
    }

    function displayMessages(messages) {
      messagesContainer.innerHTML = '';
      
      if (messages.length === 0) {
        messagesContainer.innerHTML = '<div style="text-align: center; color: #999; padding: 20px;"><p>No messages yet. Start the conversation!</p></div>';
        return;
      }

      messages.forEach(msg => {
        const messageDiv = document.createElement('div');
        messageDiv.className = msg.is_own ? 'message sent' : 'message received';
        messageDiv.textContent = msg.message;
        messagesContainer.appendChild(messageDiv);
      });

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
        body: JSON.stringify({ message: message }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          input.value = '';
          input.style.height = '38px';
          loadMessages(); // Reload messages
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
    input.addEventListener('input', function () {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 80) + 'px';
    });

    // Send on Enter (without Shift)
    input.addEventListener('keypress', function (e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
      }
    });
  </script>
</body>

</html>
