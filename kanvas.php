<?php
$pageTitle = 'Kanvas Digital';
$activePage = 'kanvas';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main">
  <div class="topbar">
    <div class="greeting">
      <h1><i class="fa-solid fa-pen-nib" style="color:var(--navy); margin-right:8px;"></i>Kanvas Digital</h1>
      <p class="page-subtitle">Coret-coret ide, gambar diagram, atau sketching materi kuliahmu.</p>
    </div>
  </div>

  <section class="section" style="display: flex; flex-direction: column; gap: 20px;">
    <!-- KOTAK KONTROL & AREA KANVAS -->
    <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
      <!-- Toolbar Atas -->
      <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 16px; justify-content: space-between;">
        <div style="display: flex; gap: 12px; align-items: center;">
          <input type="text" id="sketchTitle" placeholder="Nama gambar/diagram..." style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none; width: 200px;">
          
          <label style="font-size: 13px; font-weight: 600; color: #475569;">Warna:</label>
          <input type="color" id="brushColor" value="#1e293b" style="border: none; width: 32px; height: 32px; cursor: pointer; background: transparent;">
          
          <label style="font-size: 13px; font-weight: 600; color: #475569;">Ukuran:</label>
          <input type="range" id="brushSize" min="1" max="20" value="5" style="width: 100px; cursor: pointer;">
        </div>
        
        <div style="display: flex; gap: 8px;">
          <button id="btnClearCanvas" style="padding: 8px 16px; background: #eee; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;"><i class="fa-solid fa-trash-can"></i> Bersihkan</button>
          <button id="btnSaveCanvas" style="padding: 8px 16px; background: #6b3e3e; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;"><i class="fa-solid fa-floppy-disk"></i> Simpan Gambar</button>
        </div>
      </div>

      <!-- Area Gambar Canvas -->
      <div style="border: 2px dashed #cbd5e1; border-radius: 8px; background: #fafafa; overflow: hidden; position: relative;">
        <canvas id="paintCanvas" width="800" height="450" style="display: block; cursor: crosshair; width: 100%; height: auto;"></canvas>
      </div>
    </div>

    <!-- GALLERY HASIL KANVAS -->
    <h3><i class="fa-solid fa-images" style="margin-right: 8px;"></i>Galeri Diagram Kuliah</h3>
    <div id="sketchesGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
      <!-- Diisi via JavaScript -->
    </div>

    <div class="empty-state" id="sketchesEmptyState" style="display: none; text-align: center; padding: 40px 20px;">
      <i class="fa-regular fa-image" style="font-size: 48px; color: #cbd5e1; margin-bottom: 12px;"></i>
      <strong style="display: block;">Belum ada gambar/diagram</strong>
      <span style="color: #94a3b8; font-size: 14px;">Hasil gambarmu akan tersimpan di sini.</span>
    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const canvas = document.getElementById('paintCanvas');
  const ctx = canvas.getContext('2d');
  const colorInput = document.getElementById('brushColor');
  const sizeInput = document.getElementById('brushSize');
  const btnClear = document.getElementById('btnClearCanvas');
  const btnSave = document.getElementById('btnSaveCanvas');
  const titleInput = document.getElementById('sketchTitle');
  const grid = document.getElementById('sketchesGrid');
  const emptyState = document.getElementById('sketchesEmptyState');

  let isDrawing = false;

  // Set background awal kanvas menjadi putih agar saat disimpan tidak transparan hitam
  function resetCanvasBackground() {
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
  }
  resetCanvasBackground();

  // Logika Menggambar di Kanvas (Mouse)
  canvas.addEventListener('mousedown', (e) => {
    isDrawing = true;
    ctx.beginPath();
    ctx.moveTo(e.offsetX, e.offsetY);
  });

  canvas.addEventListener('mousemove', (e) => {
    if (!isDrawing) return;
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.strokeStyle = colorInput.value;
    ctx.lineWidth = sizeInput.value;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.stroke();
  });

  canvas.addEventListener('mouseup', () => isDrawing = false);
  canvas.addEventListener('mouseleave', () => isDrawing = false);

  // Tombol Bersihkan Papan
  btnClear.addEventListener('click', () => {
    if (confirm('Bersihkan seluruh papan kanvas?')) {
      resetCanvasBackground();
    }
  });

  // Load Galeri Gambar dari Database (Diperbarui dengan tombol Download & Edit)
  async function loadSketches() {
    try {
      const res = await fetch('api-fetch-sketches.php');
      const data = await res.json();
      
      if (data.status === 'success' && data.sketches.length > 0) {
        emptyState.style.display = 'none';
        grid.style.display = 'grid';
        grid.innerHTML = data.sketches.map(sk => `
          <div style="background: #fff; padding: 12px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 8px;">
            <img src="${sk.image_path}" style="width: 100%; height: 160px; object-fit: contain; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
            <div>
              <strong style="font-size: 14px; display: block; color: #1e293b;">${sk.title}</strong>
              <span style="font-size: 11px; color: #94a3b8;"><i class="fa-regular fa-calendar"></i> ${sk.date}</span>
            </div>
            <!-- Tombol Aksi Baru yang Lebih Lengkap -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px; border-top: 1px solid #f1f5f9; padding-top: 8px;">
              <div style="display: flex; gap: 8px;">
                <!-- 1. Fitur Download Langsung -->
                <a href="${sk.image_path}" download="${sk.title}.png" style="text-decoration: none; color: #0284c7; font-size: 12px; font-weight: 600;"><i class="fa-solid fa-download"></i> Unduh</a>
                <!-- 2. Fitur Buka Kembali di Canvas (Edit) -->
                <button onclick="editSketch('${sk.image_path}', '${sk.title}')" style="background: none; border: none; color: #0d9488; font-size: 12px; font-weight: 600; cursor: pointer; padding: 0;"><i class="fa-solid fa-pen"></i> Edit</button>
              </div>
              <!-- 3. Fitur Hapus -->
              <button onclick="deleteRecording(${sk.id})" style="background: none; border: none; color: #ef4444; font-size: 12px; font-weight: 600; cursor: pointer; padding: 0;"><i class="fa-solid fa-trash"></i> Hapus</button>
            </div>
          </div>
        `).join('');
      } else {
        grid.style.display = 'none';
        emptyState.style.display = 'block';
      }
    } catch (e) { console.error(e); }
  }

  // FUNGSI BARU: Memuat kembali gambar lama ke papan tulis canvas untuk diedit ulang
  window.editSketch = function(imagePath, title) {
    if (confirm(`Muat kembali diagram "${title}" ke papan tulis untuk diedit? Coretan saat ini di papan akan hilang.`)) {
      const img = new Image();
      img.src = imagePath;
      img.onload = function() {
        resetCanvasBackground(); // bersihkan dulu papan lama
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height); // cetak gambar lama ke canvas
        titleInput.value = title + " (Edit)"; // set judul otomatis agar tahu ini editan
        window.scrollTo({ top: 0, behavior: 'smooth' }); // scroll otomatis ke atas papan tulis
      };
    }
  };
  // Aksi Simpan Gambar Ke Server & Database
  btnSave.addEventListener('click', async () => {
    const title = titleInput.value.trim();
    if (!title) { alert('Masukkan judul gambar/diagram dulu!'); return; }

    // Ubah coretan kanvas menjadi string data URL Base64 Gambar
    const imageData = canvas.toDataURL('image/png');

    try {
      const response = await fetch('api-save-sketch.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title, image: imageData })
      });
      const result = await response.json();

      if (result.status === 'success') {
        alert('Gambar kanvas berhasil disimpan ke galeri!');
        titleInput.value = '';
        resetCanvasBackground();
        loadSketches();
      } else {
        alert(result.message);
      }
    } catch (err) { alert('Gagal menyimpan gambar kanvas.'); }
  });

  // Aksi Hapus Gambar
  window.deleteSketch = async function(id) {
    if (!confirm('Hapus gambar ini dari galeri?')) return;
    try {
      const res = await fetch('api-delete-sketch.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const result = await res.json();
      if (result.status === 'success') { loadSketches(); } else { alert(result.message); }
    } catch (e) { alert('Gagal menghapus gambar.'); }
  };

  loadSketches();
});
</script>

<?php include 'includes/footer.php'; ?>