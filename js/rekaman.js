/* =========================================================
   AMBIS — REKAMAN SUARA (VERSI CONNECT DATABASE)
   ========================================================= */
(async function () { // 1. DIUBAH: Menambahkan kata kunci 'async' di pembungkus fungsi utama
  'use strict';
  const $ = sel => document.querySelector(sel);
  const { escapeHtml } = AmbisData;
  
  // 2. DIUBAH: Mengambil data awal langsung dari database via API PHP
  let state = await AmbisData.loadState(); 

  const btnRecord = $('#btnRecord');
  const iconRecord = $('#iconRecord');
  const btnPause = $('#btnPause');
  const iconPause = $('#iconPause');
  const btnDiscard = $('#btnDiscard');
  const statusEl = $('#recorderStatus');
  const timerEl = $('#recorderTimer');
  const hintEl = $('#recorderHint');
  const visualizer = $('#recorderVisualizer');
  const preview = $('#recorderPreview');
  const previewAudio = $('#previewAudio');
  const recordingTitleInput = $('#recordingTitle');

  /* ---------- Visualizer bars (KODE FRONT-END UTUH) ---------- */
  const BAR_COUNT = 40;
  for (let i = 0; i < BAR_COUNT; i++) {
    const bar = document.createElement('div');
    bar.className = 'bar';
    bar.style.height = '4px';
    visualizer.appendChild(bar);
  }
  const bars = Array.from(visualizer.querySelectorAll('.bar'));

  let mediaRecorder = null;
  let audioChunks = [];
  let stream = null;
  let audioCtx = null, analyser = null, sourceNode = null, rafId = null;
  let timerInterval = null;
  let seconds = 0;
  let isPaused = false;
  let lastBlob = null;

  function formatTimer(s) {
    const m = String(Math.floor(s / 60)).padStart(2, '0');
    const sec = String(s % 60).padStart(2, '0');
    return `${m}:${sec}`;
  }

  function startTimer() {
    seconds = 0;
    timerEl.textContent = formatTimer(0);
    timerInterval = setInterval(() => {
      if (!isPaused) {
        seconds++;
        timerEl.textContent = formatTimer(seconds);
      }
    }, 1000);
  }
  function stopTimer() { clearInterval(timerInterval); }

  function animateVisualizer() {
    if (!analyser) return;
    const data = new Uint8Array(analyser.frequencyBinCount);
    analyser.getByteFrequencyData(data);
    const step = Math.floor(data.length / BAR_COUNT) || 1;
    bars.forEach((bar, i) => {
      const v = data[i * step] || 0;
      const h = isPaused ? 4 : Math.max(4, (v / 255) * 56);
      bar.style.height = h + 'px';
    });
    rafId = requestAnimationFrame(animateVisualizer);
  }
  function stopVisualizer() {
    if (rafId) cancelAnimationFrame(rafId);
    bars.forEach(bar => bar.style.height = '4px');
    visualizer.classList.remove('active');
  }

  async function startRecording() {
    try {
      stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    } catch (err) {
      toast('Izin mikrofon ditolak atau tidak tersedia di perangkat ini', 'danger');
      return;
    }

    audioChunks = [];
    mediaRecorder = new MediaRecorder(stream);
    mediaRecorder.addEventListener('dataavailable', e => { if (e.data.size > 0) audioChunks.push(e.data); });
    mediaRecorder.addEventListener('stop', onRecordingStopped);
    mediaRecorder.start();

    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    analyser = audioCtx.createAnalyser();
    analyser.fftSize = 256;
    sourceNode = audioCtx.createMediaStreamSource(stream);
    sourceNode.connect(analyser);
    visualizer.classList.add('active');
    animateVisualizer();

    isPaused = false;
    startTimer();

    btnRecord.classList.add('recording');
    iconRecord.className = 'fa-solid fa-stop';
    btnPause.disabled = false;
    btnDiscard.disabled = false;
    statusEl.textContent = 'Sedang merekam...';
    statusEl.classList.add('on');
    hintEl.textContent = 'Klik ikon stop untuk mengakhiri rekaman, atau pause untuk menjeda sebentar.';
    preview.classList.remove('show');
  }

  function togglePause() {
    if (!mediaRecorder) return;
    if (!isPaused) {
      mediaRecorder.pause();
      isPaused = true;
      iconPause.className = 'fa-solid fa-play';
      statusEl.textContent = 'Dijeda';
      statusEl.classList.remove('on');
    } else {
      mediaRecorder.resume();
      isPaused = false;
      iconPause.className = 'fa-solid fa-pause';
      statusEl.textContent = 'Sedang merekam...';
      statusEl.classList.add('on');
    }
  }

  function stopRecording() {
    if (!mediaRecorder) return;
    mediaRecorder.stop();
    stream.getTracks().forEach(t => t.stop());
    if (audioCtx) audioCtx.close();
    stopVisualizer();
    stopTimer();

    btnRecord.classList.remove('recording');
    iconRecord.className = 'fa-solid fa-microphone';
    btnPause.disabled = true;
    iconPause.className = 'fa-solid fa-pause';
    btnDiscard.disabled = true;
    statusEl.textContent = 'Siap merekam';
    statusEl.classList.remove('on');
    hintEl.textContent = 'Klik tombol mikrofon untuk mulai merekam. Browser akan meminta izin akses mikrofon terlebih dahulu.';
  }

  function onRecordingStopped() {
    lastBlob = new Blob(audioChunks, { type: 'audio/webm' });
    const url = URL.createObjectURL(lastBlob);
    previewAudio.src = url;
    recordingTitleInput.value = `Rekaman ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long' })}`;
    preview.classList.add('show');
  }

  function discardCurrent() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') stopRecording();
    preview.classList.remove('show');
    lastBlob = null;
    timerEl.textContent = '00:00';
  }

  btnRecord.addEventListener('click', () => {
    if (!mediaRecorder || mediaRecorder.state === 'inactive') startRecording();
    else stopRecording();
  });
  btnPause.addEventListener('click', togglePause);
  btnDiscard.addEventListener('click', discardCurrent);
  $('#btnDiscardPreview').addEventListener('click', () => {
    preview.classList.remove('show');
    lastBlob = null;
    toast('Rekaman dibuang', 'info');
  });

  // 3. DIUBAH: Proses simpan dialihkan dari localStorage menembak ke API PHP
  $('#btnSaveRecording').addEventListener('click', () => {
    if (!lastBlob) return;
    const title = recordingTitleInput.value.trim() || 'Rekaman tanpa judul';
    const durationText = timerEl.textContent;

    const reader = new FileReader();
    reader.onload = async () => {
      const response = await fetch('api-recording.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          title: title,
          duration: durationText,
          audio: reader.result // Data string Base64 suara
        })
      });

      const resData = await response.json();
      if (resData.status === 'success') {
        toast('Rekaman berhasil disimpan', 'success');
        preview.classList.remove('show');
        lastBlob = null;
        
        // Ambil data terbaru dari database lalu gambar ulang daftarnya
        state = await AmbisData.loadState();
        renderList();
      } else {
        toast('Gagal menyimpan rekaman ke database', 'danger');
      }
    };
    reader.readAsDataURL(lastBlob);
  });

  // 4. DIUBAH: Rendering list & Tombol Hapus dialihkan menembak ke API PHP DELETE
  function renderList() {
    const list = $('#recList');
    const empty = $('#recEmptyState');
    if (state.recordings.length === 0) {
      list.innerHTML = '';
      empty.classList.add('show');
      return;
    }
    empty.classList.remove('show');
    
    list.innerHTML = state.recordings.map(rec => {
      const demoClass = rec.demo ? ' demo' : '';
      const audioTag = rec.demo
        ? `<audio controls></audio>`
        : `<audio controls src="${rec.audio}"></audio>`;
      return `
        <div class="rec-item${demoClass}" data-id="${rec.id}">
          <div class="rec-item-icon"><i class="fa-solid fa-microphone"></i></div>
          <div class="rec-item-body">
            <strong>${escapeHtml(rec.title)}</strong>
            <span><i class="fa-regular fa-calendar"></i> ${AmbisData.formatDateLong(rec.date)} · ${escapeHtml(rec.duration)}${rec.demo ? ' · contoh' : ''}</span>
          </div>
          ${audioTag}
          <div class="rec-item-actions">
            <button class="icon-btn" data-delete-rec="${rec.id}" title="Hapus"><i class="fa-regular fa-trash-can"></i></button>
          </div>
        </div>
      `;
    }).join('');

    document.querySelectorAll('[data-delete-rec]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = Number(btn.dataset.deleteRec);
        if (!confirm(`Yakin ingin menghapus rekaman ini?`)) return;

        const response = await fetch('api-recording.php', {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        });

        const resData = await response.json();
        if (resData.status === 'success') {
          toast('Rekaman dihapus', 'danger');
          // Ambil data terbaru dari database lalu refresh list UI
          state = await AmbisData.loadState();
          renderList();
        } else {
          toast('Gagal menghapus rekaman', 'danger');
        }
      });
    });
  }

  if (!navigator.mediaDevices || !window.MediaRecorder) {
    hintEl.textContent = 'Browser ini tidak mendukung perekaman suara. Coba gunakan Chrome, Edge, atau Firefox versi terbaru.';
    btnRecord.disabled = true;
  }

  renderList();
})();