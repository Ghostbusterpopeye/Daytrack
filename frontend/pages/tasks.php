<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DayTrack – Tasks</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    .skeleton{background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:800px 100%;animation:shimmer 1.4s infinite linear;border-radius:8px;}
    @keyframes shimmer{0%{background-position:-400px 0}100%{background-position:400px 0}}
    .fade-in{animation:fadeIn .35s ease both}@keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
    .modal-wrap{display:none;position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:200;align-items:center;justify-content:center;padding:16px;}
    .modal-wrap.open{display:flex;}
    ::-webkit-scrollbar{width:4px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:#e0e0e0;border-radius:4px}
  </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
  <?php include __DIR__ . '/../components/navbar.php'; ?>
  <main class="flex-1 lg:ml-64 min-h-screen">

    <!-- TOP BAR -->
    <header class="sticky top-0 z-20 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
      <div class="lg:hidden w-10"></div>
      <div>
        <h1 class="text-xl font-bold text-gray-900">My Tasks</h1>
        <p class="text-sm text-gray-400" id="task-subtitle">Loading…</p>
      </div>
      <button onclick="openModal('add')" class="flex items-center gap-2 text-sm font-semibold text-white bg-indigo-600 rounded-xl px-4 py-2 hover:bg-indigo-700 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Task
      </button>
    </header>

    <div class="px-4 sm:px-6 py-6 max-w-7xl mx-auto">
      <!-- FILTER BAR -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-4 flex flex-wrap items-center gap-3">
        <!-- Search -->
        <div class="flex items-center gap-2 flex-1 min-w-48 border border-gray-200 rounded-xl px-3 py-2 focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
          <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input type="text" id="search-input" placeholder="Search tasks…" class="flex-1 text-sm outline-none bg-transparent placeholder-gray-400" autocomplete="off"/>
        </div>
        <!-- Tabs -->
        <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1" id="filter-tabs">
          <button class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-white shadow-sm text-indigo-600 transition-all" data-filter="all">All</button>
          <button class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-700 transition-all" data-filter="open">Open</button>
          <button class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-700 transition-all" data-filter="closed">Done</button>
          <button class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-700 transition-all" data-filter="high">High</button>
        </div>
        <!-- Sort -->
        <select id="sort-select" class="text-xs font-medium border border-gray-200 rounded-xl px-3 py-2 outline-none bg-white text-gray-600 cursor-pointer hover:border-indigo-300 transition-colors">
          <option value="default">Default</option>
          <option value="priority">Priority</option>
          <option value="az">A–Z</option>
          <option value="due">Due Date</option>
        </select>
      </div>

      <!-- TASK LIST -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div id="task-list">
          <?php for($i=0;$i<6;$i++): ?>
          <div class="flex items-center gap-4 px-6 py-4 border-b border-gray-50">
            <div class="skeleton w-4 h-4 rounded flex-shrink-0"></div>
            <div class="flex-1">
              <div class="skeleton h-3.5 rounded w-2/3 mb-2"></div>
              <div class="skeleton h-2.5 rounded w-1/3"></div>
            </div>
            <div class="skeleton h-6 w-16 rounded-full"></div>
          </div>
          <?php endfor; ?>
        </div>
        <!-- Quick add -->
        <div class="flex items-center gap-4 px-6 py-3 border-t border-gray-100 bg-gray-50/50">
          <div class="w-4 h-4 rounded border-2 border-dashed border-gray-300 flex-shrink-0"></div>
          <input type="text" id="quick-add" placeholder="Quick add task…" class="flex-1 text-sm text-gray-600 placeholder-gray-300 outline-none bg-transparent"/>
          <button onclick="quickAdd()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Add</button>
        </div>
      </div>

    </div>
  </main>
</div>

<!-- ADD/EDIT TASK MODAL -->
<div class="modal-wrap" id="task-modal">
  <div class="bg-white rounded-2xl shadow-xl border border-gray-100 w-full max-w-md max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
      <h2 class="font-bold text-gray-900" id="modal-title">Add New Task</h2>
      <button onclick="closeModal('task-modal')" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-content-center text-gray-400 transition-colors" style="display:flex;align-items:center;justify-content:center;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="task-form" class="px-6 py-5 space-y-4">
      <input type="hidden" id="edit-task-id"/>
      <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Title *</label>
        <input type="text" id="modal-task-title" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all" placeholder="What needs to be done?" required/>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Project</label>
          <select id="modal-task-project" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
            <option>General</option><option>Design System</option><option>Marketing Web</option><option>Management</option><option>User Analytics</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Due Date</label>
          <input type="date" id="modal-task-due" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"/>
        </div>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Priority</label>
        <div class="grid grid-cols-3 gap-2">
          <?php foreach(['low'=>['green','Low'],'medium'=>['amber','Medium'],'high'=>['red','High']] as $v=>[$c,$l]): ?>
          <label class="flex items-center justify-content-center gap-1.5 border-2 border-gray-100 rounded-xl p-2.5 cursor-pointer hover:border-<?=$c?>-300 transition-all text-xs font-semibold text-gray-500 peer-checked:border-<?=$c?>-500" style="display:flex;align-items:center;justify-content:center;">
            <input type="radio" name="task-priority" value="<?=$v?>" class="sr-only peer" <?=$v==='medium'?'checked':''?>/>
            <span class="w-2 h-2 rounded-full bg-<?=$c?>-400"></span>
            <?=$l?>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Notes</label>
        <textarea id="modal-task-notes" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all resize-none" placeholder="Optional notes…"></textarea>
      </div>
      <button type="submit" id="btn-submit-task" class="w-full bg-indigo-600 text-white font-semibold text-sm rounded-xl py-3 hover:bg-indigo-700 transition-colors">
        Add Task
      </button>
    </form>
  </div>
</div>

<!-- DELETE MODAL -->
<div class="modal-wrap" id="delete-modal">
  <div class="bg-white rounded-2xl shadow-xl border border-gray-100 w-full max-w-sm p-6 text-center">
    <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-content-center mx-auto mb-4" style="display:flex;align-items:center;justify-content:center;">
      <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    </div>
    <h3 class="font-bold text-gray-900 mb-2">Delete Task?</h3>
    <p class="text-sm text-gray-500 mb-6">This action cannot be undone.</p>
    <div class="flex gap-3">
      <button onclick="closeModal('delete-modal')" class="flex-1 border border-gray-200 text-gray-700 font-semibold text-sm rounded-xl py-2.5 hover:bg-gray-50 transition-colors">Cancel</button>
      <button id="btn-confirm-delete" class="flex-1 bg-red-500 text-white font-semibold text-sm rounded-xl py-2.5 hover:bg-red-600 transition-colors">Delete</button>
    </div>
  </div>
</div>

<script src="../../assets/js/daytrack.js"></script>
<script>
const API = '../../api';
let allTasks = [], pendingDelId = null, activeFilter = 'all', searchQuery = '', sortMode = 'default';

const priStyles = {
  high:   'bg-red-100 text-red-600',
  medium: 'bg-amber-100 text-amber-600',
  low:    'bg-green-100 text-green-600'
};

async function loadTasks() {
  const data = await ApiClient.tasks.getAll();
  allTasks = data || [];
  renderTasks();
}

function getFiltered() {
  let t = [...allTasks];
  if (activeFilter==='open')   t = t.filter(x=>!x.done);
  if (activeFilter==='closed') t = t.filter(x=> x.done);
  if (activeFilter==='high')   t = t.filter(x=>x.priority==='high');
  if (searchQuery) { const q=searchQuery.toLowerCase(); t=t.filter(x=>x.title.toLowerCase().includes(q)||(x.project||'').toLowerCase().includes(q)); }
  if (sortMode==='priority') { const o={high:0,medium:1,low:2}; t.sort((a,b)=>(o[a.priority]||1)-(o[b.priority]||1)); }
  else if (sortMode==='az')  t.sort((a,b)=>a.title.localeCompare(b.title));
  else if (sortMode==='due') t.sort((a,b)=>{ if(!a.due&&!b.due) return 0; if(!a.due) return 1; if(!b.due) return -1; return a.due.localeCompare(b.due); });
  return t;
}

function renderTasks() {
  const done  = allTasks.filter(t=>t.done).length;
  const open  = allTasks.filter(t=>!t.done).length;
  document.getElementById('task-subtitle').textContent = `${open} remaining · ${done} completed`;

  const tasks = getFiltered();
  const list  = document.getElementById('task-list');
  if (!tasks.length) {
    list.innerHTML = `<div class="text-center py-12 text-gray-400">
      <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
      <p class="text-sm">No tasks found.</p></div>`;
    return;
  }
  const today = new Date().toISOString().split('T')[0];
  list.innerHTML = tasks.map((t,i) => {
    const overdue = t.due && !t.done && t.due < today;
    return `
    <div class="flex items-center gap-4 px-6 py-3.5 border-b border-gray-50 hover:bg-gray-50/70 transition-colors group fade-in" style="animation-delay:${Math.min(i*.02,.25)}s;" data-task-id="${t.id}">
      <button onclick="toggleDone(${t.id},${t.done})" class="w-4 h-4 rounded border-2 flex-shrink-0 flex items-center justify-content-center transition-all
              ${t.done?'bg-indigo-600 border-indigo-600':'border-gray-300 hover:border-indigo-400'}" style="min-width:16px;display:flex;align-items:center;justify-content:center;">
        ${t.done?`<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`:''}
      </button>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium ${t.done?'line-through text-gray-400':'text-gray-800'} truncate">${escHtml(t.title)}</p>
        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
          <span class="text-xs text-gray-400">${escHtml(t.project||'General')}</span>
          ${t.due?`<span class="text-xs ${overdue?'text-red-500 font-medium':'text-gray-400'}">· ${t.due}</span>`:''}
        </div>
      </div>
      <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full ${priStyles[t.priority]||priStyles.medium}">${t.priority||'medium'}</span>
        <button onclick="openModal('edit',${t.id})" class="w-7 h-7 rounded-lg hover:bg-indigo-50 flex items-center justify-content-center text-gray-400 hover:text-indigo-600 transition-colors" style="display:flex;align-items:center;justify-content:center;" title="Edit">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </button>
        <button onclick="confirmDelete(${t.id})" class="w-7 h-7 rounded-lg hover:bg-red-50 flex items-center justify-content-center text-gray-400 hover:text-red-500 transition-colors" style="display:flex;align-items:center;justify-content:center;" title="Delete">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
      </div>
    </div>`; }).join('');
}

/* ── Actions ── */
async function toggleDone(id, isDone) {
  await ApiClient.tasks.update(id, {done: !isDone});
  const idx = allTasks.findIndex(t=>t.id===id);
  if (idx>-1) allTasks[idx].done = !isDone;
  renderTasks();
  showToast(!isDone ? 'Task completed' : 'Task reopened');
}

function confirmDelete(id) { pendingDelId=id; openModal('delete'); }
document.getElementById('btn-confirm-delete').addEventListener('click', async () => {
  if (pendingDelId) { await ApiClient.tasks.remove(pendingDelId); allTasks=allTasks.filter(t=>t.id!==pendingDelId); renderTasks(); showToast('Deleted','danger'); }
  pendingDelId=null; closeModal('delete-modal');
});

/* ── Modals ── */
function openModal(mode, id) {
  if (mode==='delete') { document.getElementById('delete-modal').classList.add('open'); return; }
  const form = document.getElementById('task-form');
  form.reset(); document.getElementById('pri-med-radio')?.setAttribute('checked','');
  if (mode==='edit' && id) {
    const t = allTasks.find(x=>x.id===id);
    if (!t) return;
    document.getElementById('modal-title').textContent = 'Edit Task';
    document.getElementById('btn-submit-task').textContent = 'Save Changes';
    document.getElementById('edit-task-id').value         = id;
    document.getElementById('modal-task-title').value     = t.title;
    document.getElementById('modal-task-project').value   = t.project||'General';
    document.getElementById('modal-task-due').value       = t.due||'';
    document.getElementById('modal-task-notes').value     = t.notes||'';
    const priEl = document.querySelector(`input[name="task-priority"][value="${t.priority||'medium'}"]`);
    if (priEl) priEl.checked = true;
  } else {
    document.getElementById('modal-title').textContent = 'Add New Task';
    document.getElementById('btn-submit-task').textContent = 'Add Task';
    document.getElementById('edit-task-id').value = '';
    const med = document.querySelector('input[name="task-priority"][value="medium"]');
    if (med) med.checked = true;
  }
  document.getElementById('task-modal').classList.add('open');
}
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-wrap').forEach(el => el.addEventListener('click', e => { if(e.target===el) el.classList.remove('open'); }));

document.getElementById('task-form').addEventListener('submit', async e => {
  e.preventDefault();
  const title   = document.getElementById('modal-task-title').value.trim();
  if (!title) return;
  const priority = document.querySelector('input[name="task-priority"]:checked')?.value||'medium';
  const project  = document.getElementById('modal-task-project').value;
  const due      = document.getElementById('modal-task-due').value;
  const notes    = document.getElementById('modal-task-notes').value.trim();
  const editId   = document.getElementById('edit-task-id').value;
  const btn = document.getElementById('btn-submit-task');
  btn.disabled = true; btn.textContent = 'Saving…';

  if (editId) {
    const upd = await ApiClient.tasks.update(parseInt(editId,10),{title,project,priority,due,notes});
    if (upd) { const idx=allTasks.findIndex(t=>t.id===parseInt(editId,10)); if(idx>-1) allTasks[idx]={...allTasks[idx],...upd}; showToast('Updated'); }
  } else {
    const task = await ApiClient.tasks.create({title,project,done:false,priority,due,notes});
    if (task) { allTasks.unshift(task); showToast('Task added'); }
  }
  btn.disabled=false; closeModal('task-modal'); renderTasks();
});

/* ── Quick add ── */
async function quickAdd() {
  const inp = document.getElementById('quick-add');
  const title = inp.value.trim();
  if (!title) return;
  const task = await ApiClient.tasks.create({title,project:'General',done:false,priority:'medium'});
  if (task) { allTasks.unshift(task); inp.value=''; renderTasks(); showToast('Task added'); }
}
document.getElementById('quick-add').addEventListener('keydown', e => { if(e.key==='Enter') quickAdd(); });

/* ── Filters ── */
document.getElementById('filter-tabs').addEventListener('click', e => {
  const btn = e.target.closest('[data-filter]');
  if (!btn) return;
  activeFilter = btn.dataset.filter;
  document.querySelectorAll('.tab-btn').forEach(b => {
    const a = b.dataset.filter===activeFilter;
    b.className = `tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all ${a?'bg-white shadow-sm text-indigo-600':'text-gray-500 hover:text-gray-700'}`;
  });
  renderTasks();
});
document.getElementById('search-input').addEventListener('input', e => { searchQuery=e.target.value; renderTasks(); });
document.getElementById('sort-select').addEventListener('change', e => { sortMode=e.target.value; renderTasks(); });

loadTasks();
</script>
</body>
</html>
