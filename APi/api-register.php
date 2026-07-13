<?php
header('Content-Type: application/json');
require_once 'includes/koneksi.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
    exit;
}

$nama  = trim($input['nama']);
$peran = trim($input['peran']);
$email = trim($input['email']);
$password = $input['password'];

// Validasi input sederhana
if (empty($nama) || empty($peran) || empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi!']);
    exit;
}

try {
    // 1. Cek apakah email sudah terdaftar sebelumnya
    $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmtCheck->execute([$email]);
    if ($stmtCheck->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Email ini sudah terdaftar!']);
        exit;
    }

    // 2. Amankan password menggunakan bcrypt (Blowfish)
    $passwordTerenskripsi = password_hash($password, PASSWORD_BCRYPT);

    // 3. Masukkan data user baru ke database
    $stmtInsert = $pdo->prepare("INSERT INTO users (nama, peran, email, password) VALUES (?, ?, ?, ?)");
    $stmtInsert->execute([$nama, $peran, $email, $passwordTerenskripsi]);

    echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil! Silakan login.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal mendaftar: ' . $e->getMessage()]);
}
?>