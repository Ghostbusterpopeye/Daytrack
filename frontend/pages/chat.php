<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/auth_check.php';
$userName    = htmlspecialchars($currentUser['name'] ?? 'User');
$userInitial = strtoupper($userName[0] ?? 'U');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DayTrack – Messages</title>
  <meta name="description" content="Team chat and messages on DayTrack."/>
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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    .skeleton{background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:800px 100%;animation:shimmer 1.4s infinite linear;border-radius:8px;}
    @keyframes shimmer{0%{background-position:-400px 0}100%{background-position:400px 0}}
    .fade-in{animation:fadeIn .4s ease both}
    @keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
    ::-webkit-scrollbar{width:4px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:#e0e0e0;border-radius:4px}
    /* Hide scrollbar for clean chat area */
    #chat-messages::-webkit-scrollbar{display:none;}
    #chat-messages{-ms-overflow-style:none;scrollbar-width:none;}
  </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased overflow-hidden">

<div class="flex h-screen">

  <!-- SIDEBAR -->
  <?php include __DIR__ . '/../components/navbar.php'; ?>

  <!-- MAIN CONTENT -->
  <main class="flex-1 lg:ml-64 h-screen flex flex-col relative bg-white lg:bg-transparent">
    
    <!-- TOP BAR -->
    <header class="flex-shrink-0 sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="lg:hidden w-10"></div>
        <div>
          <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Workspace</p>
          <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            Team Chat
            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
              Live
            </span>
          </h1>
        </div>
      </div>
      <div class="flex -space-x-2">
        <div class="w-8 h-8 rounded-full bg-indigo-200 border-2 border-white flex items-center justify-center text-xs font-bold text-indigo-700">A</div>
        <div class="w-8 h-8 rounded-full bg-purple-200 border-2 border-white flex items-center justify-center text-xs font-bold text-purple-700">B</div>
        <div class="w-8 h-8 rounded-full bg-pink-200 border-2 border-white flex items-center justify-center text-xs font-bold text-pink-700">C</div>
      </div>
    </header>

    <!-- PAGE BODY (CHAT AREA) -->
    <div class="flex-1 flex justify-center h-full overflow-hidden">
      <!-- Chat Container -->
      <div class="w-full max-w-4xl flex flex-col bg-white lg:border-x border-gray-100 h-full relative shadow-sm">
        
        <!-- Messages Area -->
        <div id="chat-messages" class="flex-1 overflow-y-auto px-4 py-6 space-y-6 pb-32">
          <!-- Loading skeletons -->
          <div class="flex flex-col gap-6">
            <div class="flex items-end gap-3">
              <div class="skeleton w-8 h-8 rounded-full flex-shrink-0"></div>
              <div class="skeleton w-64 h-12 rounded-2xl rounded-bl-none"></div>
            </div>
            <div class="flex items-end gap-3 flex-row-reverse">
              <div class="skeleton w-8 h-8 rounded-full flex-shrink-0"></div>
              <div class="skeleton w-48 h-10 rounded-2xl rounded-br-none"></div>
            </div>
          </div>
        </div>

        <!-- Input Area -->
        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-white via-white to-transparent">
          <div class="max-w-3xl mx-auto">
            <div class="flex items-end gap-2 bg-gray-50 border border-gray-200 rounded-2xl p-2 shadow-sm focus-within:ring-2 focus-within:ring-indigo-100 focus-within:border-indigo-300 transition-all">
              <textarea id="msg-input" rows="1" placeholder="Type your message..."
                class="flex-1 max-h-32 bg-transparent text-sm text-gray-800 placeholder-gray-400 outline-none resize-none px-3 py-2"
              ></textarea>
              <button id="btn-send" class="w-10 h-10 flex-shrink-0 bg-indigo-600 text-white rounded-xl flex items-center justify-center hover:bg-indigo-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19V5m0 0l-7 7m7-7l7 7"/></svg>
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>
</div>

<script src="../../assets/js/daytrack.js"></script>
<script>
  const API         = '../../api';
  const SENDER_NAME = <?= json_encode($currentUser['name']) ?>;
  const SENDER_INIT = <?= json_encode($userInitial) ?>;
  let allMessages   = [];

  function colorFor(name) {
    const colors = ['#6366f1', '#10b981', '#f43f5e', '#f59e0b', '#06b6d4', '#8b5cf6'];
    let hash = 0;
    for(let i=0;i<name.length;i++) hash = name.charCodeAt(i) + ((hash<<5)-hash);
    return colors[Math.abs(hash) % colors.length];
  }

  function timeAgo(iso) {
    const d = new Date(iso);
    const diff = Math.floor((Date.now() - d) / 1000);
    if(diff < 60) return 'Just now';
    if(diff < 3600) return Math.floor(diff/60)+'m ago';
    if(diff < 86400) return Math.floor(diff/3600)+'h ago';
    return d.toLocaleDateString();
  }

  async function loadMessages() {
    const data = await ApiClient.messages.getAll();
    allMessages = data || [];
    renderMessages();
  }

  function renderMessages() {
    const container = document.getElementById('chat-messages');
    if (!allMessages.length) {
      container.innerHTML = `
        <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-60 pb-10">
          <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          <p class="text-sm font-medium">No messages yet. Say hello!</p>
        </div>`;
      return;
    }

    container.innerHTML = allMessages.map((m, i) => {
      const isSelf = m.sender_name === SENDER_NAME;
      const initial= (m.sender_name||'?')[0].toUpperCase();
      const col    = colorFor(m.sender_name);
      const isLast = i === allMessages.length - 1;

      if (isSelf) {
        return `
          <div class="flex items-end gap-2.5 flex-row-reverse fade-in" ${isLast?'id="last-msg"':''}>
            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-[11px] flex-shrink-0 shadow-sm">
              ${SENDER_INIT}
            </div>
            <div class="max-w-[75%]">
              <div class="bg-indigo-600 text-white px-4 py-2.5 rounded-2xl rounded-br-none shadow-sm text-sm whitespace-pre-wrap leading-relaxed">${escHtml(m.body)}</div>
              <p class="text-[10px] text-gray-400 text-right mt-1 font-medium">${timeAgo(m.created_at)}</p>
            </div>
          </div>`;
      } else {
        return `
          <div class="flex items-end gap-2.5 fade-in" ${isLast?'id="last-msg"':''}>
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-[11px] flex-shrink-0 shadow-sm" style="background:${col};">
              ${escHtml(initial)}
            </div>
            <div class="max-w-[75%]">
              <p class="text-[10px] text-gray-500 font-semibold mb-1 ml-1">${escHtml(m.sender_name)}</p>
              <div class="bg-gray-100 text-gray-800 px-4 py-2.5 rounded-2xl rounded-bl-none text-sm whitespace-pre-wrap leading-relaxed">${escHtml(m.body)}</div>
              <p class="text-[10px] text-gray-400 mt-1 font-medium ml-1">${timeAgo(m.created_at)}</p>
            </div>
          </div>`;
      }
    }).join('');
    
    // Auto scroll to bottom
    const lastMsg = document.getElementById('last-msg');
    if (lastMsg) lastMsg.scrollIntoView({ behavior: 'smooth', block: 'end' });
  }

  async function sendMessage() {
    const input = document.getElementById('msg-input');
    const body  = input.value.trim();
    if(!body) return;
    
    // Optimistic UI update
    const tempMsg = { id: 'temp', sender_name: SENDER_NAME, body, created_at: new Date().toISOString() };
    allMessages.push(tempMsg);
    renderMessages();
    
    input.value = '';
    input.style.height = 'auto';

    const saved = await ApiClient.messages.create({ body });
    if (saved) {
      allMessages = allMessages.filter(m => m.id !== 'temp');
      allMessages.push(saved);
      renderMessages();
    }
  }

  // Events
  document.getElementById('btn-send').addEventListener('click', sendMessage);
  
  const inputEl = document.getElementById('msg-input');
  inputEl.addEventListener('keydown', e => {
    if(e.key === 'Enter' && !e.shiftKey) { 
      e.preventDefault(); 
      sendMessage(); 
    }
  });
  inputEl.addEventListener('input', function() {
    this.style.height = 'auto'; 
    this.style.height = Math.min(this.scrollHeight, 128) + 'px';
  });

  loadMessages();
</script>
</body>
</html>
