<?php
$pageTitle = 'Semua Catatan';
$activePage = 'catatan';
include 'includes/header.php';
include 'includes/sidebar.php';

// Query string opsional dari halaman lain, dibaca ulang oleh catatan.js
$kategoriAwal = isset($_GET['kategori']) ? htmlspecialchars($_GET['kategori'], ENT_QUOTES) : '';
$cariAwal = isset($_GET['cari']) ? htmlspecialchars($_GET['cari'], ENT_QUOTES) : '';
?>

<!-- STYLE CSS MODAL -->
<style>
  .modal {
    display: none; 
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
  }

  .modal.show {
    display: flex !important;
    align-items: center;
    justify-content: center;
  }

  .modal-content {
    background: #ffffff;
    padding: 24px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    animation: modalFadeIn 0.3s ease-out;
  }

  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 12px;
    margin-bottom: 20px;
  }

  .modal-header h3 {
    font-size: 1.25rem;
    color: #1e3a8a;
    margin: 0;
  }

  .close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #94a3b8;
  }

  .close-btn:hover {
    color: #ef4444;
  }

  .form-group {
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    text-align: left;
  }

  .form-group label {
    font-weight: 600;
    font-size: 0.875rem;
    color: #475569;
  }

  .form-group input[type="text"],
  .form-group input[type="date"],
  .form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
    box-sizing: border-box;
  }

  .form-group input:focus,
  .form-group textarea:focus {
    border-color: #1e3a8a;
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
  }

  .form-group textarea {
    resize: vertical;
    min-height: 100px;
  }

  .category-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-top: 4px;
  }

  .cat-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 500;
  }

  .cat-option.active {
    border-color: #1e3a8a;
    background-color: #eff6ff;
    color: #1e3a8a;
  }

  .modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    border-top: 1px solid #e2e8f0;
    padding-top: 16px;
  }

  @keyframes modalFadeIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
</style>

<!-- KODE HALAMAN UTAMA -->
<main class="main">

  <div class="topbar">
    <div class="greeting">
      <h1><i class="fa-solid fa-note-sticky" style="color:var(--navy); margin-right:8px;"></i>Semua Catatan</h1>
      <p class="page-subtitle">Kelola seluruh catatan kuliahmu di satu tempat.</p>
    </div>
    <div class="topbar-right">
      <div class="search-wrap">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Cari catatan, mata kuliah, atau tanggal...">
      </div>
      <button class="btn btn-primary" id="btnCatatanBaru"><i class="fa-solid fa-plus"></i> Catatan Baru</button>
    </div>
  </div>

  <section class="section" id="sectionCatatan">
    <div class="filter-chip" id="filterChip">
      <span id="filterChipText">Kategori</span>
      <button id="filterChipClear" type="button"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="notes-grid" id="notesGrid"></div>

    <div class="empty-state" id="notesEmptyState">
      <i class="fa-regular fa-folder-open"></i>
      <strong>Belum ada catatan</strong>
      <span>Coba ubah kata kunci pencarian, atau buat catatan baru.</span>
    </div>
  </section>

  <!-- MODAL TAMBAH / EDIT CATATAN -->
  <div id="modalNote" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalNoteTitle"><i class="fa-solid fa-note-sticky"></i> Catatan Baru</h3>
        <button id="closeModalNote" class="close-btn">&times;</button>
      </div>
      <form id="formNote">
        <input type="hidden" id="noteId" name="id">
        
        <div class="form-group">
          <label for="noteTitle">Judul Catatan</label>
          <input type="text" id="noteTitle" required placeholder="Masukkan judul catatan...">
        </div>

        <div class="form-group">
          <label>Kategori</label>
          <div class="category-options">
            <label class="cat-option" data-type="a"><input type="radio" name="type" value="a" checked> Pemrograman Web</label>
            <label class="cat-option" data-type="b"><input type="radio" name="type" value="b"> Jaringan Komputer</label>
            <label class="cat-option" data-type="c"><input type="radio" name="type" value="c"> Basis Data</label>
            <label class="cat-option" data-type="d"><input type="radio" name="type" value="d"> Matematika Komputasi</label>
          </div>
        </div>

        <div class="form-group">
          <label for="notePreview">Isi Catatan</label>
          <textarea id="notePreview" required placeholder="Tulis isi catatan kamu di sini..."></textarea>
        </div>

        <div class="form-group">
          <label for="noteDate">Tanggal</label>
          <input type="date" id="noteDate" required>
        </div>

        <div class="modal-footer">
          <button type="button" id="cancelModalNote" class="btn btn-secondary">Batal</button>
          <button type="submit" id="submitModalNote" class="btn btn-primary">Simpan Catatan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL HAPUS CATATAN -->
  <div id="modalDelete" class="modal">
    <div class="modal-content">
      <h3>Konfirmasi Hapus</h3>
      <p>Apakah kamu yakin ingin menghapus catatan <strong id="deleteNoteTitle"></strong>?</p>
      <div class="modal-footer">
        <button type="button" id="cancelModalDelete" class="btn btn-secondary">Batal</button>
        <button type="button" id="confirmModalDelete" class="btn btn-danger">Hapus</button>
      </div>
    </div>
  </div>

</main>

<script>
  window.AMBIS_INIT = { kategori: <?= json_encode($kategoriAwal) ?>, cari: <?= json_encode($cariAwal) ?> };
</script>

<?php
// Menyambungkan file JS eksternal
$pageScripts = ['js/catatan.js?v=1.2'];
include 'includes/footer.php';
?>