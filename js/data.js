/* =========================================================
   AMBIS — DATA LAYER BERSAMA (VERSI BACK-END DATABASE)
   Dipakai oleh semua halaman. Menggunakan Fetch API untuk
   sinkronisasi langsung dengan MySQL melalui PHP.
   ========================================================= */
(function (global) {
  'use strict';

  // Kategori tetap hardcoded di JS karena untuk keperluan UI & Styling warna
  const CATEGORY = {
    a: { label: 'Pemrograman Web',      tagClass: '',      topClass: '',   ws: 'wsCountA', base: 6, color: '#6F4E37' },
    b: { label: 'Jaringan Komputer',    tagClass: 'tag-b', topClass: 'c2', ws: 'wsCountB', base: 4, color: '#DB2777' },
    c: { label: 'Basis Data',           tagClass: 'tag-c', topClass: 'c3', ws: 'wsCountC', base: 3, color: '#0F766E' },
    d: { label: 'Matematika Komputasi', tagClass: 'tag-d', topClass: 'c4', ws: 'wsCountD', base: 2, color: '#B45309' },
  };

// Fungsi memuat data secara Asinkronus dari API PHP secara otomatis
  async function loadState() {
    try {
      const responseNotes = await fetch('api-fetch-catatan.php');
      const resultNotes = await responseNotes.json();
      
      const responseSettings = await fetch('api-fetch-settings.php');
      const resultSettings = await responseSettings.json();
      
      return { 
        notes: resultNotes.notes || [],
        settings: resultSettings.settings || { nama: '', peran: '', email: '' }
      };
    } catch (error) {
      console.error("Gagal load data:", error);
      return { 
        notes: [], 
        settings: { nama: '', peran: '', email: '' } 
      };
    }
  }

  // Fungsi penyeragaman penyimpanan data ke database (Optional Helper)
  function saveState(state) {
    // Karena sekarang setiap aksi (Add/Edit/Delete) langsung menembak API masing-masing,
    // fungsi saveState lokal ini bisa dikosongkan atau sekadar untuk log info.
    console.log("State disinkronkan ke server melalui API.");
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str == null ? '' : String(str);
    return div.innerHTML;
  }

  function formatDateLong(isoDate) {
    const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const d = new Date(isoDate + 'T00:00:00');
    if (isNaN(d)) return isoDate;
    return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
  }

  // Dieksport agar bisa dipakai di file JS lainnya
  global.AmbisData = {
    CATEGORY,
    loadState, 
    saveState,
    escapeHtml, 
    formatDateLong
  };
})(window);