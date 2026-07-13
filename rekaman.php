<?php
$pageTitle = 'Rekaman Suara';
$activePage = 'rekaman';
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main">
  <div class="topbar">
    <div class="greeting">
      <h1><i class="fa-solid fa-microphone" style="color:var(--navy); margin-right:8px;"></i>Rekaman Suara Kuliah</h1>
      <p class="page-subtitle">Rekam dan putar kembali penjelasan dosen saat kuliah.</p>
    </div>
  </div>

  <section class="section">
    <!-- KOTAK KONTROL PEREKAM -->
    <div style="background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center; margin-bottom: 24px;">
      <div style="margin-bottom: 16px;">
        <input type="text" id="recordingTitle" placeholder="Masukkan judul rekaman kuliah..." style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none; text-align: center;">
      </div>
      
      <!-- BULATAN TOMBOL REKAM -->
      <div style="margin-bottom: 12px;">
        <button id="btnToggleRecord" style="width: 70px; height: 70px; border-radius: 50%; background: #ef4444; border: none; color: white; font-size: 24px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4); transition: all 0.3s;">
          <i class="fa-solid fa-microphone" id="recordIcon"></i>
        </button>
      </div>
      <div id="recordStatus" style="font-weight: 600; color: #64748b; font-size: 14px;">Klik tombol untuk mulai merekam</div>
      <div id="recordTimer" style="font-size: 18px; font-weight: bold; margin-top: 4px; display: none; color: #ef4444;">00:00</div>
    </div>

    <!-- DAFTAR HASIL REKAMAN -->
    <h3><i class="fa-solid fa-list" style="margin-right: 8px;"></i>Daftar Rekaman Kamu</h3>
    <div id="recordingsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; margin-top: 16px;">
      <!-- Diisi otomatis via JavaScript -->
    </div>

    <div class="empty-state" id="recordingsEmptyState" style="display: none; text-align: center; padding: 40px 20px;">
      <i class="fa-regular fa-circle-xmark" style="font-size: 48px; color: #cbd5e1; margin-bottom: 12px;"></i>
      <strong style="display: block;">Belum ada rekaman suara</strong>
      <span style="color: #94a3b8; font-size: 14px;">Materi kuliah yang kamu rekam akan muncul di sini.</span>
    </div>
  </section>
</main>

<!-- SCRIPT PENGOLAH AUDIO DAN DATABASE -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  let mediaRecorder;
  let audioChunks = [];
  let isRecording = false;
  let timerInterval;
  let seconds = 0;

  const btnRecord = document.getElementById('btnToggleRecord');
  const recordIcon = document.getElementById('recordIcon');
  const recordStatus = document.getElementById('recordStatus');
  const recordTimer = document.getElementById('recordTimer');
  const titleInput = document.getElementById('recordingTitle');
  const grid = document.getElementById('recordingsGrid');
  const emptyState = document.getElementById('recordingsEmptyState');

  // Load daftar rekaman dari MySQL saat halaman dibuka
  async function loadRecordings() {
    try {
      const res = await fetch('api-fetch-recordings.php');
      const data = await res.json();
      
      if (data.status === 'success' && data.recordings.length > 0) {
        emptyState.style.display = 'none';
        grid.style.display = 'grid';
        grid.innerHTML = data.recordings.map(rec => `
          <div style="background: #fff; padding: 16px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 10px;">
            <div>
              <strong style="font-size: 15px; display: block; color: #1e293b;">${rec.title}</strong>
              <span style="font-size: 12px; color: #94a3b8;"><i class="fa-regular fa-calendar"></i> ${rec.date}</span>
            </div>
            <audio src="${rec.file_path}" controls style="width: 100%; height: 32px;"></audio>
            <button onclick="deleteRecording(${rec.id})" style="background: none; border: none; color: #ef4444; font-size: 13px; text-align: right; cursor: pointer; align-self: flex-end;"><i class="fa-solid fa-trash"></i> Hapus</button>
          </div>
        `).join('');
      } else {
        grid.style.display = 'none';
        emptyState.style.display = 'block';
      }
    } catch (e) { console.error(e); }
  }

  // Aksi Klik Tombol Rekam
  btnRecord.addEventListener('click', async () => {
    if (!isRecording) {
      const title = titleInput.value.trim();
      if (!title) { alert('Masukkan judul rekaman terlebih dahulu!'); return; }

      // Minta izin akses Mikrofon laptop
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];

        mediaRecorder.ondataavailable = event => { audioChunks.push(event.data); };

        mediaRecorder.onstop = async () => {
          const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
          
          // Kirim file audio ke backend menggunakan FormData
          const formData = new FormData();
          formData.append('audio_data', audioBlob);
          formData.append('title', titleInput.value.trim());

          recordStatus.textContent = "Sedang menyimpan rekaman...";
          
          const response = await fetch('api-save-recording.php', { method: 'POST', body: formData });
          const result = await response.json();
          
          if (result.status === 'success') {
            titleInput.value = '';
            alert('Rekaman kuliah berhasil disimpan!');
            loadRecordings();
          } else { alert(result.message); }
          
          recordStatus.textContent = "Klik tombol untuk mulai merekam";
        };

        // Mulai Perekaman
        mediaRecorder.start();
        isRecording = true;
        btnRecord.style.background = '#1e293b'; // Berubah warna gelap saat merekam
        recordIcon.className = 'fa-solid fa-square'; // Berubah jadi ikon stop kotak
        recordStatus.textContent = "Sedang mrekam suara... Jangan tutup halaman!";
        
        // Jalankan Timer Detik
        seconds = 0;
        recordTimer.style.display = 'block';
        recordTimer.textContent = "00:00";
        timerInterval = setInterval(() => {
          seconds++;
          let mins = Math.floor(seconds / 60).toString().padStart(2, '0');
          let secs = (seconds % 60).toString().padStart(2, '0');
          recordTimer.textContent = `${mins}:${secs}`;
        }, 1000);

      } catch (err) { alert('Gagal mengakses mikrofon laptop: ' + err); }
    } else {
      // Hentikan Perekaman
      mediaRecorder.stop();
      mediaRecorder.stream.getTracks().forEach(track => track.stop()); // Matikan mic
      isRecording = false;
      btnRecord.style.background = '#ef4444';
      recordIcon.className = 'fa-solid fa-microphone';
      recordTimer.style.display = 'none';
      clearInterval(timerInterval);
    }
  });

  // Fungsi Hapus Rekaman Global
  window.deleteRecording = async function(id) {
    if (!confirm('Apakah kamu yakin ingin menghapus rekaman ini?')) return;
    try {
      const res = await fetch('api-delete-recording.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const result = await res.json();
      if (result.status === 'success') { loadRecordings(); } else { alert(result.message); }
    } catch (e) { alert('Gagal menghapus rekaman.'); }
  };

  loadRecordings();
});
</script>

<?php include 'includes/footer.php'; ?>