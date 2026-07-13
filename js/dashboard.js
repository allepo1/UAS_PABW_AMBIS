(async function () {
  'use strict';

  const $ = (sel) => document.querySelector(sel);
  const { CATEGORY, escapeHtml, formatDateLong } = AmbisData;

  async function loadDashboardData() {
    try {
      const response = await fetch('api-fetch-dashboard.php');
      const result = await response.json();
      
      if (result.status === 'success') {
        // 1. Set angka statistik utama
        if ($('#statTotalCatatan')) $('#statTotalCatatan').textContent = result.total_catatan;
        if ($('#statTotalRekaman')) $('#statTotalRekaman').textContent = 0; 
        if ($('#statTotalKanvas')) $('#statTotalKanvas').textContent = 0; 

        // 2. Set angka di tiap card Workspace
        if ($('#wsCountA')) $('#wsCountA').textContent = `${result.counts.a} catatan`;
        if ($('#wsCountB')) $('#wsCountB').textContent = `${result.counts.b} catatan`;
        if ($('#wsCountC')) $('#wsCountC').textContent = `${result.counts.c} catatan`;
        if ($('#wsCountD')) $('#wsCountD').textContent = `${result.counts.d} catatan`;

        // 3. Render 3 Catatan Terbaru
        const grid = $('#notesGrid');
        const emptyState = $('#notesEmptyState');
        
        if (grid) {
          if (result.latest_notes.length === 0) {
            grid.innerHTML = '';
            if (emptyState) emptyState.classList.add('show');
          } else {
            if (emptyState) emptyState.classList.remove('show');
            
            grid.innerHTML = result.latest_notes.map(note => {
              const cat = CATEGORY[note.type];
              return `
                <div class="note-card ${cat.topClass}">
                  <div class="note-top"></div>
                  <div class="note-body">
                    <span class="note-tag ${cat.tagClass}">${escapeHtml(cat.label)}</span>
                    <div class="note-title">${escapeHtml(note.title)}</div>
                    <p class="note-preview">${escapeHtml(note.preview)}</p>
                    <div class="note-meta"><i class="fa-regular fa-calendar"></i> ${formatDateLong(note.date)}</div>
                  </div>
                </div>
              `;
            }).join('');
          }
        }

        // NOMOR 3: Mengisi bagian "Aktivitas Terbaru" secara otomatis dari database
        const timelineList = $('#timelineList');
        if (timelineList) {
          if (result.latest_notes.length === 0) {
            timelineList.innerHTML = '<p style="color:#999; font-size:13px; padding:8px 0;">Belum ada aktivitas baru.</p>';
          } else {
            timelineList.innerHTML = result.latest_notes.map(note => {
              return `
                <div class="timeline-item" style="display: flex; gap: 12px; margin-bottom: 12px; font-size: 13px;">
                  <div class="timeline-icon" style="color: #6b3e3e;"><i class="fa-solid fa-circle-dot" style="font-size: 10px; margin-top: 4px;"></i></div>
                  <div class="timeline-content">
                    <span style="font-weight: 600;">Membuat catatan:</span> "${escapeHtml(note.title)}"
                    <div style="color: #999; font-size: 11px;">${formatDateLong(note.date)}</div>
                  </div>
                </div>
              `;
            }).join('');
          }
        }

      }
    } catch (e) {
      console.error("Gagal memuat data dashboard:", e);
    }
  }

  // Jalankan fungsi load data saat halaman dibuka
  loadDashboardData();

  // NOMOR 2: Memperbaiki tombol "Buat Catatan Teks" (Quick Access) agar bisa memicu Modal asli
  const quickCatatan = $('#quickCatatan');
  const noteModal = $('#noteModal'); // ID disesuaikan dengan modal-note.php kamu

  if (quickCatatan && noteModal) {
    quickCatatan.addEventListener('click', (e) => {
      e.preventDefault();
      
      // Reset form bawaan modal terlebih dahulu
      const formCatatan = $('#formCatatan');
      if (formCatatan) formCatatan.reset();
      
      // Atur ID ke 0 (karena ini catatan baru)
      if ($('#noteId')) $('#noteId').value = '0';
      if ($('#modalTitle')) $('#modalTitle').textContent = 'Tambah Catatan Baru';

      // Paksa modal punyamu muncul dengan gaya flexbox
      noteModal.style.setProperty('display', 'flex', 'important');
      noteModal.style.opacity = '1';
      noteModal.style.visibility = 'visible';
    });
  }

})();