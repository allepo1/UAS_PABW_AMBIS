<?php
$pageTitle = 'Kategori';
$activePage = 'kategori';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<main class="main">

  <div class="topbar">
    <div class="greeting">
      <h1><i class="fa-solid fa-tags" style="color:var(--navy); margin-right:8px;"></i>Kategori Mata Kuliah</h1>
      <p class="page-subtitle">Kategori mengikuti mata kuliah yang dipakai untuk menandai catatanmu.</p>
    </div>
  </div>

  <section class="section">
    <div class="kategori-grid" id="kategoriGrid"></div>
  </section>

</main>

<?php
$pageScripts = ['js/kategori.js'];
include 'includes/footer.php';
?>
