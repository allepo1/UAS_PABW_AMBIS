<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ambis — Daftar Akun Baru</title>
  <link rel="stylesheet" href="css/style.css"> <!-- Memakai file CSS utama milik temanmu -->
  <style>
    .auth-container { max-width: 400px; margin: 80px auto; padding: 24px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .auth-title { text-align: center; margin-bottom: 24px; color: var(--navy); }
    .auth-footer { text-align: center; margin-top: 16px; font-size: 13px; color: var(--text-muted); }
  </style>
</head>
<body style="background: #f4f6f9;">

  <div class="auth-container">
    <h2 class="auth-title">Daftar Akun Ambis</h2>
    
    <form id="formRegister">
      <div class="form-group">
        <label for="regNama">Nama Lengkap</label>
        <input type="text" id="regNama" required placeholder="Contoh: Kepin Pratama">
      </div>
      <div class="form-group">
        <label for="regPeran">Peran / Program Studi</label>
        <input type="text" id="regPeran" required placeholder="Contoh: Mahasiswa TI">
      </div>
      <div class="form-group">
        <label for="regEmail">Email</label>
        <input type="email" id="regEmail" required placeholder="nama@student.ac.id">
      </div>
      <div class="form-group">
        <label for="regPassword">Password</label>
        <input type="password" id="regPassword" required placeholder="Buat password aman">
      </div>
      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 12px;">Daftar Sekarang</button>
    </form>

    <div class="auth-footer">
      Sudah punya akun? <a href="login.php" style="color: var(--navy); font-weight: bold;">Login di sini</a>
    </div>
  </div>

  <script>
    document.getElementById('formRegister').addEventListener('submit', async function(e) {
      e.preventDefault();

      const payload = {
        nama: document.getElementById('regNama').value,
        peran: document.getElementById('regPeran').value,
        email: document.getElementById('regEmail').value,
        password: document.getElementById('regPassword').value
      };

      try {
        const response = await fetch('api-register.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });

        const res = await response.json();
        if (res.status === 'success') {
          alert(res.message);
          window.location.href = 'login.php'; // Alihkan ke halaman login setelah sukses
        } else {
          alert(res.message);
        }
      } catch (error) {
        alert('Terjadi kesalahan jaringan.');
      }
    });
  </script>
</body>
</html>