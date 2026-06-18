<?php
/**
 * DayTrack – Sidebar Navigation Component
 * frontend/components/navbar.php
 */
$current = basename($_SERVER['PHP_SELF'], '.php');

$menuItems = [
  ['dashboard', 'Today', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'],
  ['tasks',     'To-do', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'],
  ['projects',  'Projects', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>'],
  ['meet',      'Meetings', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.277A1 1 0 0121 8.68v6.64a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>'],
  ['chat',      'Messages', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>'],
];

$userName    = htmlspecialchars($currentUser['name'] ?? 'User');
$userEmail   = htmlspecialchars($currentUser['email'] ?? '');
$userInitial = strtoupper(($currentUser['name'] ?? 'U')[0]);
?>

<!-- ════ SIDEBAR ════ -->
<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col shadow-sm transition-transform duration-300">

  <!-- Logo -->
  <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
    <span class="font-bold text-gray-900 text-lg">DayTrack</span>
    <button id="sidebar-toggle" class="ml-auto text-gray-400 hover:text-gray-600 transition-colors lg:hidden">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
      </svg>
    </button>
  </div>

  <!-- Scrollable area -->
  <div class="flex-1 overflow-y-auto py-4 px-3">

    <!-- MAIN MENU -->
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest px-3 mb-2">Main Menu</p>
    <nav class="space-y-0.5 mb-6">
      <?php foreach ($menuItems as [$page, $label, $icon]):
        $active = ($current === $page);
      ?>
      <a href="<?= $page ?>.php"
         class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 group
                <?= $active
                  ? 'bg-indigo-50 text-indigo-700'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
        <span class="<?= $active ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' ?> transition-colors">
          <?= $icon ?>
        </span>
        <?= $label ?>
        <?php if ($active): ?>
          <span class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </nav>

    <!-- LISTS / PROJECTS -->
    <div class="mb-6">
      <div class="flex items-center justify-between px-3 mb-2">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Lists</p>
        <a href="projects.php" class="w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-content-center text-white text-xs font-bold hover:bg-indigo-700 transition-colors" style="display:flex;align-items:center;justify-content:center;">+</a>
      </div>
      <div id="sidebar-projects" class="space-y-1">
        <!-- JS populated -->
        <div class="px-3 py-1.5 text-xs text-gray-400">Loading…</div>
      </div>
    </div>

    <!-- UPGRADE CARD -->
    <div class="mx-1 mb-4 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 p-4 text-white">
      <p class="font-bold text-sm mb-1">Upgrade plan</p>
      <p class="text-xs opacity-80 mb-3 leading-relaxed">Unlock your workspace, share your impact with multiple people and much more.</p>
      <a href="settings.php" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white text-indigo-600 hover:bg-indigo-50 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </a>
    </div>

    <!-- BOTTOM LINKS -->
    <nav class="space-y-0.5 px-0 mb-4">
      <a href="settings.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Settings
      </a>
      <button onclick="logoutUser()" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all text-left">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        Sign Out
      </button>
    </nav>
  </div>

  <!-- USER PROFILE -->
  <div class="border-t border-gray-100 px-4 py-3">
    <a href="profile.php" class="flex items-center gap-3 rounded-xl p-2 hover:bg-gray-50 transition-colors">
      <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-content-center text-white font-bold text-sm flex-shrink-0" style="display:flex;align-items:center;justify-content:center;">
        <?= $userInitial ?>
      </div>
      <div class="flex-1 min-w-0">
        <p class="font-semibold text-gray-900 text-sm truncate"><?= $userName ?></p>
        <p class="text-xs text-gray-400 truncate"><?= $userEmail ?></p>
      </div>
      <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
      </svg>
    </a>
  </div>
</aside>

<!-- Mobile overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden lg:hidden" onclick="closeSidebar()"></div>

<!-- Mobile hamburger -->
<button id="hamburger" onclick="openSidebar()" class="fixed top-4 left-4 z-30 lg:hidden w-10 h-10 bg-white rounded-xl shadow-md flex items-center justify-content-center border border-gray-200 hover:bg-gray-50 transition-colors" style="display:flex;align-items:center;justify-content:center;">
  <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
  </svg>
</button>

<script>
function openSidebar()  { document.getElementById('sidebar').style.transform='translateX(0)'; document.getElementById('sidebar-overlay').classList.remove('hidden'); }
function closeSidebar() { document.getElementById('sidebar').style.transform=''; document.getElementById('sidebar-overlay').classList.add('hidden'); }
async function logoutUser() {
  await fetch('../../api/auth.php?action=logout', {method:'POST', credentials:'same-origin'});
  window.location.href = 'login.php';
}
// Load sidebar projects
(async function() {
  try {
    const r    = await fetch('../../api/projects.php', {credentials:'same-origin'});
    const json = await r.json();
    const projs = (json.data || []).filter(p => !p.archived).slice(0, 6);
    const colors = {primary:'#6366f1',success:'#10b981',warning:'#f59e0b',danger:'#ef4444',info:'#06b6d4'};
    const el = document.getElementById('sidebar-projects');
    if (!el) return;
    if (!projs.length) { el.innerHTML = '<div class="px-3 py-1 text-xs text-gray-400">No projects yet</div>'; return; }
    el.innerHTML = projs.map(p => {
      const col = colors[p.color] || '#6366f1';
      return `
        <div class="project-group">
          <button type="button" class="project-toggle w-full flex items-center justify-between gap-2 px-3 py-2 rounded-xl text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all" data-project-id="${p.id}">
            <span class="flex items-center gap-2 min-w-0">
              <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${col};"></span>
              <span class="truncate">${p.name.substring(0,22)}${p.name.length>22?'…':''}</span>
            </span>
            <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
          <div class="project-submenu hidden pl-6 mt-1 space-y-1">
            <a href="tasks.php" class="block px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700">All tasks</a>
            <a href="projects.php" class="block px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700">Project details</a>
          </div>
        </div>`;
    }).join('');

    document.querySelectorAll('.project-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        const group = btn.closest('.project-group');
        const submenu = group?.querySelector('.project-submenu');
        const icon = btn.querySelector('svg');
        if (!submenu || !icon) return;
        const isOpen = !submenu.classList.contains('hidden');
        submenu.classList.toggle('hidden', isOpen);
        icon.style.transform = isOpen ? '' : 'rotate(90deg)';
      });
    });
  } catch(e) {}
})();
</script>
