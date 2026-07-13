<?php
$pageTitle = 'Workspace Saya';
$activePage = 'workspace';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// 1. Ambil jumlah catatan per kategori secara dinamis dari MySQL
try {
    $stmt = $pdo->prepare("SELECT type, COUNT(*) as total FROM notes WHERE user_id = ? GROUP BY type");
    $stmt->execute([$user_id]);
    $counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    $counts = [];
}

// Ambil jumlah per kategori (default 0 jika belum ada catatan)
$countWeb = isset($counts['a']) ? $counts['a'] : 0;
$countJarkom = isset($counts['b']) ? $counts['b'] : 0;
$countBasdat = isset($counts['c']) ? $counts['c'] : 0;
$countMatkom = isset($counts['d']) ? $counts['d'] : 0;
?>

<main class="main">
  <div class="topbar">
    <div class="greeting">
      <h1><i class="fa-solid fa-layer-group" style="color:var(--navy); margin-right:8px;"></i>Workspace Saya</h1>
      <p class="page-subtitle">Setiap workspace mengelompokkan catatan berdasarkan mata kuliah.</p>
    </div>
  </div>

  <section class="section">
    <!-- Menggunakan class grid/flex bawaan dari template CSS asli kamu -->
    <div class="workspace-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; margin-top: 10px;">
      
      <!-- CARD 1: Pemrograman Web -->
      <div class="workspace-card" style="background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 16px;">
        <div>
          <div class="folder-icon-wrapper" style="width: 48px; height: 48px; background: #fdf2f8; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <i class="fa-solid fa-folder" style="color: #db2777; font-size: 20px;"></i>
          </div>
          <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 700; color: #1e293b;">Pemrograman Web</h3>
          <span style="font-size: 13px; color: #94a3b8; font-weight: 500;"><?= $countWeb ?> catatan</span>
        </div>
        <a href="catatan.php?kategori=a" class="btn-workspace" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; background: #f7f4f0; color: #6b4e3e; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: 600;">
          Buka Workspace <i class="fa-solid fa-arrow-right" style="font-size: 11px;"></i>
        </a>
      </div>

      <!-- CARD 2: Jaringan Komputer -->
      <div class="workspace-card" style="background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 16px;">
        <div>
          <div class="folder-icon-wrapper" style="width: 48px; height: 48px; background: #fdf2f8; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <i class="fa-solid fa-folder" style="color: #db2777; font-size: 20px;"></i>
          </div>
          <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 700; color: #1e293b;">Jaringan Komputer</h3>
          <span style="font-size: 13px; color: #94a3b8; font-weight: 500;"><?= $countJarkom ?> catatan</span>
        </div>
        <a href="catatan.php?kategori=b" class="btn-workspace" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; background: #f7f4f0; color: #6b4e3e; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: 600;">
          Buka Workspace <i class="fa-solid fa-arrow-right" style="font-size: 11px;"></i>
        </a>
      </div>

      <!-- CARD 3: Basis Data -->
      <div class="workspace-card" style="background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 16px;">
        <div>
          <div class="folder-icon-wrapper" style="width: 48px; height: 48px; background: #e6f4ea; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <i class="fa-solid fa-folder" style="color: #137333; font-size: 20px;"></i>
          </div>
          <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 700; color: #1e293b;">Basis Data</h3>
          <span style="font-size: 13px; color: #94a3b8; font-weight: 500;"><?= $countBasdat ?> catatan</span>
        </div>
        <a href="catatan.php?kategori=c" class="btn-workspace" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; background: #f7f4f0; color: #6b4e3e; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: 600;">
          Buka Workspace <i class="fa-solid fa-arrow-right" style="font-size: 11px;"></i>
        </a>
      </div>

      <!-- CARD 4: Matematika Komputasi -->
      <div class="workspace-card" style="background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 16px;">
        <div>
          <div class="folder-icon-wrapper" style="width: 48px; height: 48px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <i class="fa-solid fa-folder" style="color: #d97706; font-size: 20px;"></i>
          </div>
          <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 700; color: #1e293b;">Matematika Komputasi</h3>
          <span style="font-size: 13px; color: #94a3b8; font-weight: 500;"><?= $countMatkom ?> catatan</span>
        </div>
        <a href="catatan.php?kategori=d" class="btn-workspace" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; background: #f7f4f0; color: #6b4e3e; text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: 600;">
          Buka Workspace <i class="fa-solid fa-arrow-right" style="font-size: 11px;"></i>
        </a>
      </div>

    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>