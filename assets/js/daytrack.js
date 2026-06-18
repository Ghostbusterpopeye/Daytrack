/**
 * DayTrack – Shared Utilities v3 (PHP Edition)
 * assets/js/daytrack.js
 *
 * Replaces localStorage TaskStore/ProjectStore/MeetStore
 * with a fetch-based ApiClient that calls the PHP backend.
 */

/* ─── Live Clock ─────────────────────────────────────── */
function startClock(elId = 'live-clock') {
  function tick() {
    const el = document.getElementById(elId);
    if (!el) return;
    const now = new Date();
    let h = now.getHours();
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    el.textContent = `${String(h).padStart(2, '0')}:${m}:${s} ${ampm}`;
  }
  tick();
  setInterval(tick, 1000);
}

/* ─── Dynamic greeting ───────────────────────────────── */
function setGreeting(elId = 'greeting-title', name = 'there') {
  const h = new Date().getHours();
  let greet = 'Good Morning';
  if (h >= 12 && h < 17) greet = 'Good Afternoon';
  else if (h >= 17) greet = 'Good Evening';
  const el = document.getElementById(elId);
  if (el) el.textContent = `${greet}, ${name}!`;
}

/* ─── HTML escape helper ─────────────────────────────── */
function escHtml(str) {
  return String(str || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

/* ─── Show toast notification ────────────────────────── */
function showToast(message, type = 'success') {
  const existing = document.getElementById('dt-toast');
  if (existing) existing.remove();
  const bgColor = type === 'danger' ? '#ef4444' : type === 'info' ? '#6366f1' : '#10b981';
  const toast = document.createElement('div');
  toast.id = 'dt-toast';
  toast.style.cssText = [
    'position:fixed', 'top:20px', 'left:50%', 'transform:translateX(-50%)',
    `background:${bgColor}`, 'color:#fff', 'padding:10px 20px',
    'border-radius:30px', 'font-size:.83rem', 'font-weight:600',
    'z-index:99999', 'white-space:nowrap', 'box-shadow:0 4px 20px rgba(0,0,0,.15)',
    'display:flex', 'align-items:center', 'gap:8px',
    'animation:dtToastIn .3s ease', 'font-family:Inter,system-ui,sans-serif'
  ].join(';');
  toast.innerHTML = `<span>${message}</span>`;
  if (!document.getElementById('dt-toast-style')) {
    const s = document.createElement('style');
    s.id = 'dt-toast-style';
    s.textContent = '@keyframes dtToastIn{from{opacity:0;transform:translateX(-50%) translateY(-10px) scale(.9)}to{opacity:1;transform:translateX(-50%) translateY(0) scale(1)}}';
    document.head.appendChild(s);
  }
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 2800);
}

/* ─── Format time 24h → 12h ─────────────────────────── */
function fmt12(time24) {
  if (!time24) return '';
  const [h, m] = time24.split(':').map(Number);
  const ampm = h >= 12 ? 'PM' : 'AM';
  const h12  = h % 12 || 12;
  return `${String(h12).padStart(2,'0')}:${String(m).padStart(2,'0')} ${ampm}`;
}

/* ─────────────────────────────────────────────────────────
   ApiClient – Replaces localStorage stores
   Expects `API` (base URL string) to be defined on the page
   before this script is loaded, e.g.:
     const API = '../../api';
──────────────────────────────────────────────────────── */
const ApiClient = {

  /**
   * Internal fetch wrapper.
   * Returns parsed JSON data on success, or null on error.
   */
  async _fetch(endpoint, method = 'GET', body = null) {
    const base = (typeof API !== 'undefined') ? API : '../../api';
    const url  = `${base}/${endpoint}`;
    const opts = {
      method,
      credentials: 'same-origin',
      headers: body ? { 'Content-Type': 'application/json' } : {},
    };
    if (body) opts.body = JSON.stringify(body);
    try {
      const res  = await fetch(url, opts);
      const json = await res.json();
      if (!json.success) {
        console.warn(`[ApiClient] ${method} ${url} →`, json.error || 'unknown error');
        if (json.error && json.error.includes('Unauthorized')) {
          window.location.href = 'login.php';
        }
        return null;
      }
      return json.data ?? json;
    } catch (err) {
      console.error('[ApiClient] Network error:', err);
      showToast('Network error. Please check your connection.', 'danger');
      return null;
    }
  },

  /* ── Tasks ── */
  tasks: {
    getAll()           { return ApiClient._fetch('tasks.php'); },
    create(data)       { return ApiClient._fetch('tasks.php', 'POST', data); },
    update(id, data)   { return ApiClient._fetch(`tasks.php?id=${id}`, 'PUT', data); },
    remove(id)         { return ApiClient._fetch(`tasks.php?id=${id}`, 'DELETE'); },
  },

  /* ── Projects ── */
  projects: {
    getAll()           { return ApiClient._fetch('projects.php'); },
    create(data)       { return ApiClient._fetch('projects.php', 'POST', data); },
    update(id, data)   { return ApiClient._fetch(`projects.php?id=${id}`, 'PUT', data); },
    remove(id)         { return ApiClient._fetch(`projects.php?id=${id}`, 'DELETE'); },
  },

  /* ── Meetings ── */
  meetings: {
    getAll()           { return ApiClient._fetch('meetings.php'); },
    create(data)       { return ApiClient._fetch('meetings.php', 'POST', data); },
    update(id, data)   { return ApiClient._fetch(`meetings.php?id=${id}`, 'PUT', data); },
    remove(id)         { return ApiClient._fetch(`meetings.php?id=${id}`, 'DELETE'); },
  },

  /* ── Auth ── */
  auth: {
    me()               { return ApiClient._fetch('auth.php?action=me'); },
    logout()           { return ApiClient._fetch('auth.php?action=logout', 'POST'); },
  },

  /* ── Messages ── */
  messages: {
    getAll()           { return ApiClient._fetch('messages.php'); },
    create(data)       { return ApiClient._fetch('messages.php', 'POST', data); },
  },
};
