/* =========================================================
AMBIS — KANVAS DIGITAL (VERSI CONNECT DATABASE)
   ========================================================= */
(async function () { // 1. DIUBAH: Menambahkan kata kunci 'async' di fungsi utama
  'use strict';
  const $ = sel => document.querySelector(sel);
  const $$ = sel => Array.from(document.querySelectorAll(sel));
  const { escapeHtml, formatDateLong } = AmbisData;
  
  // 2. DIUBAH: Mengambil data kanvas langsung dari database MySQL via API PHP
  let state = await AmbisData.loadState();

  const canvas = $('#drawCanvas');
  const wrap = canvas.parentElement;
  const ctx = canvas.getContext('2d');

  let currentColor = '#2B2018';
  let currentSize = 4;
  let tool = 'pen'; // 'pen' | 'eraser'
  let strokes = [];      // stroke tersimpan (untuk redraw & undo)
  let currentStroke = null;
  let drawing = false;

  /* ---------- Resize responsif (KODE FRONT-END UTUH) ---------- */
  function resizeCanvas() {
    const rect = wrap.getBoundingClientRect();
    const dpr = window.devicePixelRatio || 1;
    canvas.width = Math.max(1, Math.round(rect.width * dpr));
    canvas.height = Math.max(1, Math.round(rect.height * dpr));
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    redrawAll();
  }

  function pointToPixel(pt) {
    const rect = wrap.getBoundingClientRect();
    return { x: pt.x * rect.width, y: pt.y * rect.height };
  }

  function drawStroke(stroke) {
    if (stroke.points.length === 0) return;
    ctx.lineJoin = 'round';
    ctx.lineCap = 'round';
    ctx.strokeStyle = stroke.eraser ? '#FFFFFF' : stroke.color;
    ctx.lineWidth = stroke.size;
    ctx.beginPath();
    const p0 = pointToPixel(stroke.points[0]);
    ctx.moveTo(p0.x, p0.y);
    if (stroke.points.length === 1) {
      ctx.lineTo(p0.x + 0.1, p0.y + 0.1);
    } else {
      for (let i = 1; i < stroke.points.length; i++) {
        const p = pointToPixel(stroke.points[i]);
        ctx.lineTo(p.x, p.y);
      }
    }
    ctx.stroke();
  }

  function redrawAll() {
    const rect = wrap.getBoundingClientRect();
    ctx.fillStyle = '#FFFFFF';
    ctx.fillRect(0, 0, rect.width, rect.height);
    strokes.forEach(drawStroke);
  }

  /* ---------- Pointer events (mouse + touch/stylus) ---------- */
  function getRelativePoint(e) {
    const rect = wrap.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width;
    const y = (e.clientY - rect.top) / rect.height;
    return { x: Math.min(1, Math.max(0, x)), y: Math.min(1, Math.max(0, y)) };
  }

  canvas.addEventListener('pointerdown', e => {
    e.preventDefault();
    canvas.setPointerCapture(e.pointerId);
    drawing = true;
    currentStroke = { color: currentColor, size: currentSize, eraser: tool === 'eraser', points: [getRelativePoint(e)] };
  });
  canvas.addEventListener('pointermove', e => {
    if (!drawing || !currentStroke) return;
    currentStroke.points.push(getRelativePoint(e));
    redrawAll();
    drawStroke(currentStroke);
  });
  function endStroke(e) {
    if (!drawing) return;
    drawing = false;
    if (currentStroke && currentStroke.points.length > 0) strokes.push(currentStroke);
    currentStroke = null;
    redrawAll();
  }
  canvas.addEventListener('pointerup', endStroke);
  canvas.addEventListener('pointerleave', endStroke);
  canvas.addEventListener('pointercancel', endStroke);

  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  /* ---------- Toolbar: warna, ukuran, pen/eraser ---------- */
  $$('.color-swatch').forEach(sw => {
    sw.addEventListener('click', () => {
      $$('.color-swatch').forEach(s => s.classList.remove('active'));
      sw.classList.add('active');
      currentColor = sw.dataset.color;
      tool = 'pen';
      $('#toolPen').classList.add('active');
      $('#toolEraser').classList.remove('active');
    });
  });
  $('#brushSize').addEventListener('input', e => { currentSize = Number(e.target.value); });
  $('#toolPen').addEventListener('click', () => {
    tool = 'pen';
    $('#toolPen').classList.add('active');
    $('#toolEraser').classList.remove('active');
  });
  $('#toolEraser').addEventListener('click', () => {
    tool = 'eraser';
    $('#toolEraser').classList.add('active');
    $('#toolPen').classList.remove('active');
  });

  $('#btnUndo').addEventListener('click', () => {
    strokes.pop();
    redrawAll();
  });
  $('#btnClear').addEventListener('click', () => {
    if (strokes.length === 0) return;
    if (!confirm('Bersihkan seluruh kanvas? Gambar yang belum disimpan akan hilang.')) return;
    strokes = [];
    redrawAll();
  });

  /* ---------- Simpan ke galeri ---------- */
  const modalSave = $('#modalSaveCanvas');
  $('#btnSaveCanvas').addEventListener('click', () => {
    if (strokes.length === 0) { toast('Kanvas masih kosong, gambar sesuatu dulu ya', 'info'); return; }
    $('#canvasTitleInput').value = `Kanvas ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long' })}`;
    modalSave.classList.add('show');
    setTimeout(() => $('#canvasTitleInput').focus(), 150);
  });
  $('#closeModalSaveCanvas').addEventListener('click', () => modalSave.classList.remove('show'));
  $('#cancelModalSaveCanvas').addEventListener('click', () => modalSave.classList.remove('show'));
  modalSave.addEventListener('click', e => { if (e.target === modalSave) modalSave.classList.remove('show'); });

  // 3. DIUBAH: Proses simpan dialihkan menembak ke API PHP POST
  $('#confirmSaveCanvas').addEventListener('click', async () => {
    const title = $('#canvasTitleInput').value.trim() || 'Kanvas tanpa judul';
    const thumb = canvas.toDataURL('image/png'); // Merubah data kanvas grafis menjadi string gambar base64

    const response = await fetch('api-canvas.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ title, thumb })
    });

    const resData = await response.json();
    if (resData.status === 'success') {
      toast('Kanvas berhasil disimpan', 'success');
      modalSave.classList.remove('show');
      
      // Ambil ulang data dari database MySQL dan gambar ulang galerinya
      state = await AmbisData.loadState();
      renderGallery();
    } else {
      toast('Gagal menyimpan kanvas', 'danger');
    }
  });

  // 4. DIUBAH: Proses merender dan menghapus gambar dialihkan melalui API PHP DELETE
  // Ganti bagian fungsi renderGallery di js/kanvas.js lu biar ngebaca dari sketches, bukan canvases
function renderGallery() {
  const gallery = $('#canvasGallery');
  const empty = $('#canvasEmptyState');
  
  // Baca state.sketches (bukan state.canvases)
  if (!state.sketches || state.sketches.length === 0) {
    gallery.innerHTML = '';
    if (empty) empty.classList.add('show');
    return;
  }
  if (empty) empty.classList.remove('show');

  // Render HTML kartu sketsa menggunakan image_path dari server
  gallery.innerHTML = state.sketches.map(sketch => {
    return `
      <div class="canvas-card" data-id="${sketch.id}">
        <div class="canvas-thumb-wrap">
          <img src="${sketch.image_path}" alt="${escapeHtml(sketch.title)}" class="canvas-img-preview">
        </div>
        <div class="canvas-card-body">
          <strong class="canvas-card-title">${escapeHtml(sketch.title)}</strong>
          <span class="canvas-card-date"><i class="fa-regular fa-calendar"></i> ${formatDateLong(sketch.date)}</span>
          <div class="canvas-card-actions">
            <a href="${sketch.image_path}" download="${sketch.title.replace(/\s+/g, '_')}.png" class="btn-download"><i class="fa-solid fa-download"></i> Unduh</a>
            <button class="btn-delete-canvas" data-delete-sketch="${sketch.id}"><i class="fa-regular fa-trash-can"></i> Hapus</button>
          </div>
        </div>
      </div>\n    `;
  }).join('');

  // Benerin Logic Delete di js/kanvas.js agar nembak api-delete-sketch.php
  document.querySelectorAll('[data-delete-sketch]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = Number(btn.dataset.deleteSketch);
      if (!confirm(`Hapus gambar ini dari galeri?`)) return;

      try {
        const response = await fetch('api-delete-sketch.php', {
          method: 'POST', // Sesuai dengan isi api-delete-sketch.php lu yang ngebaca php://input
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });

        const resData = await response.json();
        if (resData.status === 'success') {
          toast('Kanvas berhasil dihapus', 'danger');
          // Ambil ulang state terbaru dari database MySQL
          state = await AmbisData.loadState();
          renderGallery();
        } else {
          toast('Gagal menghapus kanvas', 'danger');
        }
      } catch (err) {
        console.error(err);
        toast('Error koneksi backend', 'danger');
      }
    });
  });
}

  renderGallery();
})();