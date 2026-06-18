<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DayTrack – Meetings</title>
  <meta name="description" content="Schedule and manage team meetings with DayTrack."/>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { brand: '#6366f1', 'brand-dark': '#4f46e5' },
          fontFamily: { sans: ['Inter','system-ui','sans-serif'] }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    .skeleton{background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:800px 100%;animation:shimmer 1.4s infinite linear;border-radius:8px;}
    @keyframes shimmer{0%{background-position:-400px 0}100%{background-position:400px 0}}
    .fade-in{animation:fadeIn .4s ease both}
    @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
    .modal-wrap{display:none;position:fixed;inset:0;z-index:60;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:16px;}
    .modal-wrap.open{display:flex;}
    ::-webkit-scrollbar{width:4px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:#e0e0e0;border-radius:4px}
  </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased">

<div class="flex min-h-screen">

  <!-- SIDEBAR -->
  <?php include __DIR__ . '/../components/navbar.php'; ?>

  <!-- MAIN CONTENT -->
  <main class="flex-1 lg:ml-64 min-h-screen">

    <!-- TOP BAR -->
    <header class="sticky top-0 z-20 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
      <div class="lg:hidden w-10"></div>
      <div>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Schedule</p>
        <h1 class="text-xl font-bold text-gray-900">Meetings</h1>
        <p class="text-sm text-gray-400" id="meet-subtitle">Loading…</p>
      </div>
      <button id="btn-add-meet"
        class="flex items-center gap-2 bg-indigo-600 text-white font-semibold text-sm rounded-xl px-4 py-2 hover:bg-indigo-700 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        New Meeting
      </button>
    </header>

    <!-- PAGE BODY -->
    <div class="px-4 sm:px-6 py-6 max-w-7xl mx-auto">
      <!-- MEETING LIST -->
      <div id="meeting-list" class="space-y-3">
        <?php for($i=0;$i<4;$i++): ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
          <div class="flex items-start gap-4">
            <div class="skeleton w-12 h-12 rounded-xl flex-shrink-0"></div>
            <div class="flex-1">
              <div class="skeleton h-4 w-48 rounded mb-2"></div>
              <div class="flex gap-3 mb-3">
                <div class="skeleton h-3 w-20 rounded"></div>
                <div class="skeleton h-3 w-16 rounded"></div>
                <div class="skeleton h-3 w-20 rounded"></div>
              </div>
              <div class="flex gap-2">
                <div class="skeleton h-8 w-24 rounded-xl"></div>
                <div class="skeleton h-8 w-8 rounded-xl"></div>
                <div class="skeleton h-8 w-8 rounded-xl"></div>
              </div>
            </div>
          </div>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </main>
</div>

<!-- ══ ADD/EDIT MEETING MODAL ══ -->
<div class="modal-wrap" id="meet-modal-wrap" role="dialog" aria-modal="true" aria-labelledby="meet-modal-title">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md fade-in" onclick="event.stopPropagation()">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-gray-900" id="meet-modal-title">New Meeting</h2>
      <button onclick="closeModal('meet-modal-wrap')" class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="meet-form" novalidate>
      <input type="hidden" id="edit-meet-id"/>

      <!-- Title -->
      <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Meeting Title *</label>
        <input type="text" id="meet-title" placeholder="e.g. Sprint Review" maxlength="120" required
          class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all"/>
      </div>

      <!-- Time + Duration -->
      <div class="grid grid-cols-2 gap-3 mb-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Time</label>
          <input type="time" id="meet-time" value="09:00"
            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all"/>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Duration (min)</label>
          <input type="number" id="meet-duration" value="30" min="5" max="480"
            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all"/>
        </div>
      </div>

      <!-- Members + Type -->
      <div class="grid grid-cols-2 gap-3 mb-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Members</label>
          <input type="number" id="meet-members" value="2" min="1" max="200"
            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all"/>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Type</label>
          <select id="meet-type"
            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all">
            <option value="standup">Standup</option>
            <option value="review">Review</option>
            <option value="update">Update</option>
            <option value="one-on-one">1-on-1</option>
            <option value="planning">Planning</option>
            <option value="other">Other</option>
          </select>
        </div>
      </div>

      <!-- Link -->
      <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Meeting Link</label>
        <input type="url" id="meet-link" placeholder="https://meet.google.com/…"
          class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all"/>
      </div>

      <!-- Notes -->
      <div class="mb-5">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Notes</label>
        <textarea id="meet-notes" rows="2" placeholder="Agenda or notes…"
          class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all resize-none"></textarea>
      </div>

      <button type="submit" id="btn-submit-meet"
        class="w-full bg-indigo-600 text-white font-semibold text-sm rounded-xl px-4 py-2.5 hover:bg-indigo-700 transition-colors">
        Add Meeting
      </button>
    </form>
  </div>
</div>

<!-- ══ DELETE CONFIRM MODAL ══ -->
<div class="modal-wrap" id="delete-meet-wrap">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm text-center fade-in" onclick="event.stopPropagation()">
    <div class="w-14 h-14 rounded-full bg-red-50 border border-red-100 flex items-center justify-center mx-auto mb-4">
      <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    </div>
    <h3 class="text-base font-bold text-gray-900 mb-1">Delete Meeting?</h3>
    <p class="text-sm text-gray-500 mb-5">This action cannot be undone.</p>
    <div class="flex gap-3">
      <button id="btn-cancel-del-meet" class="flex-1 px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Cancel</button>
      <button id="btn-confirm-del-meet" class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-red-500 rounded-xl hover:bg-red-600 transition-colors">Delete</button>
    </div>
  </div>
</div>

<script src="../../assets/js/daytrack.js"></script>
<script>
const API = '../../api';
let allMeetings = [], pendingDelMeet = null;

const typeColors = {standup:'#6366f1',review:'#06b6d4',update:'#10b981','one-on-one':'#f59e0b',planning:'#ef4444',other:'#8b5cf6'};
const typeIcons  = {
  standup:  `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>`,
  review:   `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>`,
  update:   `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>`,
  'one-on-one':`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`,
  planning: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`,
  other:    `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.277A1 1 0 0121 8.68v6.64a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>`,
};

function openModal(id)  { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
document.querySelectorAll('.modal-wrap').forEach(el => el.addEventListener('click', e => { if(e.target===el) closeModal(el.id); }));

async function loadMeetings() {
  const data = await ApiClient.meetings.getAll();
  allMeetings = data || [];
  renderMeetings();
}

function renderMeetings() {
  const now     = new Date();
  const nowMins = now.getHours() * 60 + now.getMinutes();
  let upcoming = 0, totalMins = 0;

  allMeetings.forEach(m => {
    const [h, mi] = (m.time || '00:00').split(':').map(Number);
    const startMins = h * 60 + mi;
    const endMins   = startMins + (m.duration || 30);
    if (nowMins < startMins) upcoming++;
    totalMins += (m.duration || 30);
  });

  document.getElementById('meet-subtitle').textContent =
    allMeetings.length ? `${allMeetings.length} meeting${allMeetings.length!==1?'s':''} scheduled` : 'No meetings yet';

  const container = document.getElementById('meeting-list');
  if (!allMeetings.length) {
    container.innerHTML = `
      <div class="py-16 text-center text-gray-400 bg-white rounded-2xl border border-gray-100 shadow-sm">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.277A1 1 0 0121 8.68v6.64a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
        <p class="font-semibold text-sm">No meetings scheduled</p>
        <p class="text-xs mt-1">Tap New Meeting to add one.</p>
      </div>`;
    return;
  }

  container.innerHTML = allMeetings.map((m, i) => {
    const [h, mi] = (m.time || '00:00').split(':').map(Number);
    const startMins = h * 60 + mi;
    const endMins   = startMins + (m.duration || 30);
    const isLive    = nowMins >= startMins && nowMins < endMins;
    const isOver    = nowMins >= endMins;

    const col  = typeColors[m.type] || '#6366f1';
    const icon = typeIcons[m.type]  || typeIcons.other;

    const statusBadge = isLive
      ? `<span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-600"><span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>Live</span>`
      : isOver
      ? `<span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">Done</span>`
      : `<span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-600">Upcoming</span>`;

    return `
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 fade-in hover:shadow-md transition-shadow" style="animation-delay:${Math.min(i*.05,.3)}s" id="meet-card-${m.id}">
      <div class="flex items-start gap-4">
        <!-- Type icon -->
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:${col}18;color:${col};">${icon}</div>
        <!-- Content -->
        <div class="flex-1 min-w-0">
          <div class="flex items-start justify-between gap-2 mb-2">
            <p class="font-bold text-gray-900 text-sm leading-tight">${escHtml(m.title)}</p>
            ${statusBadge}
          </div>
          <div class="flex flex-wrap items-center gap-3 text-xs text-gray-400 mb-3">
            <span class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              ${fmt12(m.time)}
            </span>
            <span class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              ${m.duration} min
            </span>
            <span class="flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              ${m.members} member${m.members!==1?'s':''}
            </span>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize" style="background:${col}18;color:${col};">${m.type||'other'}</span>
          </div>
          ${m.notes ? `<p class="text-xs text-gray-500 mb-3 leading-relaxed border-l-2 pl-2" style="border-color:${col};">${escHtml(m.notes)}</p>` : ''}
          <!-- Action buttons -->
          <div class="flex items-center gap-2">
            ${m.link && m.link !== '#'
              ? `<a href="${escHtml(m.link)}" target="_blank" rel="noopener"
                   class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl transition-colors text-white"
                   style="background:${col};">
                   <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.277A1 1 0 0121 8.68v6.64a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                   Join
                 </a>`
              : `<span class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-xl bg-gray-50 text-gray-400 border border-gray-100">No Link</span>`}
            <button data-edit-meet-id="${m.id}" title="Edit"
              class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 bg-gray-50 border border-gray-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-colors">
              <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>
            <button data-del-meet-id="${m.id}" title="Delete"
              class="w-8 h-8 flex items-center justify-center rounded-xl text-red-400 bg-red-50 border border-red-100 hover:bg-red-500 hover:text-white hover:border-red-500 transition-colors">
              <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>`;
  }).join('');
}

/* ── Click events ── */
document.getElementById('meeting-list').addEventListener('click', e => {
  const editEl = e.target.closest('[data-edit-meet-id]');
  const delEl  = e.target.closest('[data-del-meet-id]');
  if (editEl) openEditMeetModal(parseInt(editEl.dataset.editMeetId, 10));
  if (delEl)  { pendingDelMeet = parseInt(delEl.dataset.delMeetId, 10); openModal('delete-meet-wrap'); }
});

document.getElementById('btn-confirm-del-meet').addEventListener('click', async () => {
  if (pendingDelMeet) {
    await ApiClient.meetings.remove(pendingDelMeet);
    allMeetings = allMeetings.filter(m => m.id !== pendingDelMeet);
    renderMeetings();
    showToast('Meeting deleted', 'danger');
  }
  pendingDelMeet = null;
  closeModal('delete-meet-wrap');
});
document.getElementById('btn-cancel-del-meet').addEventListener('click', () => closeModal('delete-meet-wrap'));

/* ── Add meeting ── */
document.getElementById('btn-add-meet').addEventListener('click', () => {
  document.getElementById('meet-modal-title').textContent = 'New Meeting';
  document.getElementById('btn-submit-meet').textContent  = 'Add Meeting';
  document.getElementById('edit-meet-id').value = '';
  document.getElementById('meet-form').reset();
  document.getElementById('meet-time').value     = '09:00';
  document.getElementById('meet-duration').value = '30';
  document.getElementById('meet-members').value  = '2';
  openModal('meet-modal-wrap');
});

/* ── Edit meeting ── */
function openEditMeetModal(id) {
  const m = allMeetings.find(x => x.id === id);
  if (!m) return;
  document.getElementById('meet-modal-title').textContent = 'Edit Meeting';
  document.getElementById('btn-submit-meet').textContent  = 'Save Changes';
  document.getElementById('edit-meet-id').value   = id;
  document.getElementById('meet-title').value     = m.title;
  document.getElementById('meet-time').value      = m.time || '09:00';
  document.getElementById('meet-duration').value  = m.duration || 30;
  document.getElementById('meet-members').value   = m.members || 2;
  document.getElementById('meet-type').value      = m.type || 'standup';
  document.getElementById('meet-link').value      = (m.link && m.link !== '#') ? m.link : '';
  document.getElementById('meet-notes').value     = m.notes || '';
  openModal('meet-modal-wrap');
}

/* ── Submit ── */
document.getElementById('meet-form').addEventListener('submit', async e => {
  e.preventDefault();
  const title    = document.getElementById('meet-title').value.trim();
  if (!title) { document.getElementById('meet-title').classList.add('border-red-400'); return; }
  const time     = document.getElementById('meet-time').value;
  const duration = parseInt(document.getElementById('meet-duration').value) || 30;
  const members  = parseInt(document.getElementById('meet-members').value)  || 2;
  const type     = document.getElementById('meet-type').value;
  const link     = document.getElementById('meet-link').value.trim() || '#';
  const notes    = document.getElementById('meet-notes').value.trim();
  const editId   = document.getElementById('edit-meet-id').value;
  const btn      = document.getElementById('btn-submit-meet');
  btn.disabled   = true;

  if (editId) {
    const updated = await ApiClient.meetings.update(parseInt(editId, 10), {title, time, duration, members, type, link, notes});
    if (updated) {
      const idx = allMeetings.findIndex(m => m.id === parseInt(editId, 10));
      if (idx > -1) allMeetings[idx] = {...allMeetings[idx], ...updated};
      showToast('Meeting updated');
    }
  } else {
    const meet = await ApiClient.meetings.create({title, time, duration, members, type, link, notes});
    if (meet) {
      allMeetings.push(meet);
      allMeetings.sort((a, b) => (a.time || '').localeCompare(b.time || ''));
      showToast('Meeting added');
    }
  }

  btn.disabled = false;
  closeModal('meet-modal-wrap');
  document.getElementById('meet-form').reset();
  renderMeetings();
});

loadMeetings();
</script>
</body>
</html>
