<?php
// $activePage dipakai untuk menandai menu yang sedang aktif.
function navClass($key, $active) { return 'nav-item' . ($key === $active ? ' active' : ''); }

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$sidebar_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

$totalCatatanSidebar = 0;

// JIKA $pdo BELUM ADA, KITA PANGGIL KONEKSI SECARA MANDIRI DI SINI
if (!isset($pdo) || $pdo === null) {
    if (file_exists('includes/koneksi.php')) {
        include_once 'includes/koneksi.php';
    } elseif (file_exists('../includes/koneksi.php')) {
        include_once '../includes/koneksi.php';
    }
}

// HITUNG DATA
if (isset($pdo) && $pdo !== null) {
    try {
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE user_id = ?");
        $stmtCount->execute([$sidebar_user_id]);
        $totalCatatanSidebar = (int)$stmtCount->fetchColumn();
    } catch (PDOException $e) {
        $totalCatatanSidebar = 0;
    }
}
?>
<aside class="sidebar" id="sidebar">
  <div class="brand">
    <div class="brand-icon"><i class="fa-solid fa-graduation-cap"></i></div>
    <div class="brand-text">
      <strong>Ambis</strong>
      <small>Catatan Kuliah</small>
    </div>
  </div>

  <nav class="nav-scroll">
    <div class="nav-group-label">Menu Utama</div>
    <ul>
      <li><a href="index.php" class="<?= navClass('dashboard', $activePage) ?>"><i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span></a></li>
      <!-- 3. Cetak totalCatatanSidebar langsung ke dalam badge-mini -->
      <li><a href="catatan.php" class="<?= navClass('catatan', $activePage) ?>"><i class="fa-solid fa-note-sticky"></i><span>Semua Catatan</span><span class="badge-mini" id="navBadgeCatatan"><?= $totalCatatanSidebar ?></span></a></li>
      <li><a href="workspace.php" class="<?= navClass('workspace', $activePage) ?>"><i class="fa-solid fa-layer-group"></i><span>Workspace</span></a></li>
      <li><a href="rekaman.php" class="<?= navClass('rekaman', $activePage) ?>"><i class="fa-solid fa-microphone"></i><span>Rekaman Suara</span></a></li>
      <li><a href="kanvas.php" class="<?= navClass('kanvas', $activePage) ?>"><i class="fa-solid fa-pen-nib"></i><span>Kanvas Digital</span></a></li>
    </ul>

    <div class="nav-group-label">Lainnya</div>
    <ul>
      <li><a href="kategori.php" class="<?= navClass('kategori', $activePage) ?>"><i class="fa-solid fa-tags"></i><span>Kategori</span></a></li>
      <li><a href="arsip.php" class="<?= navClass('arsip', $activePage) ?>"><i class="fa-solid fa-box-archive"></i><span>Arsip</span></a></li>
      <li><a href="pengaturan.php" class="<?= navClass('pengaturan', $activePage) ?>"><i class="fa-solid fa-gear"></i><span>Pengaturan</span></a></li>
    </ul>
  </nav>

  <div class="sidebar-footer">
    <div class="avatar">
      <?php 
        echo isset($_SESSION['nama']) && !empty($_SESSION['nama']) ? strtoupper(substr($_SESSION['nama'], 0, 1)) : 'U'; 
      ?>
    </div>
    <div class="user-meta">
      <strong><?php echo isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Pengguna'; ?></strong>
      <!-- DIUBAH: Menampilkan kata "Mahasiswa" + isi kolom peran (Informatika) -->
      <small>
        <?php 
          $prodi = isset($_SESSION['prodi']) ? htmlspecialchars($_SESSION['prodi']) : 'TI';
          echo "Mahasiswa " . $prodi; 
        ?>
      </small>
    </div>
    <button class="logout-btn" title="Logout" type="button" onclick="window.location.href='logout.php'"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
  </div>
</aside>