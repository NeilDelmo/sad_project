<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat with Seller</title>
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
        <h3>Juan Dela Cruz</h3>
        <p>Fresh Tuna Seller</p>
      </div>
    </div>

    <div class="chat-messages" id="chatMessages">
      <div class="message received">Hi! Interested in the fresh tuna?</div>
      <div class="message sent">Yes, is it still available?</div>
      <div class="message received">Yes, caught this morning. When can you pick up?</div>
    </div>

    <div class="chat-input">
      <textarea id="messageInput" placeholder="Type your message..."></textarea>
      <button class="send-btn" onclick="sendMessage()">Send</button>
    </div>
  </div>

  <script>
    const input = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('chatMessages');

    function sendMessage() {
      const message = input.value.trim();
      if (message) {
        const newMessage = document.createElement('div');
        newMessage.className = 'message sent';
        newMessage.textContent = message;
        messagesContainer.appendChild(newMessage);

        input.value = '';
        input.style.height = '38px'; // reset size
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      }
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
