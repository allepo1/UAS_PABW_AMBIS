(async function () {
  'use strict';

  const $ = (sel) => document.querySelector(sel);
  const { CATEGORY, escapeHtml, formatDateLong } = AmbisData;

  let allNotes = [];
  let activeFilterCategory = window.AMBIS_INIT?.kategori || '';
  let activeSearchQuery = window.AMBIS_INIT?.cari || '';
  let noteIdToDelete = null;

  // 1. Ambil data dari API & Render ke Grid
  async function fetchAndRenderNotes() {
    try {
      const response = await fetch('api-fetch-notes.php');
      const result = await response.json();
      
      if (result.status === 'success') {
        allNotes = result.notes;
        applyFilterAndSearch();
      }
    } catch (e) {
      console.error("Gagal memuat catatan:", e);
    }
  }

  function applyFilterAndSearch() {
    const grid = $('#notesGrid');
    const emptyState = $('#notesEmptyState');
    const chip = $('#filterChip');
    const chipText = $('#filterChipText');

    if (!grid) return;

    // Filter Kategori, Fitur Cari, DAN pastikan data TIDAK sedang diarsipkan (is_archived == 0)
    let filtered = allNotes.filter(note => {
      const matchesArchive = parseInt(note.is_archived || 0) === 0;
      const matchesCat = activeFilterCategory ? note.type === activeFilterCategory : true;
      const matchesSearch = activeSearchQuery ? (
        note.title.toLowerCase().includes(activeSearchQuery.toLowerCase()) ||
        note.preview.toLowerCase().includes(activeSearchQuery.toLowerCase())
      ) : true;
      return matchesArchive && matchesCat && matchesSearch;
    });

    // Atur Tampilan Filter Chip
    if (activeFilterCategory && CATEGORY[activeFilterCategory]) {
      if (chip) chip.add('show');
      if (chipText) chipText.textContent = `Workspace: ${CATEGORY[activeFilterCategory].label}`;
    } else {
      if (chip) chip.classList.remove('show');
    }

    // Render Kartu ke HTML
    if (filtered.length === 0) {
      grid.innerHTML = '';
      if (emptyState) emptyState.classList.add('show');
    } else {
      if (emptyState) emptyState.classList.remove('show');
      
      grid.innerHTML = filtered.map(note => {
        const cat = CATEGORY[note.type];
        return `
          <div class="note-card ${cat.topClass}">
            <div class="note-top"></div>
            <div class="note-body">
              <span class="note-tag ${cat.tagClass}">${escapeHtml(cat.label)}</span>
              <div class="note-title">${escapeHtml(note.title)}</div>
              <p class="note-preview">${escapeHtml(note.preview)}</p>
              <div class="note-meta"><i class="fa-regular fa-calendar"></i> ${formatDateLong(note.date)}</div>
              <!-- Aksi Kartu Ditambahkan Fitur Arsip -->
              <div class="note-actions" style="display:flex; gap:12px; margin-top:12px; justify-content:flex-end; align-items:center;">
                <button class="btn-archive" data-id="${note.id}" style="background:none; border:none; color:#64748b; font-size:12px; font-weight:600; cursor:pointer;"><i class="fa-solid fa-box-archive"></i> Arsipkan</button>
                <button class="btn-edit" data-id="${note.id}" style="background:none; border:none; color:#1e3a8a; font-size:12px; font-weight:600; cursor:pointer;"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                <button class="btn-delete" data-id="${note.id}" data-title="${escapeHtml(note.title)}" style="background:none; border:none; color:#ef4444; font-size:12px; font-weight:600; cursor:pointer;"><i class="fa-solid fa-trash"></i> Hapus</button>
              </div>
            </div>
          </div>
        `;
      }).join('');

      initCardButtons();
    }
  }

  // 2. Hubungkan Aksi Tombol di Setiap Kartu (Edit, Hapus, dan Tambahan Arsip)
  function initCardButtons() {
    // Aksi Tombol Arsip Baru
    document.querySelectorAll('.btn-archive').forEach(btn => {
      btn.onclick = async function() {
        const id = this.getAttribute('data-id');
        if (!confirm('Apakah kamu yakin ingin memindahkan catatan ini ke halaman Arsip?')) return;
        
        try {
          const response = await fetch('api-archive-item.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type: 'note', id: id })
          });
          const res = await response.json();
          if (res.status === 'success') {
            fetchAndRenderNotes();
          } else {
            alert(res.message);
          }
        } catch (err) {
          alert('Gagal mengarsipkan catatan.');
        }
      };
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
      btn.onclick = function() {
        const id = this.getAttribute('data-id');
        const note = allNotes.find(n => n.id == id);
        if (!note) return;

        $('#noteId').value = note.id;
        $('#noteTitle').value = note.title;
        $('#notePreview').value = note.preview;
        $('#noteDate').value = note.date;
        
        document.querySelectorAll('.cat-option').forEach(opt => opt.classList.remove('active'));
        const targetRadio = $(`input[name="type"][value="${note.type}"]`);
        if (targetRadio) {
          targetRadio.checked = true;
          targetRadio.closest('.cat-option').classList.add('active');
        }

        $('#modalNoteTitle').innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Edit Catatan';
        $('#modalNote').classList.add('show');
      };
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.onclick = function() {
        noteIdToDelete = this.getAttribute('data-id');
        $('#deleteNoteTitle').textContent = this.getAttribute('data-title');
        $('#modalDelete').classList.add('show');
      };
    });
  }

  /* ---------- EVENT HANDLER MODAL & COMPONENT ---------- */
  
  if ($('#btnCatatanBaru')) {
    $('#btnCatatanBaru').onclick = () => {
      $('#formNote').reset();
      $('#noteId').value = '';
      $('#noteDate').value = new Date().toISOString().slice(0, 10);
      document.querySelectorAll('.cat-option').forEach(opt => opt.classList.remove('active'));
      const defRadio = $('input[name="type"][value="a"]');
      if (defRadio) { defRadio.checked = true; defRadio.closest('.cat-option').classList.add('active'); }
      
      $('#modalNoteTitle').innerHTML = '<i class="fa-solid fa-plus"></i> Catatan Baru';
      $('#modalNote').classList.add('show');
    };
  }

  if ($('#closeModalNote')) $('#closeModalNote').onclick = () => $('#modalNote').classList.remove('show');
  if ($('#cancelModalNote')) $('#cancelModalNote').onclick = () => $('#modalNote').classList.remove('show');
  if ($('#cancelModalDelete')) $('#cancelModalDelete').onclick = () => $('#modalDelete').classList.remove('show');

  document.querySelectorAll('.cat-option').forEach(label => {
    label.onclick = function() {
      document.querySelectorAll('.cat-option').forEach(opt => opt.classList.remove('active'));
      this.classList.add('active');
      const radio = this.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    };
  });

  if ($('#searchInput')) {
    $('#searchInput').value = activeSearchQuery;
    $('#searchInput').oninput = function() {
      activeSearchQuery = this.value;
      applyFilterAndSearch();
    };
  }

  if ($('#filterChipClear')) {
    $('#filterChipClear').onclick = () => {
      activeFilterCategory = '';
      applyFilterAndSearch();
    };
  }

  if ($('#formNote')) {
    $('#formNote').onsubmit = async function(e) {
      e.preventDefault();
      const payload = {
        id: $('#noteId').value,
        title: $('#noteTitle').value.trim(),
        type: $('input[name="type"]:checked').value,
        preview: $('#notePreview').value.trim(),
        date: $('#noteDate').value
      };

      try {
        const response = await fetch('api-save-note.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const res = await response.json();
        if (res.status === 'success') {
          $('#modalNote').classList.remove('show');
          fetchAndRenderNotes();
        } else {
          alert(res.message);
        }
      } catch (err) { alert('Gagal menyimpan.'); }
    };
  }

  if ($('#confirmModalDelete')) {
    $('#confirmModalDelete').onclick = async function() {
      if (!noteIdToDelete) return;
      try {
        const response = await fetch('api-delete-note.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: noteIdToDelete })
        });
        const res = await response.json();
        if (res.status === 'success') {
          $('#modalDelete').classList.remove('show');
          fetchAndRenderNotes();
        } else { alert(res.message); }
      } catch (err) { alert('Gagal menghapus.'); }
    };
  }

  fetchAndRenderNotes();

})();