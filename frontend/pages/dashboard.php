<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
$userName    = htmlspecialchars($currentUser['name']);
$userInitial = strtoupper($userName[0] ?? 'U');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DayTrack – Dashboard</title>
  <meta name="description" content="Your productivity dashboard."/>
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
    [x-cloak]{display:none}
    .skeleton{background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:800px 100%;animation:shimmer 1.4s infinite linear;border-radius:8px;}
    @keyframes shimmer{0%{background-position:-400px 0}100%{background-position:400px 0}}
    .fade-in{animation:fadeIn .4s ease both}
    @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
    ::-webkit-scrollbar{width:4px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:#e0e0e0;border-radius:4px}
  </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased">

<!-- LAYOUT WRAPPER -->
<div class="flex min-h-screen">

  <!-- ════ SIDEBAR ════ -->
  <?php include __DIR__ . '/../components/navbar.php'; ?>

  <!-- ════ MAIN CONTENT ════ -->
  <main class="flex-1 lg:ml-64 min-h-screen">

    <!-- TOP BAR -->
    <header class="sticky top-0 z-20 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
      <div class="lg:hidden w-10"></div><!-- spacer for hamburger -->
      <div>
        <p class="text-sm text-gray-400" id="date-label">Loading…</p>
        <h1 class="text-xl font-bold text-gray-900" id="greeting-title">Good morning</h1>
      </div>
      <div class="flex items-center gap-3">
        <!-- Team avatars -->
        <div class="flex -space-x-2 hidden sm:flex">
          <div class="w-8 h-8 rounded-full bg-indigo-200 border-2 border-white flex items-center justify-content-center text-xs font-bold text-indigo-700" style="display:flex;align-items:center;justify-content:center;">A</div>
          <div class="w-8 h-8 rounded-full bg-purple-200 border-2 border-white flex items-center justify-content-center text-xs font-bold text-purple-700" style="display:flex;align-items:center;justify-content:center;">B</div>
          <div class="w-8 h-8 rounded-full bg-pink-200 border-2 border-white flex items-center justify-content-center text-xs font-bold text-pink-700" style="display:flex;align-items:center;justify-content:center;">C</div>
        </div>
        <div class="text-right hidden sm:block">
          <p class="text-xs font-semibold text-gray-700"><?= $userName ?></p>
          <p class="text-xs text-gray-400 flex items-center gap-1 justify-end">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
            <span id="header-member-count">–</span>
          </p>
        </div>
      </div>
    </header>

    <!-- PAGE BODY -->
    <div class="px-4 sm:px-6 py-6 max-w-7xl mx-auto">
      <!-- TODAY'S TASKS -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 fade-in">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
          <h2 class="font-bold text-gray-900 text-base">Today's Tasks</h2>
          <div class="flex items-center gap-2">
            <a href="tasks.php" class="flex items-center gap-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Focus Mode
            </a>
          </div>
        </div>

        <!-- Task list -->
        <div id="today-tasks" class="divide-y divide-gray-50">
          <?php for($i=0;$i<4;$i++): ?>
          <div class="px-6 py-3 flex items-center gap-3">
            <div class="skeleton w-4 h-4 rounded flex-shrink-0"></div>
            <div class="flex-1">
              <div class="skeleton h-3.5 rounded w-2/3 mb-1.5"></div>
              <div class="skeleton h-2.5 rounded w-1/4"></div>
            </div>
          </div>
          <?php endfor; ?>
        </div>

        <!-- Quick add -->
        <div class="px-6 py-3 border-t border-gray-50 flex items-center gap-3">
          <div class="w-4 h-4 rounded border-2 border-dashed border-gray-300 flex-shrink-0"></div>
          <input type="text" id="quick-add" placeholder="Add a task…" class="flex-1 text-sm text-gray-600 placeholder-gray-300 outline-none bg-transparent"/>
          <button onclick="quickAddTask()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">Add</button>
        </div>
      </div>

      <!-- PROJECTS + MEETINGS ROW -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        <!-- Active Projects -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm fade-in">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="font-bold text-gray-900 text-sm">Active Projects</h2>
            <a href="projects.php" class="text-xs text-indigo-600 font-medium hover:underline">See all</a>
          </div>
          <div id="dash-projects" class="p-3 space-y-2">
            <?php for($i=0;$i<3;$i++): ?>
            <div class="p-3 rounded-xl border border-gray-50">
              <div class="skeleton h-3.5 rounded w-3/5 mb-2"></div>
              <div class="skeleton h-1.5 rounded w-full mb-1"></div>
              <div class="skeleton h-2.5 rounded w-1/4"></div>
            </div>
            <?php endfor; ?>
          </div>
        </div>

        <!-- Upcoming Meetings -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm fade-in">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
            <h2 class="font-bold text-gray-900 text-sm">Upcoming Meetings</h2>
            <a href="meet.php" class="text-xs text-indigo-600 font-medium hover:underline">See all</a>
          </div>
          <div id="dash-meetings" class="p-3 space-y-2">
            <?php for($i=0;$i<3;$i++): ?>
            <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-50">
              <div class="skeleton w-9 h-9 rounded-xl flex-shrink-0"></div>
              <div class="flex-1">
                <div class="skeleton h-3.5 rounded w-3/4 mb-1.5"></div>
                <div class="skeleton h-2.5 rounded w-1/3"></div>
              </div>
            </div>
            <?php endfor; ?>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<script src="../../assets/js/daytrack.js"></script>
<script>
const API = '../../api';

/* ── Date & Greeting ── */
(function() {
  const now   = new Date();
  const days  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const mons  = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  document.getElementById('date-label').textContent = `${days[now.getDay()]}, ${mons[now.getMonth()]} ${now.getDate()}`;
  const h = now.getHours();
  const g = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : 'Good Evening';
  document.getElementById('greeting-title').textContent = `${g}, <?= $userName ?>`;
})();

const priColors = {high:'bg-red-100 text-red-600', medium:'bg-amber-100 text-amber-600', low:'bg-green-100 text-green-600'};
const typeColors= {standup:'#6366f1',review:'#06b6d4',update:'#10b981','one-on-one':'#f59e0b',planning:'#ef4444',other:'#8b5cf6'};

/* ── Quick add task ── */
async function quickAddTask() {
  const input = document.getElementById('quick-add');
  const title = input.value.trim();
  if (!title) return;
  const task = await ApiClient.tasks.create({title, project:'General', done:false, priority:'medium'});
  if (task) { input.value = ''; showToast('Task added'); loadDashboard(); }
}
document.getElementById('quick-add').addEventListener('keydown', e => { if(e.key==='Enter') quickAddTask(); });

/* ── Load All ── */
async function loadDashboard() {
  const [tasks, projects, meetings] = await Promise.all([
    ApiClient.tasks.getAll(),
    ApiClient.projects.getAll(),
    ApiClient.meetings.getAll(),
  ]);

  const t = tasks    || [];
  const p = projects || [];
  const m = meetings || [];

  document.getElementById('header-member-count').textContent = p.reduce((s,x)=>s+(x.members||0),0) + ' members';

  // Today tasks
  renderTodayTasks(t.slice(0,8));
  renderDashProjects(p.filter(x=>!x.archived).slice(0,4));
  renderDashMeetings(m.slice(0,4));
}

function renderTodayTasks(tasks) {
  const el = document.getElementById('today-tasks');
  if (!tasks.length) {
    el.innerHTML = `<div class="px-6 py-8 text-center text-sm text-gray-400">
      <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      No tasks yet. <a href="tasks.php" class="text-indigo-600 underline">Add one</a>
    </div>`;
    return;
  }
  el.innerHTML = tasks.map(t => `
    <div class="px-6 py-3 flex items-center gap-3 hover:bg-gray-50 transition-colors group" data-task-id="${t.id}">
      <button onclick="toggleTask(${t.id},${t.done?'true':'false'})" class="w-4 h-4 rounded border-2 flex-shrink-0 transition-all flex items-center justify-content-center flex-shrink-0
              ${t.done ? 'bg-indigo-600 border-indigo-600' : 'border-gray-300 hover:border-indigo-400'}"
        style="display:flex;align-items:center;justify-content:center;min-width:16px;">
        ${t.done ? '<svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>' : ''}
      </button>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-medium ${t.done ? 'line-through text-gray-400' : 'text-gray-700'} truncate">${escHtml(t.title)}</p>
        <p class="text-xs text-gray-400 mt-0.5">${escHtml(t.project||'General')}</p>
      </div>
      <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
        <span class="text-xs font-medium px-2 py-0.5 rounded-full ${priColors[t.priority]||priColors.medium}">${t.priority||'medium'}</span>
        <a href="tasks.php" class="text-gray-400 hover:text-gray-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </a>
      </div>
    </div>`).join('');
}

async function toggleTask(id, isDone) {
  await ApiClient.tasks.update(id, {done: !isDone});
  loadDashboard();
}

function renderDashProjects(projs) {
  const el = document.getElementById('dash-projects');
  if (!projs.length) { el.innerHTML = `<p class="text-sm text-gray-400 text-center py-4">No active projects.</p>`; return; }
  const colHex = {primary:'#6366f1',success:'#10b981',warning:'#f59e0b',danger:'#ef4444',info:'#06b6d4'};
  el.innerHTML = projs.map(p => {
    const col = colHex[p.color]||'#6366f1';
    const pct = p.progress||0;
    return `
    <a href="projects.php" class="block p-3 rounded-xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all group">
      <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
          <div class="w-6 h-6 rounded-lg flex items-center justify-content-center" style="background:${col}22;display:flex;align-items:center;justify-content:center;">
            <span style="width:8px;height:8px;border-radius:50%;background:${col};display:block;"></span>
          </div>
          <span class="text-sm font-semibold text-gray-800">${escHtml(p.name)}</span>
        </div>
        <span class="text-xs font-bold" style="color:${col};">${pct}%</span>
      </div>
      <div class="w-full bg-gray-100 rounded-full h-1.5 mb-1.5">
        <div class="h-1.5 rounded-full transition-all" style="width:${pct}%;background:${col};"></div>
      </div>
      <p class="text-xs text-gray-400">${p.members||0} members · ${p.done_tasks||0}/${p.total_tasks||0} tasks</p>
    </a>`; }).join('');
}

function renderDashMeetings(meets) {
  const el = document.getElementById('dash-meetings');
  if (!meets.length) { el.innerHTML = `<p class="text-sm text-gray-400 text-center py-4">No meetings scheduled.</p>`; return; }
  el.innerHTML = meets.map(m => {
    const col = typeColors[m.type]||'#6366f1';
    return `
    <div class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all">
      <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-content-center" style="background:${col}18;display:flex;align-items:center;justify-content:center;">
        <svg class="w-4 h-4" style="color:${col};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.277A1 1 0 0121 8.68v6.64a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-gray-800 truncate">${escHtml(m.title)}</p>
        <p class="text-xs text-gray-400">${fmt12(m.time)} · ${m.duration} min</p>
      </div>
      ${m.link&&m.link!=='#'?`<a href="${escHtml(m.link)}" target="_blank" class="text-xs font-medium text-indigo-600 border border-indigo-200 rounded-lg px-2.5 py-1 hover:bg-indigo-50 transition-colors flex-shrink-0">Join</a>`:''}
    </div>`; }).join('');
}

loadDashboard();
</script>
</body>
</html>
