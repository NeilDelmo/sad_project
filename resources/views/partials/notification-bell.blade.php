@auth
<style>
  .notif-wrapper { position:relative; display:inline-flex; }
  .notif-bell-btn { position:relative; display:inline-flex; align-items:center; justify-content:center; gap:8px; border:none; background:transparent; color:rgba(255,255,255,0.92); padding:10px 18px; border-radius:8px; cursor:pointer; transition:background 0.2s ease; font-weight:600; }
  .notif-bell-btn:hover,
  .notif-bell-btn.is-open { background:rgba(255,255,255,0.18); }
  .notif-bell-btn:focus-visible { outline:2px solid rgba(255,255,255,0.65); outline-offset:2px; }
  .notif-badge { position:absolute; top:6px; right:12px; background:#dc3545; color:#fff; border-radius:9999px; font-size:10px; font-weight:700; padding:2px 6px; line-height:1; display:none; }
  .notif-dropdown { position:absolute; top:46px; right:0; width:360px; max-height:420px; overflow:auto; background:#fff; color:#0f172a; border-radius:14px; box-shadow:0 10px 28px rgba(0,0,0,0.12); border:1px solid #e2e8f0; display:none; z-index:9999; }
  .notif-dropdown-header { padding:10px 12px; border-bottom:1px solid #e2e8f0; font-weight:700; color:#0f172a; display:flex; align-items:center; justify-content:space-between; }
  .notif-list { list-style:none; margin:0; padding:0; }
  .notif-item { padding:12px; border-bottom:1px solid #f1f5f9; display:flex; gap:10px; align-items:flex-start; }
  .notif-item:last-child { border-bottom:none; }
  .notif-icon { width:34px; height:34px; border-radius:9999px; display:flex; align-items:center; justify-content:center; background:#E7FAFE; color:#0075B5; flex:0 0 auto; }
  .notif-content { min-width:0; flex:1; }
  .notif-title { font-weight:700; font-size:14px; color:#0f172a; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .notif-message { font-size:13px; color:#475569; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .notif-meta { margin-top:6px; display:flex; align-items:center; justify-content:space-between; font-size:12px; color:#64748b; }
  .notif-actions { display:flex; gap:8px; }
  .notif-link { background:#0075B5; color:#fff; border:none; border-radius:6px; padding:6px 10px; font-size:12px; cursor:pointer; text-decoration:none; font-weight:600; }
  .notif-link:hover { background:#006096; }
  .notif-mark { background:#e2e8f0; color:#475569; border:none; border-radius:6px; padding:6px 10px; font-size:12px; cursor:pointer; font-weight:600; transition:all 0.2s; }
  .notif-mark:hover { background:#cbd5e1; color:#1e293b; }
  .notif-mark-all { background:transparent; color:#0075B5; border:none; font-size:12px; font-weight:600; cursor:pointer; padding:4px 8px; border-radius:4px; }
  .notif-mark-all:hover { background:#E7FAFE; }
  .notif-footer { padding:10px; border-top:1px solid #e2e8f0; text-align:center; background:#f8fafc; border-bottom-left-radius:14px; border-bottom-right-radius:14px; }
  .notif-view-all { color:#0075B5; font-size:13px; font-weight:600; text-decoration:none; }
  .notif-view-all:hover { text-decoration:underline; }
  @media (max-width:480px) { .notif-dropdown { width:calc(100vw - 32px); right:-8px; } }
</style>

<div class="notif-wrapper" data-notif-wrapper data-user-id="{{ Auth::id() }}">
  <button type="button" class="notif-bell-btn" data-notif-toggle title="Notifications" aria-label="Notifications">
    <i class="fa-solid fa-bell"></i>
    <span class="notif-badge" data-notif-badge>0</span>
  </button>
  <div class="notif-dropdown" data-notif-dropdown aria-label="Notifications dropdown">
    <div class="notif-dropdown-header">
      <span>Notifications</span>
      <button class="notif-mark-all" type="button" data-notif-mark-all>Mark all read</button>
    </div>
    <ul class="notif-list" data-notif-list></ul>
    <div class="notif-empty" data-notif-empty style="padding:14px; text-align:center; color:#64748b; display:none;">No new notifications</div>
    <div class="notif-footer">
      <a href="{{ route('notifications.index') }}" class="notif-view-all">View all notifications</a>
    </div>
  </div>
</div>

<script>
  (function(){
    const wrappers = document.querySelectorAll('[data-notif-wrapper]');
    if (!wrappers.length) return;

    const routes = {
      count: '{{ route('api.notifications.unread-count') }}',
      latest: '{{ route('api.notifications.latest') }}',
      markRead: id => `/notifications/${id}/read`,
      markAll: '{{ route('notifications.read.all') }}',
      fallbackLink: '{{ route('notifications.index') }}'
    };
    const csrf = '{{ csrf_token() }}';

    wrappers.forEach(wrapper => initBell(wrapper));

    function initBell(wrapper) {
      const bellBtn = wrapper.querySelector('[data-notif-toggle]');
      const badge = wrapper.querySelector('[data-notif-badge]');
      const dropdown = wrapper.querySelector('[data-notif-dropdown]');
      const list = wrapper.querySelector('[data-notif-list]');
      const empty = wrapper.querySelector('[data-notif-empty]');
      const markAllBtn = wrapper.querySelector('[data-notif-mark-all]');
      if (!bellBtn || !dropdown || !list || !empty) return;

      let isOpen = false;
      let pollingId = null;

      const handleOutsideClick = (evt) => {
        if (!wrapper.contains(evt.target)) closeDropdown();
      };

      bellBtn.addEventListener('click', (evt) => {
        evt.preventDefault();
        evt.stopPropagation();
        isOpen ? closeDropdown() : openDropdown();
      });

      if (markAllBtn) {
        markAllBtn.addEventListener('click', (evt) => {
          evt.preventDefault();
          evt.stopPropagation();
          markAll();
        });
      }

      list.addEventListener('click', (evt) => {
        const markBtn = evt.target.closest('[data-notif-mark-one]');
        if (markBtn) {
          evt.preventDefault();
          evt.stopPropagation();
          const id = markBtn.getAttribute('data-notif-mark-one');
          if (id) markRead(id, markBtn);
        }
      });

      function openDropdown() {
        isOpen = true;
        dropdown.style.display = 'block';
        bellBtn.classList.add('is-open');
        document.addEventListener('click', handleOutsideClick);
        fetchLatest();
      }

      function closeDropdown() {
        isOpen = false;
        dropdown.style.display = 'none';
        bellBtn.classList.remove('is-open');
        document.removeEventListener('click', handleOutsideClick);
      }

      async function fetchCount() {
        try {
          const res = await fetch(routes.count, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
          });
          if (!res.ok) return;
          const data = await res.json();
          var countSource = data.count;
          if (countSource == null && data.unread_count != null) {
            countSource = data.unread_count;
          }
          const count = Number(countSource != null ? countSource : 0);
          if (!badge) return;
          if (count > 0) {
            badge.style.display = 'inline-block';
            badge.textContent = count > 99 ? '99+' : String(count);
          } else {
            badge.style.display = 'none';
          }
        } catch (_) {}
      }

      function itemIconFor(type) {
        switch(String(type || '').toLowerCase()) {
          case 'new_vendor_offer':
            return 'fa-hand-holding-dollar';
          case 'counter_vendor_offer':
            return 'fa-arrows-rotate';
          case 'vendor_offer_accepted':
          case 'vendor_accepted_counter':
            return 'fa-circle-check';
          case 'new_customer_order':
          case 'customer_order_status':
            return 'fa-shopping-cart';
          case 'order_status_updated':
            return 'fa-box';
          default:
            return 'fa-bell';
        }
      }

      async function fetchLatest({ silent = false } = {}) {
        try {
          const res = await fetch(routes.latest, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
          });
          if (!res.ok) return;
          const data = await res.json();
          const items = Array.isArray(data.items) ? data.items : (Array.isArray(data.offers) ? data.offers : []);
          list.innerHTML = '';
          if (items.length === 0) {
            empty.style.display = 'block';
            return;
          }
          empty.style.display = 'none';
          items.forEach(o => {
            const li = document.createElement('li');
            li.className = 'notif-item';
            li.innerHTML = `
              <div class="notif-icon"><i class="fa-solid ${itemIconFor(o.type)}"></i></div>
              <div class="notif-content">
                <div class="notif-title">${escapeHtml(o.title || 'Notification')}</div>
                <div class="notif-message">${escapeHtml(o.message || '')}</div>
                <div class="notif-meta">
                  <span>${escapeHtml(o.created_at || '')}</span>
                  <div class="notif-actions">
                    <a href="${escapeAttr(o.link || routes.fallbackLink)}" class="notif-link">Open</a>
                    <button class="notif-mark" data-notif-mark-one="${o.id || ''}">Mark read</button>
                  </div>
                </div>
              </div>`;
            list.appendChild(li);
          });
          if (!silent) fetchCount();
        } catch (_) {}
      }

      function escapeHtml(str) {
        if (!str) return '';
        return String(str)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function escapeAttr(str) {
        return escapeHtml(str || '');
      }

      async function markRead(id, btn) {
        try {
          const res = await fetch(routes.markRead(id), {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrf,
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
          });
          if (res.ok) {
            const item = btn.closest('.notif-item');
            if (item) item.remove();
            if (!list.children.length) empty.style.display = 'block';
            fetchCount();
          }
        } catch (_) {}
      }

      async function markAll() {
        try {
          const res = await fetch(routes.markAll, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrf,
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
          });
          if (res.ok) {
            list.innerHTML = '';
            empty.style.display = 'block';
            fetchCount();
          }
        } catch (_) {}
      }

      function startPolling() {
        if (pollingId) return;
        pollingId = setInterval(() => {
          fetchCount();
          if (isOpen) fetchLatest({ silent: true });
        }, 5000);
        window.addEventListener('beforeunload', () => clearInterval(pollingId), { once: true });
      }

      function subscribeRealtime() {
        try {
          const dataset = wrapper ? wrapper.dataset : null;
          const userId = Number((dataset && dataset.userId) ? dataset.userId : 0);
          if (window.Echo && userId) {
            window.Echo.private(`App.Models.User.${userId}`).notification(() => {
              fetchCount();
              if (isOpen) {
                fetchLatest({ silent: true });
              }
            });
          }
        } catch (_) {}
      }

      fetchCount();
      fetchLatest({ silent: true });
      startPolling();
      subscribeRealtime();
    }
  })();
</script>
@endauth
