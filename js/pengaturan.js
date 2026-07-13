/* =========================================================
   AMBIS — PENGATURAN (VERSI CONNECT DATABASE)
   ========================================================= */
(async function () { // 1. DIUBAH: Menambahkan kata kunci 'async' di pembungkus fungsi utama
  'use strict';
  const $ = sel => document.querySelector(sel);
  
  // 2. DIUBAH: Mengambil data pengaturan profil langsung dari database MySQL via API PHP
  let state = await AmbisData.loadState();
  const { escapeHtml } = AmbisData;

  // KODE UI FRONT-END UTUH: Berfungsi mengambil inisial huruf dari nama depan & belakang
  function initials(name) {
    return (name || '').trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase() || 'K';
  }

  // KODE UI FRONT-END UTUH: Berfungsi memasukkan data dari database ke dalam kotak input form
  function fillForm() {
    $('#settingNama').value = state.settings.nama;
    $('#settingPeran').value = state.settings.peran;
    $('#settingEmail').value = state.settings.email;
    $('#toggleNotifEmail').checked = !!state.settings.notifEmail;
    $('#toggleNotifAktivitas').checked = !!state.settings.notifAktivitas;
    $('#profileAvatar').textContent = initials(state.settings.nama);
    $('#profileNamePreview').textContent = state.settings.nama;
    $('#profileRolePreview').textContent = state.settings.peran;
  }
  fillForm();

  // 3. DIUBAH: Proses submit diarahkan menggunakan Fetch API asinkronus menuju file backend PHP
  $('#formProfil').addEventListener('submit', async e => {
    e.preventDefault();
    
    // Bungkus semua data inputan dari halaman pengaturan
    const payload = {
      nama: $('#settingNama').value.trim(),
      peran: $('#settingPeran').value.trim(),
      email: $('#settingEmail').value.trim(),
      notifEmail: $('#toggleNotifEmail').checked,
      notifAktivitas: $('#toggleNotifAktivitas').checked
    };

    // Kirim data ke API backend dengan metode POST
    const response = await fetch('api-settings.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const resData = await response.json();
    if (resData.status === 'success') {
      // Ambil data terbaru dari database MySQL dan perbarui teks preview profil di layar
      state = await AmbisData.loadState(); 
      fillForm();
      toast('Profil berhasil disimpan ke database', 'success');
    } else {
      toast('Gagal memperbarui profil', 'danger');
    }
  });

  // Listener interaksi cepat bawaan tetap dipertahankan
  $('#toggleNotifEmail').addEventListener('change', () => toast('Preferensi notifikasi email diperbarui', 'info'));
  $('#toggleNotifAktivitas').addEventListener('change', () => toast('Preferensi notifikasi aktivitas diperbarui', 'info'));

  // Tombol Reset Data Lokal dialihkan untuk sekadar membersihkan sisa data sisa di browser jika ada
  $('#btnResetData').addEventListener('click', () => {
    if (!confirm('Yakin ingin membersihkan sisa data lokal di browser? Tindakan ini tidak menghapus data MySQL.')) return;
    localStorage.removeItem(AmbisData.STORAGE_KEY);
    toast('Data lokal browser berhasil dibersihkan', 'danger');
    setTimeout(() => window.location.href = 'index.php', 800);
  });
})();