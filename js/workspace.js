/* =========================================================
   AMBIS — WORKSPACE (VERSI CONNECT DATABASE)
   ========================================================= */
(async function () { // DIUBAH: Menambahkan kata kunci 'async'
  'use strict';
  
  // DIUBAH: Menunggu data dimuat secara asinkronus dari MySQL via API PHP
  const state = await AmbisData.loadState();
  const { CATEGORY } = AmbisData;

  Object.keys(CATEGORY).forEach(type => {
    const cat = CATEGORY[type];
    const count = state.notes.filter(n => n.type === type).length;
    const el = document.getElementById(cat.ws);
    if (el) el.textContent = `${count} catatan`;
  });
})();