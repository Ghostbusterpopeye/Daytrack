<?php
/**
 * DayTrack – Login / Register Page (Tailwind v3 CDN, standalone)
 */
if (session_status() === PHP_SESSION_NONE) session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DayTrack – Sign In</title>
  <meta name="description" content="Sign in or create an account on DayTrack – your daily productivity companion."/>
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
    .fade-in{animation:fadeIn .5s ease both}
    @keyframes fadeIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
    .slide-in{animation:slideIn .4s ease both}
    @keyframes slideIn{from{opacity:0;height:0;transform:scaleY(.95)}to{opacity:1;height:auto;transform:scaleY(1)}}
    .form-input{width:100%;padding:.625rem 1rem;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:.75rem;font-size:.875rem;color:#111827;outline:none;transition:all .15s;}
    .form-input:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,.15);}
    .form-input::placeholder{color:#9ca3af;}
    ::-webkit-scrollbar{width:4px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:#e0e0e0;border-radius:4px}

    /* Decorative task cards */
    .demo-card{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:14px;backdrop-filter:blur(8px);}
  </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased min-h-screen">

<div class="min-h-screen flex">

  <!-- ══ LEFT PANEL (hidden on mobile) ══ -->
  <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] relative flex-col items-center justify-center
              bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 p-12 overflow-hidden">
    <!-- Decorative blobs -->
    <div class="absolute -top-24 -left-24 w-72 h-72 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-purple-400/20 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>

    <div class="relative z-10 max-w-md w-full">
      <!-- Logo -->
      <div class="flex items-center gap-3 mb-10">
        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center border border-white/30">
          <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
        </div>
        <span class="text-2xl font-bold text-white">DayTrack</span>
      </div>

      <!-- Headline -->
      <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">
        Your productivity,<br/>
        <span class="text-indigo-200">beautifully organized.</span>
      </h1>
      <p class="text-indigo-200 text-base leading-relaxed mb-10">
        Track tasks, manage projects, and schedule meetings — all in one elegant workspace.
      </p>

      <!-- Feature pills -->
      <div class="flex flex-wrap gap-2 mb-10">
        <?php foreach(['Task Management','Projects','Meetings','Team Chat'] as $feat): ?>
        <span class="text-xs font-semibold px-3 py-1.5 rounded-full bg-white/15 text-white border border-white/25 backdrop-blur"><?= $feat ?></span>
        <?php endforeach; ?>
      </div>

      <!-- Decorative task card mockups -->
      <div class="space-y-3">
        <div class="demo-card p-4 flex items-center gap-3">
          <div class="w-5 h-5 rounded-md bg-indigo-400 flex items-center justify-center flex-shrink-0">
            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
          </div>
          <span class="text-white text-sm font-medium line-through opacity-60">Finalize Q4 roadmap presentation</span>
          <span class="ml-auto text-xs text-indigo-300 font-semibold">Done</span>
        </div>
        <div class="demo-card p-4 flex items-center gap-3">
          <div class="w-5 h-5 rounded-md border-2 border-white/50 flex-shrink-0"></div>
          <span class="text-white text-sm font-medium">Review pull request #47</span>
          <span class="ml-auto text-xs bg-amber-400/20 text-amber-200 font-semibold px-2 py-0.5 rounded-full">High</span>
        </div>
        <div class="demo-card p-4 flex items-center gap-3">
          <div class="w-5 h-5 rounded-md border-2 border-white/50 flex-shrink-0"></div>
          <span class="text-white text-sm font-medium">Team standup · 09:00 AM</span>
          <span class="ml-auto text-xs bg-green-400/20 text-green-200 font-semibold px-2 py-0.5 rounded-full">Upcoming</span>
        </div>
      </div>

    </div>
  </div>

  <!-- ══ RIGHT PANEL ══ -->
  <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-white">
    <div class="w-full max-w-sm">

      <!-- Mobile logo (only shown on mobile) -->
      <div class="flex items-center justify-center gap-2 mb-8 lg:hidden">
        <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
        </div>
        <span class="text-xl font-bold text-gray-900">DayTrack</span>
      </div>

      <!-- ── LOGIN FORM ── -->
      <div id="login-panel" class="fade-in">
        <h2 class="text-2xl font-extrabold text-gray-900 mb-1">Welcome back</h2>
        <p class="text-sm text-gray-500 mb-6">Sign in to continue to DayTrack</p>

        <!-- Demo hint -->
        <div class="flex items-start gap-2.5 bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-3 mb-5">
          <svg class="w-4 h-4 text-indigo-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
          <p class="text-xs text-indigo-700">Demo: <strong>rian@demo.com</strong> / <strong>password123</strong></p>
        </div>

        <form id="login-form" novalidate>
          <!-- Email -->
          <div class="mb-4">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="login-email">Email Address</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
              <input type="email" id="login-email" placeholder="rian@demo.com" required autocomplete="email"
                class="form-input pl-9"/>
            </div>
          </div>

          <!-- Password -->
          <div class="mb-5">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="login-password">Password</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              <input type="password" id="login-password" placeholder="••••••••" required autocomplete="current-password"
                class="form-input pl-9 pr-10"/>
              <button type="button" id="toggle-login-pw" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                <svg id="eye-login-show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg id="eye-login-hide" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
              </button>
            </div>
          </div>

          <!-- Error -->
          <div id="login-error" class="hidden flex items-center gap-2 text-xs text-red-600 bg-red-50 border border-red-100 rounded-xl px-3 py-2.5 mb-4">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span id="login-error-msg">Invalid credentials.</span>
          </div>

          <button type="submit" id="btn-login"
            class="w-full flex items-center justify-center gap-2 bg-indigo-600 text-white font-semibold text-sm rounded-xl py-2.5 hover:bg-indigo-700 transition-colors mb-5 shadow-sm">
            <span id="btn-login-text">Sign In</span>
            <svg id="btn-login-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
          </button>
        </form>

        <div class="flex items-center gap-3 mb-5">
          <div class="flex-1 h-px bg-gray-100"></div>
          <span class="text-xs text-gray-400 font-medium">or</span>
          <div class="flex-1 h-px bg-gray-100"></div>
        </div>

        <p class="text-center text-sm text-gray-500">
          Don't have an account?
          <button id="btn-show-register" class="text-indigo-600 font-semibold hover:text-indigo-700 transition-colors ml-1">Create one free</button>
        </p>
      </div>

      <!-- ── REGISTER FORM ── -->
      <div id="register-panel" class="hidden fade-in">
        <h2 class="text-2xl font-extrabold text-gray-900 mb-1">Create Account</h2>
        <p class="text-sm text-gray-500 mb-6">Join DayTrack for free — no credit card needed.</p>

        <form id="register-form" novalidate>
          <!-- Name -->
          <div class="mb-4">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="reg-name">Full Name</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              <input type="text" id="reg-name" placeholder="Rian Pratama" required autocomplete="name"
                class="form-input pl-9"/>
            </div>
          </div>

          <!-- Email -->
          <div class="mb-4">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="reg-email">Email Address</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
              <input type="email" id="reg-email" placeholder="you@email.com" required autocomplete="email"
                class="form-input pl-9"/>
            </div>
          </div>

          <!-- Password -->
          <div class="mb-5">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5" for="reg-password">Password</label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              <input type="password" id="reg-password" placeholder="Min. 6 characters" required minlength="6"
                class="form-input pl-9"/>
            </div>
          </div>

          <!-- Error -->
          <div id="reg-error" class="hidden flex items-center gap-2 text-xs text-red-600 bg-red-50 border border-red-100 rounded-xl px-3 py-2.5 mb-4">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span id="reg-error-msg"></span>
          </div>

          <button type="submit" id="btn-register"
            class="w-full flex items-center justify-center gap-2 bg-emerald-600 text-white font-semibold text-sm rounded-xl py-2.5 hover:bg-emerald-700 transition-colors mb-5 shadow-sm">
            <span id="btn-reg-text">Create Account</span>
            <svg id="btn-reg-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
          </button>
        </form>

        <p class="text-center text-sm text-gray-500">
          Already have an account?
          <button id="btn-show-login" class="text-indigo-600 font-semibold hover:text-indigo-700 transition-colors ml-1">Sign in</button>
        </p>
      </div>

    </div>
  </div>
</div>

<script>
  const API = '../../api';

  /* ── Panel toggle ── */
  document.getElementById('btn-show-register').addEventListener('click', () => {
    document.getElementById('login-panel').classList.add('hidden');
    document.getElementById('register-panel').classList.remove('hidden');
  });
  document.getElementById('btn-show-login').addEventListener('click', () => {
    document.getElementById('register-panel').classList.add('hidden');
    document.getElementById('login-panel').classList.remove('hidden');
  });

  /* ── Password visibility ── */
  document.getElementById('toggle-login-pw').addEventListener('click', () => {
    const inp  = document.getElementById('login-password');
    const show = document.getElementById('eye-login-show');
    const hide = document.getElementById('eye-login-hide');
    if (inp.type === 'password') {
      inp.type = 'text';
      show.classList.add('hidden');
      hide.classList.remove('hidden');
    } else {
      inp.type = 'password';
      show.classList.remove('hidden');
      hide.classList.add('hidden');
    }
  });

  /* ── Login ── */
  document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const email    = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;
    const errBox   = document.getElementById('login-error');
    const errMsg   = document.getElementById('login-error-msg');
    const btn      = document.getElementById('btn-login');
    const spinner  = document.getElementById('btn-login-spinner');
    const btnText  = document.getElementById('btn-login-text');

    errBox.classList.add('hidden');
    btn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Signing in…';

    try {
      const res  = await fetch(`${API}/auth.php?action=login`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({email, password})
      });
      const json = await res.json();
      if (json.success) {
        window.location.href = 'dashboard.php';
      } else {
        errBox.classList.remove('hidden');
        errMsg.textContent = json.error || 'Invalid credentials.';
      }
    } catch(err) {
      errBox.classList.remove('hidden');
      errMsg.textContent = 'Network error. Please try again.';
    } finally {
      btn.disabled = false;
      spinner.classList.add('hidden');
      btnText.textContent = 'Sign In';
    }
  });

  /* ── Register ── */
  document.getElementById('register-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const name     = document.getElementById('reg-name').value.trim();
    const email    = document.getElementById('reg-email').value.trim();
    const password = document.getElementById('reg-password').value;
    const errBox   = document.getElementById('reg-error');
    const errMsg   = document.getElementById('reg-error-msg');
    const btn      = document.getElementById('btn-register');
    const spinner  = document.getElementById('btn-reg-spinner');
    const btnText  = document.getElementById('btn-reg-text');

    errBox.classList.add('hidden');
    btn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Creating…';

    try {
      const res  = await fetch(`${API}/auth.php?action=register`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        credentials: 'same-origin',
        body: JSON.stringify({name, email, password})
      });
      const json = await res.json();
      if (json.success) {
        window.location.href = 'dashboard.php';
      } else {
        errBox.classList.remove('hidden');
        errMsg.textContent = json.error || 'Registration failed.';
      }
    } catch(err) {
      errBox.classList.remove('hidden');
      errMsg.textContent = 'Network error. Please try again.';
    } finally {
      btn.disabled = false;
      spinner.classList.add('hidden');
      btnText.textContent = 'Create Account';
    }
  });
</script>
</body>
</html>
