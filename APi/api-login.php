<?php
header('Content-Type: application/json');
require_once 'includes/koneksi.php';

// Memulai session PHP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
    exit;
}

$email    = trim($input['email']);
$password = $input['password'];

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email dan password wajib diisi!']);
    exit;
}

try {
    // 1. Cari user berdasarkan email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. Jika user ditemukan, konversi dan cocokkan password-nya
    if ($user) {
        // Konversi ke array jika hasil fetch berupa objek demi keamanan indeks
        $userData = (array)$user;
        
        if (password_verify($password, $userData['password'])) {
            // 1. Buat Header
            $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
            
            // 2. Buat Payload (Data user & Waktu kadaluarsa)
            $payload = json_encode([
                'user_id' => $userData['id'],
                'nama'    => $userData['nama'],
                'peran'   => $userData['program_studi'],
                'exp'     => time() + (60 * 60 * 24) // Token mati dalam 1 hari
            ]);

            // Encode Base64Url (standar JWT nggak boleh ada karakter +, /, atau =)
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

            // 3. Buat Signature dengan HMAC-SHA256
            // Kunci rahasia ini idealnya ditaruh di file koneksi.php, jangan sampai bocor
            $secretKey = 'AmBis_S3cr3t_K3y_B4ng3t'; 
            
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secretKey, true);
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            // 4. Gabungkan ketiga bagian dengan titik
            $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

            // Kirim token ke frontend
            echo json_encode([
                'status'  => 'success', 
                'message' => 'Login berhasil!',
                'token'   => $jwt
            ]);
            exit;
        }
    }
    
    // Jika user tidak ditemukan atau password salah, langsung lempar respons ini
    echo json_encode(['status' => 'error', 'message' => 'Email atau password salah!']);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal memproses login: ' . $e->getMessage()]);
    exit;
}
?>