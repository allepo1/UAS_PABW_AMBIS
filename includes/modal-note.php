<div class="modal-overlay" id="noteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
  <div class="modal-box" style="background: #fff; padding: 24px; border-radius: 12px; width: 90%; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
    <div class="modal-head" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
      <h3 id="modalTitle" style="margin: 0; font-family: 'Plus Jakarta Sans', sans-serif;">Tambah Catatan Baru</h3>
      <button type="button" id="btnTutupModal" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #999;">&times;</button>
    </div>
    
    <form id="formCatatan">
      <input type="hidden" id="noteId" value="0">
      
      <div style="margin-bottom: 12px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Judul Catatan</label>
        <input type="text" id="noteTitleInput" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;">
      </div>
      
      <div style="margin-bottom: 12px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Mata Kuliah (Workspace)</label>
        <select id="noteTypeInput" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;">
          <option value="a">Pemrograman Web</option>
          <option value="b">Jaringan Komputer</option>
          <option value="c">Basis Data</option>
          <option value="d">Matematika Komputasi</option>
        </select>
      </div>
      
      <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Isi Singkat Catatan</label>
        <textarea id="notePreviewInput" required rows="4" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; resize: vertical;"></textarea>
      </div>
      
      <div style="display: flex; justify-content: flex-end; gap: 8px;">
        <button type="button" class="btn" id="btnBatalModal" style="padding: 8px 16px; background: #eee; border: none; border-radius: 6px; cursor: pointer;">Batal</button>
        <button type="submit" class="btn btn-primary" style="padding: 8px 16px; background: #6b3e3e; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Simpan Catatan</button>
      </div>
    </form>
  </div>
</div>

<script>
// 1. Fungsi pembuka modal global
function openNoteModal(mode, id = 0) {
  const modal = document.getElementById('noteModal');
  const form = document.getElementById('formCatatan');
  if (!modal || !form) return;
  
  form.reset();
  document.getElementById('noteId').value = id;
  
  if (mode === 'add') {
    document.getElementById('modalTitle').textContent = 'Tambah Catatan Baru';
  }
  modal.style.display = 'flex';
}

// 2. Trik Pamungkas: Langsung tembak tombol dari dalam modal begitu halaman siap
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('noteModal');
  const btnTutup = document.getElementById('btnTutupModal');
  const btnBatal = document.getElementById('btnBatalModal');
  const form = document.getElementById('formCatatan');

  // Pasang pelatuk langsung ke ID tombol utama dashboard di sini!
  const btnCatatanBaruUtama = document.getElementById('btnCatatanBaru');
  if (btnCatatanBaruUtama) {
    btnCatatanBaruUtama.onclick = function(e) {
      e.preventDefault();
      console.log("Tombol Catatan Baru sukses diklik!");
      
      // Paksa modal muncul di layar secara absolut
      const m = document.getElementById('noteModal');
      if (m) {
        m.style.setProperty('display', 'flex', 'important');
        m.style.opacity = '1';
        m.style.visibility = 'visible';
      } else {
        alert("Elemen #noteModal tidak ditemukan di halaman ini!");
      }
    };
  }

  if (btnTutup) btnTutup.onclick = function() { modal.style.display = 'none'; };
  if (btnBatal) btnBatal.onclick = function() { modal.style.display = 'none'; };

  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const dataCatatan = {
        id: parseInt(document.getElementById('noteId').value) || 0,
        title: document.getElementById('noteTitleInput').value,
        type: document.getElementById('noteTypeInput').value,
        preview: document.getElementById('notePreviewInput').value,
        date: new Date().toISOString().split('T')[0]
      };

      try {
        const response = await fetch('api-save-note.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(dataCatatan)
        });

        const result = await response.json();
        if (result.status === 'success') {
          alert(result.message);
          modal.style.display = 'none';
          window.location.reload();
        } else {
          alert(result.message);
        }
      } catch (error) {
        alert('Gagal memproses data.');
      }
    });
  }
});
</script>