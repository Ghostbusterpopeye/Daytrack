<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DayTrack – Projects</title>
  <meta name="description" content="Manage all your projects in one place with DayTrack."/>
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
    .pill-tab{padding:6px 16px;border-radius:9999px;font-size:.8rem;font-weight:600;transition:all .15s;}
    .pill-tab.active{background:#6366f1;color:#fff;}
    .pill-tab:not(.active){color:#6b7280;}
    .pill-tab:not(.active):hover{background:#f3f4f6;}
    input[type=radio].color-radio{display:none;}
    input[type=radio].color-radio:checked + label{box-shadow:0 0 0 3px #fff,0 0 0 5px var(--col);}
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
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Workspace</p>
        <h1 class="text-xl font-bold text-gray-900">Projects</h1>
        <p class="text-sm text-gray-400" id="proj-subtitle">Loading…</p>
      </div>
      <button id="btn-add-proj"
        class="flex items-center gap-2 bg-indigo-600 text-white font-semibold text-sm rounded-xl px-4 py-2 hover:bg-indigo-700 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        New Project
      </button>
    </header>

    <!-- PAGE BODY -->
    <div class="px-4 sm:px-6 py-6 max-w-7xl mx-auto">
      <!-- SEARCH + FILTER -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="relative flex-1">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
          <input type="text" id="proj-search" placeholder="Search projects…" autocomplete="off"
            class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-sm text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all"/>
        </div>
        <div class="flex items-center gap-1 bg-gray-50 rounded-xl p-1" id="proj-tabs">
          <button class="pill-tab active" data-ptab="all">All</button>
          <button class="pill-tab" data-ptab="active">Active</button>
          <button class="pill-tab" data-ptab="archived">Archived</button>
        </div>
      </div>

      <!-- PROJECT GRID -->
      <div id="project-list" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <?php for($i=0;$i<4;$i++): ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 fade-in">
          <div class="flex items-center gap-3 mb-4">
            <div class="skeleton w-12 h-12 rounded-xl flex-shrink-0"></div>
            <div class="flex-1">
              <div class="skeleton h-4 w-36 rounded mb-2"></div>
              <div class="skeleton h-3 w-24 rounded"></div>
            </div>
            <div class="skeleton h-6 w-12 rounded-lg"></div>
          </div>
          <div class="skeleton h-2 w-full rounded-full mb-3"></div>
          <div class="flex gap-2">
            <div class="skeleton h-8 flex-1 rounded-xl"></div>
            <div class="skeleton h-8 w-8 rounded-xl"></div>
            <div class="skeleton h-8 w-8 rounded-xl"></div>
            <div class="skeleton h-8 w-8 rounded-xl"></div>
          </div>
        </div>
        <?php endfor; ?>
      </div>

    </div>
  </main>
</div>

<!-- ══ ADD/EDIT PROJECT MODAL ══ -->
<div class="modal-wrap" id="proj-modal-wrap" role="dialog" aria-modal="true" aria-labelledby="proj-modal-title">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md fade-in" onclick="event.stopPropagation()">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-lg font-bold text-gray-900" id="proj-modal-title">New Project</h2>
      <button onclick="closeModal('proj-modal-wrap')" class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="project-form" novalidate>
      <input type="hidden" id="edit-proj-id"/>

      <!-- Name -->
      <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Project Name *</label>
        <input type="text" id="proj-name" placeholder="e.g. Mobile App Redesign" maxlength="60" required
          class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all"/>
      </div>

      <!-- Description -->
      <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
        <textarea id="proj-desc" rows="2" placeholder="What is this project about?"
          class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all resize-none"></textarea>
      </div>

      <!-- Color -->
      <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-600 mb-2">Color</label>
        <div class="flex items-center gap-3">
          <?php foreach(['primary'=>['#6366f1','Indigo'],'success'=>['#10b981','Green'],'warning'=>['#f59e0b','Amber'],'danger'=>['#ef4444','Red'],'info'=>['#06b6d4','Cyan']] as $k=>[$hex,$label]): ?>
          <div>
            <input type="radio" name="proj-color" id="col-<?=$k?>" value="<?=$k?>" class="color-radio" <?=$k==='primary'?'checked':''?> style="--col:<?=$hex?>"/>
            <label for="col-<?=$k?>" title="<?=$label?>" style="--col:<?=$hex?>;background:<?=$hex?>22;border:2px solid <?=$hex?>66;"
              class="block w-9 h-9 rounded-full cursor-pointer flex items-center justify-center transition-all hover:scale-110">
              <span style="width:14px;height:14px;border-radius:50%;background:<?=$hex?>;display:block;"></span>
            </label>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Icon -->
      <div class="mb-4">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Icon</label>
        <select id="proj-icon"
          class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all">
          <option value="briefcase">Briefcase</option>
          <option value="layers">Layers</option>
          <option value="globe">Global</option>
          <option value="chart">Analytics</option>
          <option value="phone">Mobile</option>
          <option value="palette">Design</option>
          <option value="code">Development</option>
          <option value="megaphone">Marketing</option>
          <option value="star">Priority</option>
          <option value="shield">Security</option>
        </select>
      </div>

      <!-- Members -->
      <div class="mb-5">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Team Members</label>
        <input type="number" id="proj-members" value="1" min="1" max="100"
          class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all"/>
      </div>

      <button type="submit" id="btn-submit-proj"
        class="w-full bg-indigo-600 text-white font-semibold text-sm rounded-xl px-4 py-2.5 hover:bg-indigo-700 transition-colors">
        Create Project
      </button>
    </form>
  </div>
</div>

<!-- ══ DELETE CONFIRM MODAL ══ -->
<div class="modal-wrap" id="delete-proj-wrap">
  <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm text-center fade-in" onclick="event.stopPropagation()">
    <div class="w-14 h-14 rounded-full bg-red-50 border border-red-100 flex items-center justify-center mx-auto mb-4">
      <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    </div>
    <h3 class="text-base font-bold text-gray-900 mb-1">Delete Project?</h3>
    <p class="text-sm text-gray-500 mb-5">This action cannot be undone. All data will be lost.</p>
    <div class="flex gap-3">
      <button id="btn-cancel-del-proj" class="flex-1 px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Cancel</button>
      <button id="btn-confirm-del-proj" class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-red-500 rounded-xl hover:bg-red-600 transition-colors">Delete</button>
    </div>
  </div>
</div>

<script src="../../assets/js/daytrack.js"></script>
<script>
const API = '../../api';
let allProjects = [], pendingDelProj = null, activeTab = 'all', projSearch = '';

const colHex  = {primary:'#6366f1',success:'#10b981',warning:'#f59e0b',danger:'#ef4444',info:'#06b6d4'};
const iconMap  = {
  briefcase:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>`,
  layers:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>`,
  globe:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
  chart:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>`,
  phone:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>`,
  palette:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>`,
  code:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>`,
  megaphone:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>`,
  star:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>`,
  shield:`<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>`,
};

function openModal(id)  { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
document.querySelectorAll('.modal-wrap').forEach(el => el.addEventListener('click', e => { if(e.target===el) closeModal(el.id); }));

async function loadProjects() {
  const data = await ApiClient.projects.getAll();
  allProjects = data || [];
  renderProjects();
}

function getFiltered() {
  let p = [...allProjects];
  if (activeTab === 'active')   p = p.filter(x => !x.archived);
  if (activeTab === 'archived') p = p.filter(x =>  x.archived);
  if (projSearch) { const q = projSearch.toLowerCase(); p = p.filter(x => x.name.toLowerCase().includes(q)); }
  return p;
}

function renderProjects() {
  const act = allProjects.filter(p => !p.archived).length;
  const arc = allProjects.filter(p =>  p.archived).length;

  document.getElementById('proj-subtitle').textContent = `${act} active project${act !== 1 ? 's' : ''}`;

  const list  = document.getElementById('project-list');
  const projs = getFiltered();

  if (!projs.length) {
    list.innerHTML = `
      <div class="col-span-2 py-16 text-center text-gray-400 bg-white rounded-2xl border border-gray-100 shadow-sm">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
        <p class="font-semibold text-sm">No projects found</p>
        <p class="text-xs mt-1">Create your first project to get started.</p>
      </div>`;
    return;
  }

  list.innerHTML = projs.map((p, i) => {
    const col = colHex[p.color] || '#6366f1';
    const pct = p.progress || 0;
    const svg = iconMap[p.icon] || iconMap.briefcase;
    return `
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 fade-in hover:shadow-md transition-shadow ${p.archived ? 'opacity-60' : ''}"
         style="animation-delay:${Math.min(i*.05,.3)}s" id="proj-card-${p.id}">
      <div class="flex items-start gap-3 mb-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:${col}18;color:${col};">${svg}</div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-0.5">
            <p class="font-bold text-gray-900 text-sm truncate">${escHtml(p.name)}</p>
            ${p.archived ? '<span class="text-xs font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 flex-shrink-0">Archived</span>' : ''}
          </div>
          <p class="text-xs text-gray-400">${p.members||0} member${(p.members||0)!==1?'s':''} · ${p.done_tasks||0}/${p.total_tasks||0} tasks</p>
          ${p.desc ? `<p class="text-xs text-gray-500 mt-1 truncate">${escHtml(p.desc)}</p>` : ''}
        </div>
        <span class="text-sm font-bold flex-shrink-0" style="color:${col};">${pct}%</span>
      </div>
      <!-- Progress bar -->
      <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4">
        <div class="h-1.5 rounded-full transition-all duration-500" style="width:${pct}%;background:${col};"></div>
      </div>
      <!-- Actions -->
      <div class="flex items-center gap-2">
        <a href="tasks.php" class="flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold py-2 rounded-xl border transition-colors"
           style="background:${col}10;color:${col};border-color:${col}30;">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Tasks
        </a>
        <button data-edit-proj="${p.id}" title="Edit"
          class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 bg-gray-50 border border-gray-100 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-colors">
          <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </button>
        <button data-archive-proj="${p.id}" data-archived="${p.archived?1:0}" title="${p.archived?'Restore':'Archive'}"
          class="w-8 h-8 flex items-center justify-center rounded-xl border transition-colors ${p.archived ? 'text-emerald-500 bg-emerald-50 border-emerald-200' : 'text-gray-400 bg-gray-50 border-gray-100 hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200'}">
          <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${p.archived
              ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>'
              : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>'}
          </svg>
        </button>
        <button data-del-proj="${p.id}" title="Delete"
          class="w-8 h-8 flex items-center justify-center rounded-xl text-red-400 bg-red-50 border border-red-100 hover:bg-red-500 hover:text-white hover:border-red-500 transition-colors">
          <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
      </div>
    </div>`;
  }).join('');
}

/* ── Events ── */
document.getElementById('project-list').addEventListener('click', async e => {
  const editEl    = e.target.closest('[data-edit-proj]');
  const delEl     = e.target.closest('[data-del-proj]');
  const archiveEl = e.target.closest('[data-archive-proj]');

  if (editEl)    openEditProjModal(parseInt(editEl.dataset.editProj, 10));
  if (delEl)     { pendingDelProj = parseInt(delEl.dataset.delProj, 10); openModal('delete-proj-wrap'); }
  if (archiveEl) {
    const id  = parseInt(archiveEl.dataset.archiveProj, 10);
    const was = archiveEl.dataset.archived === '1';
    await ApiClient.projects.update(id, { archived: !was });
    const idx = allProjects.findIndex(p => p.id === id);
    if (idx > -1) allProjects[idx].archived = !was;
    renderProjects();
    showToast(was ? 'Project restored' : 'Project archived');
  }
});

document.getElementById('btn-confirm-del-proj').addEventListener('click', async () => {
  if (pendingDelProj) {
    await ApiClient.projects.remove(pendingDelProj);
    allProjects = allProjects.filter(p => p.id !== pendingDelProj);
    renderProjects();
    showToast('Project deleted', 'danger');
  }
  pendingDelProj = null;
  closeModal('delete-proj-wrap');
});
document.getElementById('btn-cancel-del-proj').addEventListener('click', () => closeModal('delete-proj-wrap'));

/* ── New/Edit modal ── */
document.getElementById('btn-add-proj').addEventListener('click', () => {
  document.getElementById('proj-modal-title').textContent = 'New Project';
  document.getElementById('btn-submit-proj').textContent  = 'Create Project';
  document.getElementById('edit-proj-id').value = '';
  document.getElementById('project-form').reset();
  document.getElementById('col-primary').checked = true;
  openModal('proj-modal-wrap');
});

function openEditProjModal(id) {
  const p = allProjects.find(x => x.id === id);
  if (!p) return;
  document.getElementById('proj-modal-title').textContent = 'Edit Project';
  document.getElementById('btn-submit-proj').textContent  = 'Save Changes';
  document.getElementById('edit-proj-id').value  = id;
  document.getElementById('proj-name').value     = p.name;
  document.getElementById('proj-desc').value     = p.desc || '';
  document.getElementById('proj-members').value  = p.members || 1;
  document.getElementById('proj-icon').value     = p.icon || 'briefcase';
  const colEl = document.querySelector(`input[name="proj-color"][value="${p.color||'primary'}"]`);
  if (colEl) colEl.checked = true;
  openModal('proj-modal-wrap');
}

document.getElementById('project-form').addEventListener('submit', async e => {
  e.preventDefault();
  const name    = document.getElementById('proj-name').value.trim();
  if (!name) return;
  const color   = document.querySelector('input[name="proj-color"]:checked')?.value || 'primary';
  const icon    = document.getElementById('proj-icon').value;
  const desc    = document.getElementById('proj-desc').value.trim();
  const members = parseInt(document.getElementById('proj-members').value) || 1;
  const editId  = document.getElementById('edit-proj-id').value;
  const btn     = document.getElementById('btn-submit-proj');
  btn.disabled  = true;

  if (editId) {
    const upd = await ApiClient.projects.update(parseInt(editId, 10), {name, color, icon, desc, members});
    if (upd) {
      const idx = allProjects.findIndex(p => p.id === parseInt(editId, 10));
      if (idx > -1) allProjects[idx] = {...allProjects[idx], ...upd};
      showToast('Project updated');
    }
  } else {
    const proj = await ApiClient.projects.create({name, color, icon, desc, members});
    if (proj) { allProjects.unshift(proj); showToast('Project created'); }
  }

  btn.disabled = false;
  closeModal('proj-modal-wrap');
  renderProjects();
});

/* ── Tabs & search ── */
document.getElementById('proj-tabs').addEventListener('click', e => {
  const btn = e.target.closest('[data-ptab]');
  if (!btn) return;
  activeTab = btn.dataset.ptab;
  document.querySelectorAll('#proj-tabs .pill-tab').forEach(b => b.classList.toggle('active', b.dataset.ptab === activeTab));
  renderProjects();
});
document.getElementById('proj-search').addEventListener('input', e => { projSearch = e.target.value; renderProjects(); });

loadProjects();
</script>
</body>
</html>
