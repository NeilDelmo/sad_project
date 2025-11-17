@auth
@php
    // Don't show toasts on message/conversation pages
    $isMessagePage = request()->routeIs('marketplace.message') || 
                     request()->is('marketplace/message/*') ||
                     request()->is('*/messages/*') ||
                     str_contains(request()->path(), '/message');
@endphp

@if(!$isMessagePage)
<style>
    /* Toast Notification Styles */
    .toast-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    }

    .toast {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        margin-bottom: 15px;
        padding: 16px;
        display: flex;
        align-items: start;
        gap: 12px;
        animation: slideInRight 0.3s ease;
        cursor: pointer;
        transition: all 0.3s;
        border-left: 4px solid #0075B5;
    }

    .toast:hover {
        transform: translateX(-5px);
        box-shadow: 0 6px 25px rgba(0,0,0,0.25);
    }

    .toast.fade-out {
        animation: slideOutRight 0.3s ease forwards;
    }

    @keyframes slideInRight {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }

    .toast-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #1B5E88 0%, #0075B5 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }

    .toast-content {
        flex: 1;
        min-width: 0;
    }

    .toast-title {
        font-weight: 700;
        color: #1B5E88;
        margin-bottom: 4px;
        font-size: 14px;
    }

    .toast-message {
        color: #666;
        font-size: 13px;
        margin-bottom: 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .toast-time {
        color: #999;
        font-size: 11px;
    }

    .toast-close {
        flex-shrink: 0;
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 18px;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .toast-close:hover {
        background: #f0f0f0;
        color: #666;
    }
</style>

<!-- Toast Notification Container -->
<div id="toast-container" class="toast-container"></div>

<script>
    (function() {
        let shownMessageIds = new Set();
        let lastCheckTime = Date.now();
        
        function fetchLatestUnread() {
            fetch('/api/messages/latest-unread')
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            if (!shownMessageIds.has(msg.id)) {
                                showToast(msg.sender_name, msg.message, msg.created_at, msg.conversation_id);
                                shownMessageIds.add(msg.id);
                            }
                        });
                    }
                })
                .catch(err => console.error('Failed to fetch latest unread:', err));
        }

        function showToast(senderName, message, time, conversationId) {
            const container = document.getElementById('toast-container');
            if (!container) return;
            
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.onclick = () => window.location.href = '/marketplace/message/' + conversationId;
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">New message from ${escapeHtml(senderName)}</div>
                    <div class="toast-message">${escapeHtml(message)}</div>
                    <div class="toast-time">${escapeHtml(time)}</div>
                </div>
                <button class="toast-close" onclick="event.stopPropagation(); removeToast(this.parentElement)">
                    <i class="fa-solid fa-times"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Auto-remove after 5 seconds
            setTimeout(() => removeToast(toast), 5000);
        }

        function removeToast(toast) {
            if (!toast) return;
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Check for new messages every 3 seconds
        setInterval(fetchLatestUnread, 3000);
        
        // Initial check after 2 seconds
        setTimeout(fetchLatestUnread, 2000);
    })();
</script>
@endif
@endauth
