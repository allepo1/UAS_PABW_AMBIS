<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$id = isset($data['id']) ? (int)$data['id'] : 0;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID catatan tidak valid.']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);

    echo json_encode(['status' => 'success', 'message' => 'Catatan berhasil dihapus!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus: ' . $e->getMessage()]);
}
?>