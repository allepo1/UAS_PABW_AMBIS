<?php
$pageTitle = 'Pengaturan';
$activePage = 'pengaturan';
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

try {
    $stmt = $pdo->prepare("SELECT nama, program_studi, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $user = null;
}

$namaUser = isset($user['nama']) ? htmlspecialchars($user['nama']) : '';
$prodiUser = isset($user['program_studi']) ? htmlspecialchars($user['program_studi']) : '';
$emailUser = isset($user['email']) ? htmlspecialchars($user['email']) : '';
$initials = !empty($namaUser) ? strtoupper(substr($namaUser, 0, 1)) : '?';
?>

<main class="main">
  <div class="topbar">
    <div class="greeting">
      <h1><i class="fa-solid fa-gear" style="color:var(--navy); margin-right:8px;"></i>Pengaturan</h1>
      <p class="page-subtitle">Kelola profil dan preferensi aplikasi Ambis.</p>
    </div>
  </div>

  <section class="section">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; align-items: start;">
      
      <!-- KOTAK KIRI: FORM PROFIL -->
      <div style="background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;">
        <h3 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-user" style="color:#64748b;"></i> Profil
        </h3>
        
        <!-- Header Info Avatar -->
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
          <!-- PERBAIKAN ID: profileAvatar agar bisa diisi otomatis oleh js -->
          <div id="profileAvatar" style="width: 56px; height: 56px; background: #6b4e3e; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700;">
            <?= $initials ?>
          </div>
          <div>
            <!-- PERBAIKAN ID: profileNamePreview agar singkron dengan js -->
            <h4 id="profileNamePreview" style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b;"><?= $namaUser ?></h4>
            <!-- PERBAIKAN ID: profileRolePreview agar singkron dengan js -->
            <p id="profileRolePreview" style="margin: 2px 0 0 0; font-size: 13px; color: #94a3b8; font-weight: 500;"><?= $prodiUser ?></p>
          </div>
        </div>

        <!-- Form Update Data -->
        <form id="formProfil">
          <div style="margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569;">Nama Lengkap</label>
            <input type="text" id="settingNama" value="<?= $namaUser ?>" required placeholder="Masukkan nama lengkap..." style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
          </div>

          <div style="margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569;">Peran / Program Studi</label>
            <input type="text" id="settingPeran" value="<?= $prodiUser ?>" required placeholder="Contoh: Mahasiswa Informatika..." style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
          </div>

          <div style="margin-bottom: 24px; display: flex; flex-direction: column; gap: 6px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569;">Email</label>
            <input type="email" id="settingEmail" value="<?= $emailUser ?>" placeholder="Masukkan alamat email..." style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
          </div>

          <button type="submit" style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: #6b4e3e; color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
            <i class="fa-solid fa-check"></i> Simpan Profil
          </button>
        </form>
      </div>

      <!-- KOTAK KANAN: PREFERENSI -->
      <div style="background: #fff; padding: 24px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 20px;">
        <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-sliders" style="color:#64748b;"></i> Preferensi
        </h3>
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <div>
            <strong style="font-size: 14px; color: #1e293b; display: block;">Notifikasi Email</strong>
            <span style="font-size: 12px; color: #94a3b8;">Kirim ringkasan aktivitas ke email</span>
          </div>
          <input type="checkbox" id="toggleNotifEmail" style="width: 40px; height: 20px; cursor: pointer;">
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 16px;">
          <div>
            <strong style="font-size: 14px; color: #1e293b; display: block;">Notifikasi Aktivitas</strong>
            <span style="font-size: 12px; color: #94a3b8;">Tampilkan lonceng notifikasi di topbar</span>
          </div>
          <input type="checkbox" id="toggleNotifAktivitas" style="width: 40px; height: 20px; cursor: pointer;">
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 16px;">
          <div>
            <strong style="font-size: 14px; color: #1e293b; display: block;">Mode Gelap</strong>
            <span style="font-size: 12px; color: #94a3b8;">Segera hadir pada versi berikutnya</span>
          </div>
          <input type="checkbox" disabled style="width: 40px; height: 20px; opacity: 0.5;">
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 16px;">
          <div>
            <strong style="font-size: 14px; color: #1e293b; display: block;">Reset Data Lokal</strong>
            <span style="font-size: 12px; color: #94a3b8;">Hapus semua catatan, rekaman, dan kanvas di perangkat ini</span>
          </div>
          <button id="btnResetData" style="padding: 6px 16px; background: none; border: 1px solid #ef4444; color: #ef4444; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">Reset</button>
        </div>
      </div>

    </div>
  </section>
</main>

<?php
// Mendaftarkan file JS luar agar dijalankan bersih di bawah footer tanpa bentrok v=1.5 memaksa hapus cache browser
$pageScripts = ['js/pengaturan.js?v=1.5'];
include 'includes/footer.php';
?>