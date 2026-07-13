# AMBIS — Dashboard Catatan Kuliah (UAS PABW)

Aplikasi manajemen catatan kuliah berbasis Web (*Client-Server Architecture*). Proyek ini merupakan pembaruan dari versi *front-end statis* menjadi aplikasi dinamis penuh menggunakan **Database MySQL**, **PHP (PDO)**, dan Autentikasi **JWT (JSON Web Token)**.

## 🚀 Fitur Utama Sistem
- **Sistem Akun & Keamanan**: Menggunakan JWT yang disuntikkan ke dalam *Cookie* untuk sesi *login* yang aman, mencegah eksploitasi perpindahan halaman, dan menjaga isolasi data antar pengguna.
- **CRUD Catatan (Database)**: Buat, baca, perbarui, dan hapus catatan teks. Data tersimpan permanen di MySQL, bukan lagi di `localStorage`.
- **Rekaman Suara (*Server-Side*)**: Merekam audio penjelasan dosen langsung dari mikrofon (*MediaRecorder API*) dan mengunggahnya secara fisik ke *folder* `uploads/` di *server*.
- **Kanvas Digital**: Menggambar diagram/sketsa dengan dukungan layar sentuh (*Pointer Events API*) dan menyimpannya sebagai *Base64* ke dalam *database*.
- **Public API (CORS Enabled)**: Menyediakan *endpoint* khusus agar aplikasi/kelompok lain dapat menarik data dari sistem ini.


**Soal: 
Bagaimana cara aplikasi atau sistem dari kelompok lain menyambungkan (integrasi) ke aplikasi ini melalui API?**

Jawaban:
Kelompok lain bertindak sebagai Client dan hanya perlu melakukan HTTP Request ke Public Endpoint yang sudah disediakan oleh sistem ini. Berikut adalah spesifikasi teknis integrasinya:

Endpoint URL: http://localhost/ambis-dashboard/api-public-catatan.php?api_key=ambis-public-2026

HTTP Method: GET

Format Response: JSON (JavaScript Object Notation)

Contoh: 
implementasi murni di sisi frontend kelompok lain menggunakan 
JavaScript:

'''php

fetch('http://localhost/ambis-dashboard/api-public-catatan.php?api_key=ambis-public-2026')
  .then(response => response.json())
  .then(res => {
     if (res.status === 'success') {
         console.log("Total Data:", res.total_data);
         console.log("Isi Catatan:", res.data);
     }
  })

'''
## Struktur Folder

```text
ambis-dashboard/
├─ index.php           → Dashboard (ringkasan, statistik, aktivitas)
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
   ├─ dashboard.js     → Logic khusus Dashboard
   ├─ catatan.js       → CRUD & pencarian catatan
   ├─ workspace.js     → Hitung jumlah catatan per workspace
   ├─ kategori.js      → Render kartu kategori
   ├─ rekaman.js       → Perekam suara (MediaRecorder API)
   ├─ kanvas.js        → Kanvas gambar (Pointer Events API)
   └─ pengaturan.js    → Simpan profil & preferensi
   '''

##
