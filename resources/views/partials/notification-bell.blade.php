@auth
<style>
  .notif-bell { position:relative; display:inline-flex; align-items:center; justify-content:center; width:auto; height:auto; border-radius:0; background:transparent; color:rgba(255, 255, 255, 0.9); cursor:pointer; border:none; padding:10px 20px; transition: all 0.3s ease; }
  .notif-bell:hover { background:rgba(255, 255, 255, 0.1); border-radius:8px; }
  .notif-badge { position:absolute; top:4px; right:14px; background:#dc3545; color:#fff; border-radius:9999px; font-size:10px; font-weight:700; padding:2px 5px; line-height:1; display:none; }
  .notif-dropdown { position:absolute; top:46px; right:0; width:360px; max-height:420px; overflow:auto; background:#ffffff; color:#0f172a; border-radius:14px; box-shadow:0 10px 28px rgba(0,0,0,0.12); border:1px solid #e2e8f0; display:none; z-index:9999; }
  .notif-dropdown-header { padding:10px 12px; border-bottom:1px solid #e2e8f0; font-weight:700; color:#0f172a; display:flex; align-items:center; justify-content:space-between; }
  .notif-list { list-style:none; margin:0; padding:0; }
  .notif-item { padding:12px; border-bottom:1px solid #f1f5f9; display:flex; gap:10px; }
  .notif-item:last-child { border-bottom:none; }
  .notif-icon { width:34px; height:34px; border-radius:9999px; display:flex; align-items:center; justify-content:center; background:#E7FAFE; color:#0075B5; flex: 0 0 auto; }
  .notif-content { min-width:0; }
  .notif-title { font-weight:700; font-size:14px; color:#0f172a; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .notif-message { font-size:13px; color:#475569; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .notif-meta { margin-top:6px; display:flex; align-items:center; justify-content:space-between; font-size:12px; color:#64748b; }
  .notif-actions { display:flex; gap:8px; }
  .notif-link { background:#0075B5; color:#fff; border:none; border-radius:6px; padding:6px 10px; font-size:12px; cursor:pointer; text-decoration:none; }
  .notif-mark { background:#e2e8f0; color:#0f172a; border:none; border-radius:6px; padding:6px 10px; font-size:12px; cursor:pointer; }
</style>

<div id="notifBellWrapper" data-user-id="{{ Auth::id() }}" style="position:relative;">
  <button id="notifBellBtn" class="notif-bell nav-link" title="Notifications" aria-label="Notifications">
    <i class="fa-solid fa-bell"></i>
    <span id="notifBellBadge" class="notif-badge">0</span>
  </button>
  <div id="notifDropdown" class="notif-dropdown" aria-label="Notifications dropdown">
    <div class="notif-dropdown-header">
      <span>Notifications</span>
      <div style="display:flex; gap:6px; align-items:center;">
        <button id="notifMarkAllBtn" class="notif-mark" type="button">Mark all read</button>
      </div>
    </div>
    <ul id="notifList" class="notif-list"></ul>
    <div id="notifEmpty" style="padding:14px; text-align:center; color:#64748b; display:none;">No new notifications</div>
  </div>
</div>

<script>
  (function(){
    const bellBtn = document.getElementById('notifBellBtn');
    const badge = document.getElementById('notifBellBadge');
    const dropdown = document.getElementById('notifDropdown');
    const list = document.getElementById('notifList');
    const empty = document.getElementById('notifEmpty');
    const markAllBtn = document.getElementById('notifMarkAllBtn');
    const wrapper = document.getElementById('notifBellWrapper');
    const csrf = '{{ csrf_token() }}';

    let polling = null;
    let isOpen = false;
    let latestCache = [];

    const routes = {
      count: '{{ route('api.notifications.unread-count') }}',
      latest: '{{ route('api.notifications.latest') }}',
      markRead: id => `/notifications/${id}/read`,
      markAll: '{{ route('notifications.read.all') }}',
      fallbackLink: '{{ route('notifications.index') }}'
    };

    async function fetchCount() {
      try {
        const res = await fetch(routes.count, {
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) return;
        const data = await res.json();
        const count = Number(data.count || 0);
        if (count > 0) {
          badge.style.display = 'inline-block';
          badge.textContent = count > 99 ? '99+' : String(count);
        } else {
          badge.style.display = 'none';
        }
      } catch (e) { /* silent */ }
    }

    function itemIconFor(type) {
      switch(String(type || '').toLowerCase()){
        case 'new_vendor_offer': return 'fa-hand-holding-dollar';
        case 'counter_vendor_offer': return 'fa-arrows-rotate';
        case 'vendor_offer_accepted': return 'fa-circle-check';
        case 'vendor_accepted_counter': return 'fa-circle-check';
        default: return 'fa-bell';
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
        const items = Array.isArray(data.items ?? data.offers) ? (data.items ?? data.offers) : [];
        latestCache = items;
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
              <div class="notif-title">${escapeHtml(o.title || 'Offer Update')}</div>
              <div class="notif-message">${escapeHtml(o.message || '')}</div>
              <div class="notif-meta">
                <span>${escapeHtml(o.created_at || '')}</span>
                <div class="notif-actions">
                  <a href="${escapeAttribute(o.link || routes.fallbackLink)}" class="notif-link">Open</a>
                  <button class="notif-mark" data-id="${o.id}">Mark read</button>
                </div>
              </div>
            </div>`;
          list.appendChild(li);
        });
        if (!silent) fetchCount();
      } catch (e) { /* silent */ }
    }

    function escapeHtml(str){
      if (!str) return '';
      return String(str)
        .replaceAll('&','&amp;')
        .replaceAll('<','&lt;')
        .replaceAll('>','&gt;')
        .replaceAll('"','&quot;')
        .replaceAll("'",'&#039;');
    }

    function escapeAttribute(str){
      return escapeHtml(str).replaceAll('"','&quot;');
    }

    async function markRead(id, btn){
      try{
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
          // remove item from list
          const item = btn.closest('.notif-item');
          if (item) item.remove();
          // if empty, show empty state
          if (!list.children.length) empty.style.display = 'block';
          // refresh count
          fetchCount();
        } else {
          console.warn('Failed to mark notification as read', await res.text());
        }
      } catch(e) { /* silent */ }
    }

    async function markAll(){
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
      } catch (e) { /* silent */ }
    }

    function toggleDropdown(){
      isOpen = !isOpen;
      dropdown.style.display = isOpen ? 'block' : 'none';
      if (isOpen) fetchLatest();
    }

    // Events
    bellBtn && bellBtn.addEventListener('click', function(e){
      e.stopPropagation();
      toggleDropdown();
    });
    markAllBtn && markAllBtn.addEventListener('click', function(e){
      e.stopPropagation();
      markAll();
    });
    document.addEventListener('click', function(){
      if (isOpen) { isOpen = false; dropdown.style.display = 'none'; }
    });
    dropdown.addEventListener('click', function(e){ e.stopPropagation(); });
    list.addEventListener('click', function(e){
      const t = e.target;
      if (t && t.matches('.notif-mark')) {
        const id = t.getAttribute('data-id');
        if (id) markRead(id, t);
      }
    });

    function subscribeRealtime(){
      try {
        const userId = Number(wrapper?.dataset?.userId || 0);
        if (window.Echo && userId) {
          window.Echo.private(`App.Models.User.${userId}`).notification(() => {
            fetchCount();
            fetchLatest({ silent: true });
          });
        }
      } catch (e) { /* ignore */ }
    }

    // Initial fetch + polling (5s)
    fetchCount();
    fetchLatest();
    if (!polling) polling = setInterval(() => {
      fetchCount();
      if (isOpen) fetchLatest({ silent: true });
    }, 5000);

    subscribeRealtime();
  })();
</script>
@endauth
