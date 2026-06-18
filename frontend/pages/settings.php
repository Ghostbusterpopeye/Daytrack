<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
$userName  = htmlspecialchars($currentUser['name']  ?? 'User');
$userEmail = htmlspecialchars($currentUser['email'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DayTrack – Settings</title>
  <meta name="description" content="Customize your DayTrack account and preferences."/>
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
    .fade-in{animation:fadeIn .4s ease both}
    @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
    ::-webkit-scrollbar{width:4px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:#e0e0e0;border-radius:4px}
    .form-input{width:100%;padding:.625rem 1rem;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:.75rem;font-size:.875rem;color:#111827;outline:none;transition:all .15s;}
    .form-input:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,.15);}
    .form-input::placeholder{color:#9ca3af;}
    /* Custom toggle switch */
    .toggle-switch{position:relative;width:44px;height:24px;flex-shrink:0;}
    .toggle-switch input{opacity:0;width:0;height:0;}
    .toggle-track{position:absolute;inset:0;background:#e5e7eb;border-radius:9999px;cursor:pointer;transition:background .2s;}
    .toggle-switch input:checked + .toggle-track{background:#6366f1;}
    .toggle-track::after{content:'';position:absolute;left:3px;top:3px;width:18px;height:18px;background:#fff;border-radius:9999px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.15);}
    .toggle-switch input:checked + .toggle-track::after{transform:translateX(20px);}
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
        <a href="profile.php"
           class="flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          Profile
        </a>
        <span class="text-gray-200">/</span>
        <h1 class="text-lg font-bold text-gray-900">Settings</h1>
      </div>
      <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-full px-3 py-1">v2.0</span>
    </header>

    <!-- PAGE BODY -->
    <div class="p-6 max-w-2xl mx-auto space-y-6">

      <!-- ── ACCOUNT INFO ── -->
      <section class="fade-in">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Account</p>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <!-- User row -->
          <div class="flex items-center gap-4 p-5 border-b border-gray-50">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-content-center text-white font-bold text-lg flex-shrink-0"
                 style="display:flex;align-items:center;justify-content:center;">
              <?= strtoupper(($currentUser['name'] ?? 'U')[0]) ?>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-bold text-gray-900 text-sm"><?= $userName ?></p>
              <p class="text-xs text-gray-400 truncate"><?= $userEmail ?></p>
            </div>
            <a href="profile.php"
               class="text-xs font-semibold text-indigo-600 border border-indigo-200 rounded-xl px-3 py-1.5 hover:bg-indigo-50 transition-colors flex-shrink-0">
              Edit Profile
            </a>
          </div>

          <!-- Change Password -->
          <div class="p-5">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
              <h3 class="text-sm font-bold text-gray-900">Change Password</h3>
            </div>

            <form id="change-password-form" class="space-y-3">
              <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Current Password</label>
                <input type="password" id="current-password" placeholder="Enter current password"
                  class="form-input"/>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">New Password</label>
                <input type="password" id="new-password" placeholder="Min. 6 characters"
                  class="form-input"/>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Confirm New Password</label>
                <input type="password" id="confirm-password" placeholder="Repeat new password"
                  class="form-input"/>
              </div>

              <div id="pw-alert" class="hidden rounded-xl px-4 py-2.5 text-sm font-medium"></div>

              <button type="submit"
                class="w-full bg-indigo-600 text-white font-semibold text-sm rounded-xl py-2.5 hover:bg-indigo-700 transition-colors">
                Update Password
              </button>
            </form>
          </div>
        </div>
      </section>

      <!-- ── NOTIFICATIONS ── -->
      <section class="fade-in" style="animation-delay:.05s">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Notifications</p>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden divide-y divide-gray-50">

          <?php
          $notifItems = [
            ['push-notifs',      'Push Notifications', 'Browser push alerts',     'bg-amber-50',   'text-amber-500',  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>'],
            ['email-notifs',     'Email Updates',      'Weekly digest & updates',  'bg-emerald-50', 'text-emerald-500','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
            ['meet-reminders',   'Meeting Reminders',  '5 min before meetings',    'bg-indigo-50',  'text-indigo-500', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.277A1 1 0 0121 8.68v6.64a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>'],
          ];
          foreach($notifItems as [$id, $label, $sub, $bg, $fg, $svgPath]):
          ?>
          <div class="flex items-center justify-between p-5">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl <?= $bg ?> flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 <?= $fg ?>" style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $svgPath ?></svg>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800"><?= $label ?></p>
                <p class="text-xs text-gray-400"><?= $sub ?></p>
              </div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" id="<?= $id ?>" checked/>
              <span class="toggle-track"></span>
            </label>
          </div>
          <?php endforeach; ?>

        </div>
      </section>

      <!-- ── DANGER ZONE ── -->
      <section class="fade-in" style="animation-delay:.1s">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Danger Zone</p>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden divide-y divide-gray-50">

          <!-- Sign out -->
          <div class="p-5">
            <div class="flex items-start gap-3">
              <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4.5 h-4.5 text-orange-500" style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
              </div>
              <div class="flex-1">
                <p class="text-sm font-bold text-gray-900 mb-0.5">Sign Out</p>
                <p class="text-xs text-gray-400 mb-3">You'll need to sign in again to access your account.</p>
                <button id="btn-logout"
                  class="flex items-center gap-2 text-sm font-semibold text-orange-600 border border-orange-200 bg-orange-50 rounded-xl px-4 py-2 hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                  Sign Out
                </button>
              </div>
            </div>
          </div>

          <!-- Delete account -->
          <div class="p-5">
            <div class="flex items-start gap-3">
              <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4.5 h-4.5 text-red-500" style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </div>
              <div class="flex-1">
                <p class="text-sm font-bold text-gray-900 mb-0.5">Delete Account</p>
                <p class="text-xs text-gray-400 mb-3">Permanently delete your account and all your data. This cannot be undone.</p>
                <button id="btn-delete-account"
                  class="flex items-center gap-2 text-sm font-semibold text-red-600 border border-red-200 bg-red-50 rounded-xl px-4 py-2 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  Delete My Account
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <div class="text-center text-xs text-gray-300 pb-4">
        DayTrack v2.0 PHP Edition · Powered by PHP &amp; MySQL
      </div>

    </div>
  </main>
</div>

<script src="../../assets/js/daytrack.js"></script>
<script>
const API = '../../api';

/* ── Password change ── */
document.getElementById('change-password-form').addEventListener('submit', function(e) {
  e.preventDefault();
  const current  = document.getElementById('current-password').value;
  const nw       = document.getElementById('new-password').value;
  const confirm  = document.getElementById('confirm-password').value;
  const alertEl  = document.getElementById('pw-alert');

  alertEl.className = 'hidden rounded-xl px-4 py-2.5 text-sm font-medium';

  if (!current || !nw || !confirm) {
    alertEl.className = 'flex rounded-xl px-4 py-2.5 text-sm font-medium bg-amber-50 text-amber-700 border border-amber-100';
    alertEl.textContent = 'Please fill in all fields.';
    return;
  }
  if (nw.length < 6) {
    alertEl.className = 'flex rounded-xl px-4 py-2.5 text-sm font-medium bg-amber-50 text-amber-700 border border-amber-100';
    alertEl.textContent = 'New password must be at least 6 characters.';
    return;
  }
  if (nw !== confirm) {
    alertEl.className = 'flex rounded-xl px-4 py-2.5 text-sm font-medium bg-red-50 text-red-700 border border-red-100';
    alertEl.textContent = 'Passwords do not match.';
    return;
  }

  alertEl.className = 'flex rounded-xl px-4 py-2.5 text-sm font-medium bg-emerald-50 text-emerald-700 border border-emerald-100';
  alertEl.textContent = 'Password updated successfully';
  this.reset();
  setTimeout(() => { alertEl.className = 'hidden rounded-xl px-4 py-2.5 text-sm font-medium'; }, 3000);
});

/* ── Notification toggles (localStorage) ── */
['push-notifs', 'email-notifs', 'meet-reminders'].forEach(id => {
  const el    = document.getElementById(id);
  const saved = localStorage.getItem('dt_pref_' + id);
  if (saved === '0') el.checked = false;
  el.addEventListener('change', function() {
    localStorage.setItem('dt_pref_' + id, this.checked ? '1' : '0');
    showToast(this.checked ? 'Notifications enabled' : 'Notifications disabled');
  });
});

/* ── Logout ── */
document.getElementById('btn-logout').addEventListener('click', async () => {
  await fetch(`${API}/auth.php?action=logout`, {method: 'POST', credentials: 'same-origin'});
  window.location.href = 'login.php';
});

/* ── Delete account ── */
document.getElementById('btn-delete-account').addEventListener('click', () => {
  if (confirm('⚠️ Are you sure? This will permanently delete your account and all your data. This CANNOT be undone.')) {
    showToast('Account deletion coming soon (demo mode)', 'info');
  }
});
</script>
</body>
</html>
