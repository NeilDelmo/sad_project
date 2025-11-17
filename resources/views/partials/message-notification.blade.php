@auth
<script>
    // Background notification system for new messages (site-wide)
    (function() {
        if (window.__messageNotifierInitialized) return; // prevent double init
        window.__messageNotifierInitialized = true;

        let lastUnreadCount = 0;
        let hasNotified = false;
        let isFirstPoll = true;

        const notifAudio = new Audio('/audio/notify.mp3');

        function checkNewMessages() {
            fetch('/api/messages/unread-count', { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(data => {
                    const count = Number(data.unread_count || 0);

                    // First poll: play if there are any unread. Later: play only if count increased
                    const shouldPlaySound = (isFirstPoll && count > 0 && !hasNotified) ||
                                            (!isFirstPoll && count > lastUnreadCount && !hasNotified);

                    if (shouldPlaySound) {
                        notifAudio.currentTime = 0;
                        notifAudio.play().catch(() => {/* ignore autoplay restrictions */});
                        hasNotified = true;
                    }

                    if (count === 0) {
                        hasNotified = false; // reset when user has read everything
                    }

                    lastUnreadCount = count;
                    if (isFirstPoll) isFirstPoll = false;
                })
                .catch(() => { /* ignore errors */ });
        }

        // Poll periodically and on focus
        setInterval(checkNewMessages, 3000);
        window.addEventListener('focus', checkNewMessages);
    })();
</script>
@endauth
