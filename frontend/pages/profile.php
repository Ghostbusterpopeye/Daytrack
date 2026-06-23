<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
$userName    = htmlspecialchars($currentUser['name']    ?? 'User');
$userEmail   = htmlspecialchars($currentUser['email']   ?? '');
$userRole    = htmlspecialchars($currentUser['role']    ?? 'Team Member');
$userBio     = htmlspecialchars($currentUser['bio']     ?? '');
$userInitial = strtoupper(($currentUser['name'] ?? 'U')[0]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DayTrack – Profile</title>
  <meta name="description" content="View and manage your DayTrack profile."/>
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
      <div class="flex items-center gap-3">
        <div class="lg:hidden w-10"></div>
        <a href="dashboard.php"
           class="flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          Back
        </a>
        <span class="text-gray-200">/</span>
        <h1 class="text-lg font-bold text-gray-900">Profile</h1>
      </div>
      <a href="settings.php"
         class="flex items-center gap-2 text-sm font-semibold text-gray-500 border border-gray-200 rounded-xl px-3 py-1.5 hover:bg-gray-50 hover:text-gray-800 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Settings
      </a>
    </header>

    <!-- PAGE BODY -->
    <div class="p-6 max-w-2xl mx-auto">

      <!-- ── PROFILE HERO ── -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden fade-in relative z-10">
      
      <div class="h-24 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 relative overflow-hidden z-0">
        <svg class="absolute inset-0 w-full h-full object-cover" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20" preserveAspectRatio="none">
          <circle cx="10" cy="10" r="6" fill="rgba(255,255,255,0.05)"/>
          <circle cx="50" cy="5" r="10" fill="rgba(255,255,255,0.05)"/>
          <circle cx="85" cy="15" r="8" fill="rgba(255,255,255,0.05)"/>
        </svg>
      </div>

      <div class="px-6 pb-6 relative z-10">
        <div class="flex items-end justify-between -mt-10 mb-4 relative z-20">
          <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 border-4 border-white shadow-lg flex items-center justify-center text-white font-extrabold text-2xl flex-shrink-0">
            <?= $userInitial ?>
          </div>
          <button id="btn-edit-bio-trigger"
            class="flex items-center gap-1.5 text-xs font-semibold text-indigo-600 border border-indigo-200 bg-white rounded-xl px-3 py-1.5 hover:bg-indigo-50 transition-all shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Bio
          </button>
        </div>

        <h2 class="text-xl font-extrabold text-gray-900 mb-0.5 relative z-20"><?= $userName ?></h2>
        <p class="text-sm font-medium text-gray-500 mb-1"><?= $userRole ?></p>
        <p class="text-sm text-gray-400 flex items-center gap-1.5 mb-4">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
          <?= $userEmail ?>
        </p>

        <div class="flex flex-wrap gap-2 mb-4">
          <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            Member
          </span>
          <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            Verified
          </span>
          <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
            Pro User
          </span>
        </div>
      </div>
    </div>

      <!-- ── BIO CARD ── -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4 fade-in">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <h3 class="text-sm font-bold text-gray-900">About Me</h3>
          </div>
        </div>

        <!-- Bio text view -->
        <div id="bio-view">
          <p id="bio-text" class="text-sm text-gray-500 leading-relaxed">
            <?= $userBio ?: 'No bio yet. Click Edit Bio to add one.' ?>
          </p>
        </div>

        <!-- Bio edit -->
        <div id="bio-edit" class="hidden mt-2">
          <textarea id="bio-input" rows="3" maxlength="300"
            placeholder="Tell us a little about yourself…"
            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300 transition-all resize-none mb-3"><?= $userBio ?></textarea>
          <div class="flex gap-2">
            <button id="btn-save-bio"
              class="flex-1 bg-indigo-600 text-white font-semibold text-sm rounded-xl py-2 hover:bg-indigo-700 transition-colors">
              Save Bio
            </button>
            <button id="btn-cancel-bio"
              class="px-4 text-sm font-semibold text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
              Cancel
            </button>
          </div>
        </div>
      </div>

      <!-- ── ACCOUNT ACTIONS ── -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 fade-in">
        <div class="flex items-center gap-2 mb-4">
          <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <h3 class="text-sm font-bold text-gray-900">Account</h3>
        </div>
        <div class="space-y-2.5">
          <a href="settings.php"
             class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition-all group">
            <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-sm font-semibold text-gray-700 group-hover:text-indigo-700 transition-colors">Settings</span>
            <svg class="w-4 h-4 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </a>
          <button id="btn-logout"
            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-red-100 bg-red-50 hover:bg-red-500 hover:border-red-500 transition-all group">
            <svg class="w-4 h-4 text-red-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span class="text-sm font-semibold text-red-500 group-hover:text-white transition-colors text-left">Sign Out</span>
          </button>
        </div>
      </div>

    </div>
  </main>
</div>

<script src="../../assets/js/daytrack.js"></script>
<script>
const API = '../../api';

/* ── Bio edit ── */
document.getElementById('btn-edit-bio-trigger').addEventListener('click', () => {
  document.getElementById('bio-view').classList.add('hidden');
  document.getElementById('bio-edit').classList.remove('hidden');
  document.getElementById('btn-edit-bio-trigger').classList.add('hidden');
});
document.getElementById('btn-cancel-bio').addEventListener('click', () => {
  document.getElementById('bio-edit').classList.add('hidden');
  document.getElementById('bio-view').classList.remove('hidden');
  document.getElementById('btn-edit-bio-trigger').classList.remove('hidden');
});
document.getElementById('btn-save-bio').addEventListener('click', () => {
  const bio = document.getElementById('bio-input').value.trim();
  document.getElementById('bio-text').textContent = bio || 'No bio yet. Click Edit Bio to add one.';
  document.getElementById('bio-edit').classList.add('hidden');
  document.getElementById('bio-view').classList.remove('hidden');
  document.getElementById('btn-edit-bio-trigger').classList.remove('hidden');
  showToast('Bio saved');
});

/* ── Logout ── */
document.getElementById('btn-logout').addEventListener('click', async () => {
  await ApiClient.auth.logout();
  window.location.href = 'login.php';
});

</script>
</body>
</html>
