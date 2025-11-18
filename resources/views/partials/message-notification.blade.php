@auth
<script>
    // Background notification system for pending offers (site-wide)
    (function() {
        if (window.__offerNotifierInitialized) return; // prevent double init
        window.__offerNotifierInitialized = true;

        let lastPendingCount = 0;
        let hasNotified = false;
        let isFirstPoll = true;

        const notifAudio = new Audio('/audio/notify.mp3');

        function checkPendingOffers() {
            fetch('/api/offers/pending-count', { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(data => {
                    const count = Number(data.pending_count || 0);

                    // Only play sound if count increased (not on first poll to avoid spam)
                    const shouldPlaySound = !isFirstPoll && count > lastPendingCount && !hasNotified;

                    if (shouldPlaySound) {
                        notifAudio.currentTime = 0;
                        notifAudio.play().catch(() => {/* ignore autoplay restrictions */});
                        hasNotified = true;
                        
                        // Reset notification flag after 5 seconds
                        setTimeout(() => { hasNotified = false; }, 5000);
                    }

                    if (count === 0) {
                        hasNotified = false; // reset when user has no pending offers
                    }

                    lastPendingCount = count;
                    if (isFirstPoll) isFirstPoll = false;
                })
                .catch(() => { /* ignore errors */ });
        }

        // Poll every 30 seconds (less frequent to avoid annoyance)
        setInterval(checkPendingOffers, 30000);
        
        // Check on window focus
        window.addEventListener('focus', checkPendingOffers);
        
        // Initial check after 3 seconds
        setTimeout(checkPendingOffers, 3000);
    })();
</script>
@endauth
