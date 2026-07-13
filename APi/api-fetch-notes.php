<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

try {
    // Ambil seluruh catatan milik user yang sedang login
    $stmt = $pdo->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY date DESC, id DESC");
    $stmt->execute([$user_id]);
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'notes' => $notes
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'notes' => []
    ]);
}
?>