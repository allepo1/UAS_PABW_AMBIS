# Ambis — Dashboard Catatan Kuliah

Struktur proyek ini dipecah dari satu file HTML menjadi folder PHP + CSS + JS terpisah,
dengan halaman sendiri untuk setiap menu di sidebar.

## Struktur Folder

```
ambis-dashboard/
├─ index.php          → Dashboard (ringkasan, statistik, aktivitas)
├─ catatan.php         → Semua Catatan (CRUD lengkap, cari & filter kategori)
├─ workspace.php       → Workspace per mata kuliah
├─ rekaman.php         → Rekaman Suara (perekam mikrofon nyata)
├─ kanvas.php          → Kanvas Digital (gambar/diagram, mouse & sentuhan)
├─ kategori.php        → Daftar kategori mata kuliah
├─ arsip.php           → Arsip (contoh empty state)
├─ pengaturan.php      → Profil & preferensi
├─ includes/
│  ├─ header.php       → <head> + pembuka layout
│  ├─ sidebar.php      → Navigasi sidebar (scrollable, active state otomatis)
│  ├─ footer.php       → Penutup layout + pemuatan script
│  └─ modal-note.php   → Modal tambah/edit/hapus catatan (dipakai di 2 halaman)
├─ css/
│  └─ style.css        → Semua style, termasuk versi mobile (sidebar jadi drawer)
└─ js/
   ├─ data.js          → Data & helper bersama (localStorage)
   ├─ main.js          → Toast, dropdown notifikasi, sidebar mobile, ripple
   ├─ dashboard.js      → Logic khusus Dashboard
   ├─ catatan.js         → CRUD & pencarian catatan
   ├─ workspace.js       → Hitung jumlah catatan per workspace
   ├─ kategori.js        → Render kartu kategori
   ├─ rekaman.js         → Perekam suara (MediaRecorder API)
   ├─ kanvas.js          → Kanvas gambar (Pointer Events API)
   └─ pengaturan.js      → Simpan profil & preferensi
```

## Cara Menjalankan

Butuh PHP terpasang di komputer (PHP 7.4+ atau 8.x).

```bash
cd ambis-dashboard
php -S localhost:8000
```

Lalu buka `http://localhost:8000` di browser.

Bisa juga dijalankan lewat XAMPP/Laragon: cukup salin folder `ambis-dashboard`
ke folder `htdocs`/`www`, lalu buka `http://localhost/ambis-dashboard`.

## Catatan Fitur

- **Data** disimpan di `localStorage` browser (bukan database), jadi konsisten
  saat pindah halaman tapi hanya tersimpan di perangkat/browser yang sama.
  Untuk direset, buka menu **Pengaturan → Reset Data Lokal**.
- **Rekaman Suara**: memakai `MediaRecorder` + `Web Audio API`, browser akan
  meminta izin akses mikrofon. Rekaman baru disimpan sebagai audio asli
  (bisa diputar ulang). Dua contoh rekaman awal sengaja ditandai "contoh"
  dan tidak memiliki file audio sungguhan.
- **Kanvas Digital**: memakai `Pointer Events API` sehingga bisa digambar
  dengan mouse, trackpad, maupun jari di layar sentuh. Kanvas otomatis
  menyesuaikan ukuran (responsif) tanpa merusak gambar yang sudah dibuat.
- Sidebar otomatis menjadi ikon-saja pada tablet, dan menjadi drawer
  (tombol hamburger) pada layar ponsel kecil, dengan daftar menu yang
  tetap bisa di-scroll bila panjang.
