@auth
@php
    // Show toasts on all pages for offer notifications
    $showToasts = true;
@endphp

@if($showToasts)
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
<div id="toast-container" class="toast-container" data-user-id="{{ Auth::id() }}" data-audio-src="/audio/notify.mp3"></div>

<script>
    (function() {
        if (window.__toastNotifierInitialized) return; // prevent double init
        window.__toastNotifierInitialized = true;
        
        let shownOfferIds = new Set();
        const containerEl = document.getElementById('toast-container');
        const audioSrc = (containerEl?.dataset?.audioSrc) || '/audio/notify.mp3';
        const notifAudio = new Audio(audioSrc);
        notifAudio.preload = 'auto';

        function unlockAudioOnce() {
            const tryUnlock = () => {
                notifAudio.muted = true;
                notifAudio.play().then(() => {
                    notifAudio.pause();
                    notifAudio.currentTime = 0;
                    notifAudio.muted = false;
                    window.removeEventListener('pointerdown', tryUnlock);
                    window.removeEventListener('keydown', tryUnlock);
                }).catch(() => { /* ignore */ });
            };
            window.addEventListener('pointerdown', tryUnlock, { once: false });
            window.addEventListener('keydown', tryUnlock, { once: false });
        }
        unlockAudioOnce();
        
        // Load shown IDs from localStorage to persist across page loads
        const storedIds = localStorage.getItem('shownOfferIds');
        if (storedIds) {
            try {
                shownOfferIds = new Set(JSON.parse(storedIds));
            } catch (e) {
                shownOfferIds = new Set();
            }
        }

        function saveShownIds() {
            localStorage.setItem('shownOfferIds', JSON.stringify([...shownOfferIds]));
        }
        
        function fetchLatestOffers() {
            fetch('/api/offers/latest')
                .then(response => response.json())
                .then(data => {
                    if (data.offers && data.offers.length > 0) {
                        let hasNewOffers = false;
                        data.offers.forEach(offer => {
                            if (!shownOfferIds.has(offer.id)) {
                                hasNewOffers = true;
                                // Delay toast slightly so sound plays first
                                setTimeout(() => {
                                    showToast(offer.title, offer.message, offer.created_at, offer.link, offer.id);
                                }, 150);
                                shownOfferIds.add(offer.id);
                                saveShownIds();
                            }
                        });
                        
                        // Play notification sound immediately for new offers
                        if (hasNewOffers) {
                            notifAudio.currentTime = 0;
                            notifAudio.play().catch(() => {/* blocked until user interacts */});
                        }
                    }
                })
                .catch(err => console.error('Failed to fetch latest offers:', err));
        }

        function showToast(title, message, time, link, notificationId) {
            const container = document.getElementById('toast-container');
            if (!container) return;
            
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.onclick = () => {
                // Mark as read when clicked
                if (notificationId) {
                    fetch(`/api/offers/notifications/${notificationId}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } })
                        .then(() => {
                            // Refresh notification badge
                            if (window.__refreshNotifications) window.__refreshNotifications();
                        })
                        .catch(() => {});
                }
                if (link) {
                    window.location.href = link;
                }
            };
            
            const isOrderNotif = (title || '').toLowerCase().includes('order');
            const isSuccess = (title || '').toLowerCase().includes('success');
            const isError = (title || '').toLowerCase().includes('error');
            
            let icon = 'fa-handshake';
            if (isOrderNotif) icon = 'fa-shopping-cart';
            if (isSuccess) icon = 'fa-check-circle';
            if (isError) icon = 'fa-exclamation-circle';
            
            toast.innerHTML = `
                <div class="toast-icon" style="${isSuccess ? 'background: #28a745;' : (isError ? 'background: #dc3545;' : '')}">
                    <i class="fa-solid ${icon}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${escapeHtml(title)}</div>
                    <div class="toast-message">${escapeHtml(message)}</div>
                    <div class="toast-time">${escapeHtml(time)}</div>
                </div>
                <button class="toast-close" onclick="event.stopPropagation(); removeToast(this.parentElement, '${notificationId || ''}')">
                    <i class="fa-solid fa-times"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Auto-remove after 8 seconds
            setTimeout(() => removeToast(toast, notificationId), 8000);
        }

        function removeToast(toast, notificationId) {
            if (!toast) return;
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 300);
            
            // Mark notification as read
            if (notificationId) {
                fetch(`/api/offers/notifications/${notificationId}/read`, { 
                    method: 'POST', 
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } 
                })
                .then(() => {
                    // Refresh notification badge
                    if (window.__refreshNotifications) window.__refreshNotifications();
                })
                .catch(() => {});
            }
        }
        window.removeToast = removeToast; // Make it global for onclick
        window.showToast = showToast; // Make it global for session flashes

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Check for new offers once on page load after 1 second
        setTimeout(() => { fetchLatestOffers(); }, 1000);
        
        // Check again every 5 seconds for better responsiveness
        setInterval(() => { fetchLatestOffers(); }, 5000);

        // Realtime: subscribe to private user channel if Echo is available
        try {
            const userId = Number(containerEl?.dataset?.userId || 0);
            if (window.Echo && userId) {
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        const payload = notification?.data ?? notification ?? {};
                        const rootType = String(notification?.type || '').toLowerCase();
                        const embeddedType = String(payload?.type || '').toLowerCase();
                        const isOffer = embeddedType.includes('offer') || rootType.includes('offer');
                        const isOrder = embeddedType.includes('customer_order') || rootType.includes('customer_order');
                        if (!isOffer && !isOrder) return;

                        const mapByType = (t) => {
                            t = t || '';
                            if (t.includes('new_vendor_offer')) return 'New Offer Received';
                            if (t.includes('counter_vendor_offer')) return 'Counter Offer Received';
                            if (t.includes('vendor_offer_accepted')) return 'Your Offer Was Accepted';
                            if (t.includes('vendor_accepted_counter')) return 'Counter Offer Accepted';
                            if (t.includes('customer_order')) return 'Order Update';
                            return 'New Notification';
                        };
                        const title = payload.title || mapByType(embeddedType || rootType);
                        const message = payload.message
                            || payload.fisherman_message
                            || `Update on ${payload.product_name || 'your offer'}`;
                        const time = (new Date()).toLocaleString();
                        const link = payload.link || '/notifications';
                        const notificationId = notification?.id || payload.id || '';

                        if (!shownOfferIds.has(notificationId) && notificationId) {
                            // Play sound immediately
                            notifAudio.currentTime = 0;
                            notifAudio.play().catch(() => {});
                            
                            // Show toast after slight delay so sound and toast are more synchronized
                            setTimeout(() => {
                                showToast(title, message, time, link, notificationId);
                            }, 150);
                            
                            shownOfferIds.add(notificationId);
                            saveShownIds();
                        }
                    });
            }
        } catch (e) { /* ignore */ }
    })();
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            if (window.showToast) {
                window.showToast('Success', @json(session('success')), 'Just now', null, null);
            }
        }, 500);
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            if (window.showToast) {
                window.showToast('Error', @json(session('error')), 'Just now', null, null);
            }
        }, 500);
    });
</script>
@endif
@endif
@endauth
