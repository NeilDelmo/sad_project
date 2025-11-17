<script>
    // Background notification system for new messages
    (function() {
        let lastUnreadCount = {{ Auth::user() ? (Auth::user()->conversations()->whereHas('messages', function($q) { $q->where('is_read', false)->where('sender_id', '!=', Auth::id()); })->count()) : 0 }};
        let hasNotified = false;
        let isFirstPoll = true;
        
        const notifAudio = new Audio('/audio/notify.mp3');
        
        function checkNewMessages() {
            fetch('/api/messages/unread-count')
                .then(response => response.json())
                .then(data => {
                    // Play sound on first poll if unread messages exist, or on subsequent polls if count increases
                    const shouldPlaySound = (isFirstPoll && data.unread_count > 0 && !hasNotified) || 
                                            (!isFirstPoll && data.unread_count > lastUnreadCount && !hasNotified);
                    
                    if (shouldPlaySound) {
                        notifAudio.currentTime = 0;
                        notifAudio.play().catch(() => {/* ignore autoplay restrictions */});
                        hasNotified = true;
                    }
                    
                    // Reset notification flag when count goes to 0
                    if (data.unread_count === 0) {
                        hasNotified = false;
                    }
                    
                    // Update any unread count badges on the page
                    const badges = document.querySelectorAll('[data-unread-count]');
                    badges.forEach(badge => {
                        if (data.unread_count !== lastUnreadCount) {
                            badge.textContent = data.unread_count;
                            badge.setAttribute('data-unread-count', data.unread_count);
                        }
                    });
                    
                    lastUnreadCount = data.unread_count;
                    
                    if (isFirstPoll) {
                        isFirstPoll = false;
                    }
                })
                .catch(() => {/* ignore errors */});
        }
        
        // Poll every 5 seconds
        setInterval(checkNewMessages, 5000);
        
        // Also check when window gains focus
        window.addEventListener('focus', checkNewMessages);
    })();
</script>
