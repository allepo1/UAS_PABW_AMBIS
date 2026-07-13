<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Ambis — Masuk Akun</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .auth-container { max-width: 400px; margin: 100px auto; padding: 24px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .auth-title { text-align: center; margin-bottom: 24px; color: var(--navy); }
    .auth-footer { text-align: center; margin-top: 16px; font-size: 13px; color: var(--text-muted); }
  </style>
</head>
<body style="background: #f4f6f9;">

  <div class="auth-container">
    <h2 class="auth-title">Masuk ke Ambis</h2>
    
    <form id="formLogin">
      <div class="form-group">
        <label for="loginEmail">Email</label>
        <input type="email" id="loginEmail" required placeholder="nama@student.ac.id">
      </div>
      <div class="form-group">
        <label for="loginPassword">Password</label>
        <input type="password" id="loginPassword" required placeholder="Masukkan password kamu">
      </div>
      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 12px;">Masuk</button>
    </form>

    <div class="auth-footer">
      Belum punya akun? <a href="register.php" style="color: var(--navy); font-weight: bold;">Daftar di sini</a>
    </div>
  </div>

  <script>
    document.getElementById('formLogin').addEventListener('submit', async function(e) {
      e.preventDefault();

      const payload = {
        email: document.getElementById('loginEmail').value,
        password: document.getElementById('loginPassword').value
      };

      try {
        const response = await fetch('api-login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });

        const res = await response.json();
        if (res.status === 'success') {
          // Tangkap token JWT dari server dan simpan ke memori browser
          localStorage.setItem('ambis_jwt', res.token);
          
          // INI BAGIAN NOMOR 2 YANG GUE MAKSUD (NENEM COOKIE):
          document.cookie = "ambis_jwt=" + res.token + "; path=/; max-age=86400";
          
          window.location.href = 'index.php'; 
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