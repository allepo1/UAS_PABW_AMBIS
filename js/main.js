/* =========================================================
   AMBIS — PERILAKU UI BERSAMA
   Toast, dropdown notifikasi, sidebar mobile, ripple effect,
   dan badge jumlah catatan di sidebar. Jalan di semua halaman.
   ========================================================= */
(function () {
  'use strict';

  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));

  const state = AmbisData.loadState();

  /* ---------- Badge jumlah catatan di sidebar ---------- */
  const badge = $('#navBadgeCatatan');
 
  /* ---------- TOAST ---------- */
  function toast(message, type) {
    type = type || 'info';
    const wrap = $('#toastWrap');
    if (!wrap) return;
    const icons = { success: 'fa-circle-check', info: 'fa-circle-info', danger: 'fa-triangle-exclamation' };
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `<div class="toast-icon"><i class="fa-solid ${icons[type] || icons.info}"></i></div><span>${AmbisData.escapeHtml(message)}</span>`;
    wrap.appendChild(el);
    setTimeout(() => {
      el.classList.add('leaving');
      setTimeout(() => el.remove(), 250);
    }, 3000);
  }
  window.toast = toast;

  /* ---------- SIDEBAR MOBILE (drawer) ---------- */
  const sidebar = $('#sidebar');
  const overlay = $('#sidebarOverlay');
  const mobileToggle = $('#mobileToggle');
  if (mobileToggle && sidebar && overlay) {
    mobileToggle.addEventListener('click', () => {
      sidebar.classList.add('open');
      overlay.classList.add('show');
    });
    overlay.addEventListener('click', closeMobileSidebar);
    $$('.nav-item', sidebar).forEach(a => a.addEventListener('click', closeMobileSidebar));
  }
  function closeMobileSidebar() {
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('show');
  }

  /* ---------- NOTIFIKASI ---------- */
  const sampleNotifications = [
    { icon: 'fa-bell', text: 'Pengingat: UTS Basis Data minggu depan', time: '1 jam yang lalu' },
    { icon: 'fa-microphone', text: 'Rekaman "PBO Pertemuan 5" tersimpan', time: 'Kemarin' },
    { icon: 'fa-cloud-arrow-up', text: 'Semua catatan berhasil disinkronkan', time: '2 hari yang lalu' },
  ];

  const notifList = $('#notifList');
  if (notifList) {
    notifList.innerHTML = sampleNotifications.map(n => `
      <div class="notif-item">
        <div class="notif-icon"><i class="fa-solid ${n.icon}"></i></div>
        <div class="notif-text">
          <p>${AmbisData.escapeHtml(n.text)}</p>
          <span>${AmbisData.escapeHtml(n.time)}</span>
        </div>
      </div>
    `).join('') || `<div class="notif-empty">Tidak ada notifikasi baru.</div>`;
  }
  const notifDot = $('#notifDot');
  if (notifDot) notifDot.style.display = state.notifRead ? 'none' : 'block';

  const notifPanel = $('#notifPanel');
  const btnNotif = $('#btnNotif');
  if (btnNotif && notifPanel) {
    btnNotif.addEventListener('click', (e) => {
      e.stopPropagation();
      notifPanel.classList.toggle('show');
    });
    document.addEventListener('click', (e) => {
      if (!notifPanel.contains(e.target) && e.target.id !== 'btnNotif' && !btnNotif.contains(e.target)) {
        notifPanel.classList.remove('show');
      }
    });
  }
  const btnTandaiDibaca = $('#btnTandaiDibaca');
  if (btnTandaiDibaca) {
    btnTandaiDibaca.addEventListener('click', () => {
      state.notifRead = true;
      AmbisData.saveState(state);
      if (notifDot) notifDot.style.display = 'none';
      toast('Semua notifikasi ditandai sudah dibaca', 'success');
    });
  }

  /* ---------- ESC KEY: tutup modal / panel ---------- */
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      $$('.modal-overlay.show').forEach(m => m.classList.remove('show'));
      if (notifPanel) notifPanel.classList.remove('show');
      closeMobileSidebar();
    }
  });

  /* ---------- RIPPLE EFFECT ---------- */
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-primary, .quick-btn, .ws-open-btn, .rec-btn');
    if (!btn) return;
    const rect = btn.getBoundingClientRect();
    const ripple = document.createElement('span');
    const size = Math.max(rect.width, rect.height);
    ripple.style.cssText = `
      position:absolute;
      width:${size}px; height:${size}px;
      left:${e.clientX - rect.left - size / 2}px; top:${e.clientY - rect.top - size / 2}px;
      background:rgba(255,255,255,0.35);
      border-radius:50%;
      pointer-events:none;
      transform:scale(0);
      animation:ripple .5s ease-out;
    `;
    btn.style.position = 'relative';
    btn.style.overflow = 'hidden';
    btn.appendChild(ripple);
    setTimeout(() => ripple.remove(), 500);
  });

  const styleSheet = document.createElement('style');
  styleSheet.textContent = `@keyframes ripple{to{transform:scale(2.2); opacity:0;}}`;
  document.head.appendChild(styleSheet);
})();
