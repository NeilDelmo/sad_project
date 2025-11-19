@auth
<style>
  .notif-bell { position: relative; display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:9999px; background: rgba(255,255,255,0.15); color:#fff; cursor:pointer; border:none; }
  .notif-bell:hover { background: rgba(255,255,255,0.25); }
  .notif-badge { position:absolute; top:-4px; right:-4px; background:#dc3545; color:#fff; border-radius:9999px; font-size:11px; font-weight:700; padding: 2px 6px; line-height:1; display:none; }
  .notif-dropdown { position:absolute; top:46px; right:0; width:360px; max-height:420px; overflow:auto; background:#fff; color:#333; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.15); border:1px solid #e5e7eb; display:none; z-index:9999; }
  .notif-dropdown-header { padding:10px 12px; border-bottom:1px solid #eee; font-weight:700; color:#1B5E88; display:flex; align-items:center; justify-content:space-between; }
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

<div id="notifBellWrapper" style="position:relative; margin-left:8px;">
  <button id="notifBellBtn" class="notif-bell" title="Notifications" aria-label="Notifications">
    <i class="fa-solid fa-bell"></i>
    <span id="notifBellBadge" class="notif-badge">0</span>
  </button>
  <div id="notifDropdown" class="notif-dropdown" aria-label="Notifications dropdown">
    <div class="notif-dropdown-header">
      <span>Notifications</span>
      <button id="notifRefreshBtn" class="notif-mark" type="button">Refresh</button>
    </div>
    <ul id="notifList" class="notif-list"></ul>
    <div id="notifEmpty" style="padding:14px; text-align:center; color:#64748b; display:none;">No new offer notifications</div>
  </div>
</div>

<script>
  (function(){
    const bellBtn = document.getElementById('notifBellBtn');
    const badge = document.getElementById('notifBellBadge');
    const dropdown = document.getElementById('notifDropdown');
    const list = document.getElementById('notifList');
    const empty = document.getElementById('notifEmpty');
    const refreshBtn = document.getElementById('notifRefreshBtn');
    const csrf = '{{ csrf_token() }}';

    let polling = null;
    let isOpen = false;

    async function fetchCount() {
      try {
        const res = await fetch('/api/offers/pending-count', { credentials: 'same-origin' });
        if (!res.ok) return;
        const data = await res.json();
        const count = Number(data.pending_count || 0);
        if (count > 0) {
          badge.style.display = 'inline-block';
          badge.textContent = count > 99 ? '99+' : String(count);
        } else {
          badge.style.display = 'none';
        }
      } catch (e) { /* silent */ }
    }

    function itemIconFor(type) {
      switch(type){
        case 'new_vendor_offer': return 'fa-hand-holding-dollar';
        case 'counter_vendor_offer': return 'fa-arrows-rotate';
        case 'vendor_offer_accepted': return 'fa-circle-check';
        case 'vendor_accepted_counter': return 'fa-circle-check';
        default: return 'fa-bell';
      }
    }

    async function fetchLatest() {
      try {
        const res = await fetch('/api/offers/latest', { credentials: 'same-origin' });
        if (!res.ok) return;
        const data = await res.json();
        const offers = Array.isArray(data.offers) ? data.offers : [];
        list.innerHTML = '';
        if (offers.length === 0) {
          empty.style.display = 'block';
          return;
        }
        empty.style.display = 'none';
        offers.forEach(o => {
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
                  <a href="${o.link || '#'}" class="notif-link">Open</a>
                  <button class="notif-mark" data-id="${o.id}">Mark read</button>
                </div>
              </div>
            </div>`;
          list.appendChild(li);
        });
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

    async function markRead(id, btn){
      try{
        const res = await fetch(`/api/offers/notifications/${id}/read`, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrf },
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
        }
      } catch(e) { /* silent */ }
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
    refreshBtn && refreshBtn.addEventListener('click', function(e){
      e.stopPropagation();
      fetchLatest();
      fetchCount();
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

    // Initial fetch + polling (30s)
    fetchCount();
    if (!polling) polling = setInterval(fetchCount, 30000);
  })();
</script>
@endauth
