/* =========================================================
   AMBIS — KATEGORI (VERSI CONNECT DATABASE DENGAN API)
   ========================================================= */
(async function () {
  'use strict';

  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const { CATEGORY, escapeHtml } = AmbisData;

  // 1. Fungsi sapu jagat: potong semua teks bocor sebelum JSON dimulai
  async function fetchKategoriCounts() {
    try {
      const response = await fetch('api-fetch-kategori.php');
      let textData = await response.text(); 
      
      // Lacak di mana posisi karakter '{' pertama kali muncul
      const jsonStart = textData.indexOf('{');
      if (jsonStart !== -1) {
        textData = textData.substring(jsonStart); // Potong semua sampah teks di depannya!
      }
      
      const result = JSON.parse(textData); 
      return result.counts || { a: 0, b: 0, c: 0, d: 0 };
    } catch (e) {
      console.error("Gagal memuat count kategori:", e);
      return { a: 0, b: 0, c: 0, d: 0 };
    }
  }

  // Ambil data jumlah catatan terbaru
  const counts = await fetchKategoriCounts();

  // 2. Fungsi untuk me-render kotak-kotak kategori ke layar
  function renderKategoriGrid() {
    const grid = $('#kategoriGrid');
    if (!grid) return;

    // Ubah objek CATEGORY menjadi array lalu buat struktur HTML-nya
    const html = Object.keys(CATEGORY).map(key => {
      const cat = CATEGORY[key];
      const count = counts[key] || 0;

      return `
        <div class="kategori-card ${cat.topClass || ''}" style="border-top: 4px solid ${cat.color}; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 12px;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="note-tag ${cat.tagClass}" style="background-color: ${cat.color}20; color: ${cat.color}; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
              Kategori ${key.toUpperCase()}
            </span>
            <i class="fa-solid fa-folder" style="color: ${cat.color}; font-size: 1.25rem;"></i>
          </div>
          <h3 style="margin: 4px 0 0 0; font-size: 1.15rem; color: #1e293b;">${escapeHtml(cat.label)}</h3>
          <div style="margin-top: 8px; font-size: 0.875rem; color: #64748b; display: flex; align-items: center; gap: 6px;">
            <i class="fa-regular fa-file-lines"></i>
            <span><strong>${count}</strong> Catatan Kuliah</span>
          </div>
          <a href="catatan.php?kategori=${key}" class="btn" style="margin-top: 12px; border: 1px solid ${cat.color}; color: ${cat.color}; background: transparent; text-align: center; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 500; display: block; transition: all 0.2s;">
            Lihat Catatan <i class="fa-solid fa-arrow-right" style="margin-left: 4px; font-size: 0.75rem;"></i>
          </a>
        </div>
      `;
    }).join('');

    grid.innerHTML = html;
  }

  // Jalankan fungsi render
  renderKategoriGrid();
})();