<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

try {
    $stmt = $pdo->prepare("SELECT nama, program_studi, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'settings' => [
            'nama' => isset($user['nama']) ? $user['nama'] : '',
            'peran' => isset($user['program_studi']) ? $user['program_studi'] : '',
            'email' => isset($user['email']) ? $user['email'] : '',
            'notifEmail' => 0,
            'notifAktivitas' => 0
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>