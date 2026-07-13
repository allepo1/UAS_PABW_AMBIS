<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (isset($data['nama']) && isset($data['program_studi'])) {
    $nama = trim($data['nama']);
    $program_studi = trim($data['program_studi']);
    $email = isset($data['email']) ? trim($data['email']) : '';

    try {
        $stmt = $pdo->prepare("UPDATE users SET nama = ?, program_studi = ?, email = ? WHERE id = ?");
        $stmt->execute([$nama, $program_studi, $email, $user_id]);
        
        // Sinkronkan nama ke beberapa kunci session agar kompatibel
        $_SESSION['user_name'] = $nama;
        $_SESSION['nama'] = $nama;
        
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data input profil tidak lengkap.']);
}
?>