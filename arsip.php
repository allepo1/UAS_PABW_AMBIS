<?php
$pageTitle = 'Arsip';
$activePage = 'arsip';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

try {
    // 1. Ambil Catatan yang diarsipkan
    $stmtNotes = $pdo->prepare("SELECT * FROM notes WHERE user_id = ? AND is_archived = 1 ORDER BY id DESC");
    $stmtNotes->execute([$user_id]);
    $archivedNotes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

    // 2. Ambil Rekaman Suara yang diarsipkan
    $stmtRecs = $pdo->prepare("SELECT * FROM recordings WHERE user_id = ? AND is_archived = 1 ORDER BY id DESC");
    $stmtRecs->execute([$user_id]);
    $archivedRecs = $stmtRecs->fetchAll(PDO::FETCH_ASSOC);

    // 3. Ambil Kanvas/Sketsa yang diarsipkan
    $stmtSketches = $pdo->prepare("SELECT * FROM sketches WHERE user_id = ? AND is_archived = 1 ORDER BY id DESC");
    $stmtSketches->execute([$user_id]);
    $archivedSketches = $stmtSketches->fetchAll(PDO::FETCH_ASSOC);
    
    $totalArsip = count($archivedNotes) + count($archivedRecs) + count($archivedSketches);
} catch (PDOException $e) {
    $archivedNotes = $archivedRecs = $archivedSketches = [];
    $totalArsip = 0;
}
?>

<main class="main">
  <div class="topbar">
    <div class="greeting">
      <h1><i class="fa-solid fa-box-archive" style="color:var(--navy); margin-right:8px;"></i>Arsip Saya</h1>
      <p class="page-subtitle">Catatan, rekaman, atau kanvas yang kamu arsipkan akan muncul di sini.</p>
    </div>
  </div>

  <section class="section">
    <?php if ($totalArsip === 0): ?>
      <!-- TAMPILAN JIKA KOSONG (Sesuai Desain Aslimu) -->
      <div class="empty-state show" id="emptyStateArsip" style="padding:80px 20px; text-align: center;">
        <i class="fa-regular fa-folder-open" style="font-size: 48px; color: #cbd5e1; margin-bottom: 12px; display:block;"></i>
        <strong style="display:block; font-size:16px; margin-bottom:4px;">Arsip masih kosong</strong>
        <span style="color:#94a3b8; font-size:14px;">Belum ada catatan, rekaman, atau kanvas yang diarsipkan.</span>
      </div>
    <?php else: ?>
      
      <!-- NAVIGASI TAB KATEGORI ARSIP -->
      <div style="display: flex; gap: 12px; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
        <button onclick="switchTab('tab-catatan')" class="tab-btn active" style="padding: 8px 16px; background: none; border: none; font-weight: 600; cursor: pointer; color: #1e293b; border-bottom: 2px solid var(--navy);">Catatan (<?= count($archivedNotes) ?>)</button>
        <button onclick="switchTab('tab-rekaman')" class="tab-btn" style="padding: 8px 16px; background: none; border: none; font-weight: 600; cursor: pointer; color: #64748b;">Rekaman Suara (<?= count($archivedRecs) ?>)</button>
        <button onclick="switchTab('tab-kanvas')" class="tab-btn" style="padding: 8px 16px; background: none; border: none; font-weight: 600; cursor: pointer; color: #64748b;">Kanvas Digital (<?= count($archivedSketches) ?>)</button>
      </div>

      <!-- TAB PANEL 1: CATATAN -->
      <div id="tab-catatan" class="tab-content" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
        <?php if(empty($archivedNotes)): ?><p style="color:#94a3b8; font-size:14px; grid-column: 1/-1;">Tidak ada catatan diarsipkan.</p><?php endif; ?>
        <?php foreach($archivedNotes as $note): ?>
          <div style="background:#fff; padding:16px; border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); display:flex; flex-direction:column; justify-content:space-between;">
            <div>
              <h4 style="margin:0 0 6px 0; color:#1e293b;"><?= htmlspecialchars($note['title']) ?></h4>
              <p style="font-size:13px; color:#64748b; margin:0 0 12px 0;"><?= substr(strip_tags($note['preview']), 0, 80) ?>...</p>            </div>
            <button onclick="unarchiveItem('note', <?= $note['id'] ?>)" style="align-self:flex-start; background:none; border:none; color:#0d9488; font-size:12px; font-weight:600; cursor:pointer;"><i class="fa-solid fa-box-open"></i> Kembalikan</button>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- TAB PANEL 2: REKAMAN -->
      <div id="tab-rekaman" class="tab-content" style="display: none; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
        <?php if(empty($archivedRecs)): ?><p style="color:#94a3b8; font-size:14px; grid-column: 1/-1;">Tidak ada rekaman diarsipkan.</p><?php endif; ?>
        <?php foreach($archivedRecs as $rec): ?>
          <div style="background:#fff; padding:16px; border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); display:flex; flex-direction:column; gap:10px;">
            <div>
              <strong style="font-size:14px; display:block; color:#1e293b;"><?= htmlspecialchars($rec['title']) ?></strong>
              <span style="font-size:11px; color:#94a3b8;"><?= $rec['date'] ?></span>
            </div>
            <audio src="<?= $rec['file_path'] ?>" controls style="width:100%; height:30px;"></audio>
            <button onclick="unarchiveItem('recording', <?= $rec['id'] ?>)" style="align-self:flex-start; background:none; border:none; color:#0d9488; font-size:12px; font-weight:600; cursor:pointer;"><i class="fa-solid fa-box-open"></i> Kembalikan</button>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- TAB PANEL 3: KANVAS -->
      <div id="tab-kanvas" class="tab-content" style="display: none; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
        <?php if(empty($archivedSketches)): ?><p style="color:#94a3b8; font-size:14px; grid-column: 1/-1;">Tidak ada gambar diarsipkan.</p><?php endif; ?>
        <?php foreach($archivedSketches as $sk): ?>
          <div style="background:#fff; padding:12px; border-radius:12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); display:flex; flex-direction:column; gap:8px;">
            <img src="<?= $sk['image_path'] ?>" style="width:100%; height:140px; object-fit:contain; background:#f8fafc; border-radius:6px;">
            <strong style="font-size:14px; color:#1e293b;"><?= htmlspecialchars($sk['title']) ?></strong>
            <button onclick="unarchiveItem('sketch', <?= $sk['id'] ?>)" style="align-self:flex-start; background:none; border:none; color:#0d9488; font-size:12px; font-weight:600; cursor:pointer;"><i class="fa-solid fa-box-open"></i> Kembalikan</button>
          </div>
        <?php endforeach; ?>
      </div>

    <?php endif; ?>
  </section>
</main>

<script>
// Fungsi Switch Tab Konten Arsip
function switchTab(tabId) {
  document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.style.color = '#64748b';
    btn.style.borderBottom = 'none';
  });
  
  document.getElementById(tabId).style.display = 'grid';
  const activeBtn = event.currentTarget;
  activeBtn.style.color = '#1e293b';
  activeBtn.style.borderBottom = '2px solid var(--navy)';
}

// Kirim perintah RESTORE ke Backend API
async function unarchiveItem(type, id) {
  if (!confirm('Kembalikan item ini ke halaman utama?')) return;
  try {
    const res = await fetch('api-unarchive.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type, id })
    });
    const result = await res.json();
    if (result.status === 'success') {
      location.reload();
    } else {
      alert(result.message);
    }
  } catch (e) {
    alert('Gagal mengembalikan data dari arsip.');
  }
}
</script>

<?php include 'includes/footer.php'; ?>