<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<main class="main">

  <div class="topbar">
    <div class="greeting">
      <h1>Selamat Datang, <span id="userNameDisplay">...</span> 👋</h1>
      <p>
        <?php
        date_default_timezone_set('Asia/Jakarta');
        $hariArr = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $bulanArr = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        echo $hariArr[date('l')] . ", " . date('d') . " " . $bulanArr[date('m')] . " " . date('Y') . " — Yuk lanjutkan catatan kuliahmu.";
        ?>
      </p>
    </div>

    <div class="topbar-right">
      <div class="search-wrap">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Cari catatan, mata kuliah, atau tanggal...">
      </div>
      <button class="btn btn-primary" id="btnCatatanBaru"><i class="fa-solid fa-plus"></i> Catatan Baru</button>
      <div class="notif-wrap">
        <button class="icon-btn" id="btnNotif" type="button"><i class="fa-regular fa-bell"></i><span class="dot" id="notifDot"></span></button>
      </div>
      <div class="topbar-avatar" id="userAvatarDisplay">U</div>
    </div>
  </div>

  <section class="section">
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon i1"><i class="fa-solid fa-note-sticky"></i></div>
        <div class="stat-body">
          <span class="label">Total Catatan</span>
          <strong id="statTotalCatatan">-</strong>
          <span class="stat-trend"><i class="fa-solid fa-arrow-trend-up"></i> aktif minggu ini</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon i2"><i class="fa-solid fa-layer-group"></i></div>
        <div class="stat-body">
          <span class="label">Total Workspace</span>
          <strong>4</strong>
          <span class="stat-trend"><i class="fa-solid fa-arrow-trend-up"></i> per mata kuliah</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon i3"><i class="fa-solid fa-microphone"></i></div>
        <div class="stat-body">
          <span class="label">Total Rekaman</span>
          <strong id="statTotalRekaman">-</strong>
          <span class="stat-trend"><i class="fa-solid fa-arrow-trend-up"></i> tersimpan</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon i4"><i class="fa-solid fa-pen-nib"></i></div>
        <div class="stat-body">
          <span class="label">Total Kanvas</span>
          <strong id="statTotalKanvas">-</strong>
          <span class="stat-trend"><i class="fa-solid fa-arrow-trend-up"></i> tersimpan</span>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="sectionCatatan">
    <div class="section-head">
      <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Catatan Terbaru</div>
      <a class="section-link" href="catatan.php">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="notes-grid" id="notesGrid"></div>
    <div class="empty-state" id="notesEmptyState">
      <i class="fa-regular fa-folder-open"></i>
      <strong>Belum ada catatan</strong>
      <span>Buat catatan pertamamu lewat tombol "Catatan Baru".</span>
    </div>
  </section>

  <section class="section" id="sectionWorkspace">
    <div class="section-head">
      <div class="section-title"><i class="fa-solid fa-layer-group"></i> Workspace Saya</div>
      <a class="section-link" href="workspace.php">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="workspace-grid">
      <div class="ws-card">
        <div class="ws-icon"><i class="fa-solid fa-folder-open"></i></div>
        <div>
          <div class="ws-title">Pemrograman Web</div>
          <div class="ws-count" id="wsCountA">- catatan</div>
        </div>
        <a class="ws-open-btn" href="catatan.php?kategori=a">Buka Workspace <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="ws-card w2">
        <div class="ws-icon"><i class="fa-solid fa-folder-open"></i></div>
        <div>
          <div class="ws-title">Jaringan Komputer</div>
          <div class="ws-count" id="wsCountB">- catatan</div>
        </div>
        <a class="ws-open-btn" href="catatan.php?kategori=b">Buka Workspace <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="ws-card w3">
        <div class="ws-icon"><i class="fa-solid fa-folder-open"></i></div>
        <div>
          <div class="ws-title">Basis Data</div>
          <div class="ws-count" id="wsCountC">- catatan</div>
        </div>
        <a class="ws-open-btn" href="catatan.php?kategori=c">Buka Workspace <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="ws-card w4">
        <div class="ws-icon"><i class="fa-solid fa-folder-open"></i></div>
        <div>
          <div class="ws-title">Matematika Komputasi</div>
          <div class="ws-count" id="wsCountD">- catatan</div>
        </div>
        <a class="ws-open-btn" href="catatan.php?kategori=d">Buka Workspace <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="bottom-grid">
      <div class="panel">
        <div class="section-head">
          <div class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Aktivitas Terbaru</div>
        </div>
        <div class="timeline" id="timelineList"></div>
      </div>
      <div class="quick-grid">
        <button class="quick-btn q1" id="quickCatatan" type="button">
          <div class="quick-icon"><i class="fa-solid fa-note-sticky"></i></div>
          <div class="quick-text">
            <strong>Buat Catatan Teks</strong>
            <span>Tulis materi kuliah baru</span>
          </div>
          <i class="fa-solid fa-chevron-right chev"></i>
        </button>
        <a class="quick-btn q2" href="rekaman.php">
          <div class="quick-icon"><i class="fa-solid fa-microphone"></i></div>
          <div class="quick-text">
            <strong>Mulai Rekaman Suara</strong>
            <span>Rekam penjelasan dosen</span>
          </div>
          <i class="fa-solid fa-chevron-right chev"></i>
        </a>
        <a class="quick-btn q3" href="kanvas.php">
          <div class="quick-icon"><i class="fa-solid fa-pen-nib"></i></div>
          <div class="quick-text">
            <strong>Buka Kanvas Digital</strong>
            <span>Gambar diagram atau ERD</span>
          </div>
          <i class="fa-solid fa-chevron-right chev"></i>
        </a>
      </div>
    </div>
  </section>
</main>

<?php include 'includes/modal-note.php'; ?>

<script>
  const jwtToken = localStorage.getItem('ambis_jwt');
  
  if (jwtToken) {
    // 1. Tampilkan nama lu di UI
    try {
      const payloadBase64 = jwtToken.split('.')[1];
      const userData = JSON.parse(atob(payloadBase64));
      document.getElementById('userNameDisplay').innerText = userData.nama;
      document.getElementById('userAvatarDisplay').innerText = userData.nama.charAt(0).toUpperCase();
    } catch (e) {
      console.error("Token error");
    }

    // 2. Bajak fetch bawaan browser biar otomatis nyelipin token ke API
    const originalFetch = window.fetch;
    window.fetch = async function(resource, config = {}) {
      if (!config.headers) config.headers = {};
      config.headers['Authorization'] = 'Bearer ' + jwtToken;
      return originalFetch(resource, config);
    };
  }
</script>

<?php
// INI BARIS YANG GUE BUANG TADI, SEKARANG UDAH BALIK!
$pageScripts = ['js/dashboard.js'];
include 'includes/footer.php';
?>