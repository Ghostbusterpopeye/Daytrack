/**
 * DayTrack – Shared Utilities v2
 * assets/js/daytrack.js
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
function setGreeting(elId = 'greeting-title', name = 'Rian') {
  const h = new Date().getHours();
  let greet = 'Good Morning';
  if (h >= 12 && h < 17) greet = 'Good Afternoon';
  else if (h >= 17) greet = 'Good Evening';
  const el = document.getElementById(elId);
  if (el) el.textContent = `${greet}, ${name}!`;
}

/* ─── HTML escape helper ─────────────────────────────── */
function escHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

/* ─── Show toast notification ────────────────────────── */
function showToast(message, type = 'success') {
  const existing = document.getElementById('dt-toast');
  if (existing) existing.remove();
  const icons = { success: 'bi-check-circle-fill text-success', danger: 'bi-exclamation-triangle-fill text-danger', info: 'bi-info-circle-fill text-info' };
  const toast = document.createElement('div');
  toast.id = 'dt-toast';
  toast.className = 'position-fixed top-0 start-50 translate-middle-x mt-3 badge bg-dark text-white rounded-pill px-4 py-2 shadow d-flex align-items-center gap-2';
  toast.style.cssText = 'z-index:9999;font-size:.82rem;';
  toast.innerHTML = `<i class="bi ${icons[type] || icons.success}"></i>${escHtml(message)}`;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 2500);
}

/* ─── Confirm dialog helper ──────────────────────────── */
function confirmAction(msg) {
  return window.confirm(msg);
}

/* ─── LocalStorage task store ────────────────────────── */
const TaskStore = {
  KEY: 'daytrack_tasks',
  _nextId: null,
  getAll() {
    try {
      const data = JSON.parse(localStorage.getItem(this.KEY));
      return Array.isArray(data) && data.length ? data : this._seed();
    } catch { return this._seed(); }
  },
  _seed() {
    const defaults = [
      { id: 1001, title: 'Refactor Landing Page Hero', project: 'Marketing Website', done: false, priority: 'high',   due: '', notes: '' },
      { id: 1002, title: 'Sprint Planning Document',   project: 'Management',        done: false, priority: 'medium', due: '', notes: '' },
      { id: 1003, title: 'Team Avatar Review',         project: 'Design System',     done: true,  priority: 'low',    due: '', notes: '' },
      { id: 1004, title: 'Write API Documentation',    project: 'General',           done: false, priority: 'medium', due: '', notes: '' },
      { id: 1005, title: 'Update onboarding flow',     project: 'Design System',     done: false, priority: 'high',   due: '', notes: '' },
    ];
    this.save(defaults);
    return defaults;
  },
  save(tasks) { localStorage.setItem(this.KEY, JSON.stringify(tasks)); },
  nextId() {
    const all = this.getAll();
    return all.length ? Math.max(...all.map(t => t.id)) + 1 : 2000;
  },
  add(task) {
    const all = this.getAll();
    task.id = this.nextId();
    all.push(task);
    this.save(all);
    return task;
  },
  update(id, changes) {
    const all = this.getAll().map(t => t.id === id ? { ...t, ...changes } : t);
    this.save(all);
    return all;
  },
  toggle(id) {
    const all = this.getAll().map(t => t.id === id ? { ...t, done: !t.done } : t);
    this.save(all);
    return all;
  },
  remove(id) {
    const all = this.getAll().filter(t => t.id !== id);
    this.save(all);
    return all;
  }
};

/* ─── LocalStorage project store ────────────────────── */
const ProjectStore = {
  KEY: 'daytrack_projects',
  getAll() {
    try {
      const data = JSON.parse(localStorage.getItem(this.KEY));
      return Array.isArray(data) && data.length ? data : this._seed();
    } catch { return this._seed(); }
  },
  _seed() {
    const defaults = [
      { id: 1, name: 'Design System',  color: 'warning', icon: 'bi-layers',        progress: 80,  label: '80% Complete',  members: 5, archived: false, desc: 'UI component library and design tokens.' },
      { id: 2, name: 'Marketing Web',  color: 'danger',  icon: 'bi-globe',          progress: 25,  label: '3/12 Tasks left', members: 3, archived: false, desc: 'Company marketing site redesign.' },
      { id: 3, name: 'User Analytics', color: 'primary', icon: 'bi-bar-chart-line', progress: 100, label: 'Archived',      members: 2, archived: true,  desc: 'Analytics dashboard (archived).' },
    ];
    this.save(defaults);
    return defaults;
  },
  save(p) { localStorage.setItem(this.KEY, JSON.stringify(p)); },
  nextId() {
    const all = this.getAll();
    return all.length ? Math.max(...all.map(p => p.id)) + 1 : 10;
  },
  add(proj) {
    const all = this.getAll();
    proj.id = this.nextId();
    all.push(proj);
    this.save(all);
    return proj;
  },
  remove(id) {
    const all = this.getAll().filter(p => p.id !== id);
    this.save(all);
    return all;
  },
  update(id, changes) {
    const all = this.getAll().map(p => p.id === id ? { ...p, ...changes } : p);
    this.save(all);
    return all;
  }
};

/* ─── LocalStorage meeting store ─────────────────────── */
const MeetStore = {
  KEY: 'daytrack_meetings',
  getAll() {
    try {
      const data = JSON.parse(localStorage.getItem(this.KEY));
      return Array.isArray(data) && data.length ? data : this._seed();
    } catch { return this._seed(); }
  },
  _seed() {
    const defaults = [
      { id: 1, title: 'Weekly Sprint Sync',  time: '09:00', duration: 30,  members: 6, type: 'standup',  link: '#', notes: 'Review sprint goals.' },
      { id: 2, title: 'Design Review',       time: '11:30', duration: 45,  members: 4, type: 'review',   link: '#', notes: 'Check Figma files.' },
      { id: 3, title: 'Stakeholder Update',  time: '14:00', duration: 60,  members: 8, type: 'update',   link: '#', notes: 'Q3 progress update.' },
      { id: 4, title: '1-on-1 with Manager', time: '16:30', duration: 30,  members: 2, type: 'one-on-one', link: '#', notes: 'Career growth discussion.' },
    ];
    this.save(defaults);
    return defaults;
  },
  save(m) { localStorage.setItem(this.KEY, JSON.stringify(m)); },
  nextId() { const all = this.getAll(); return all.length ? Math.max(...all.map(m => m.id)) + 1 : 10; },
  add(meet) { const all = this.getAll(); meet.id = this.nextId(); all.push(meet); this.save(all); return meet; },
  remove(id) { const all = this.getAll().filter(m => m.id !== id); this.save(all); return all; }
};

/* ─── Auth helpers ───────────────────────────────────── */
function getUser() {
  try { return JSON.parse(localStorage.getItem('daytrack_user')) || { name: 'Rian', email: 'rian@demo.com' }; }
  catch { return { name: 'Rian', email: 'rian@demo.com' }; }
}
function getUserInitial() { const u = getUser(); return (u.name || 'R')[0].toUpperCase(); }

/* ─── Format time 24h → 12h ─────────────────────────── */
function fmt12(time24) {
  if (!time24) return '';
  const [h, m] = time24.split(':').map(Number);
  const ampm = h >= 12 ? 'PM' : 'AM';
  const h12 = h % 12 || 12;
  return `${String(h12).padStart(2,'0')}:${String(m).padStart(2,'0')} ${ampm}`;
}
