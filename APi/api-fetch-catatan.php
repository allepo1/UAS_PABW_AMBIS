<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

require_once 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sesi hilang, buat backup sesi tester (ganti angka 1 dengan ID user kawanmu jika tahu)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit; 
}

$user_id = $_SESSION['user_id'];
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

try {
    if (!empty($kategori)) {
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE user_id = ? AND type = ? ORDER BY date DESC, id DESC");
        $stmt->execute([$user_id, $kategori]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY date DESC, id DESC");
        $stmt->execute([$user_id]);
    }
    
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$notes) {
        $notes = [];
    }

    // Paksa kirim dua format (format murni array dan format object status) 
    // agar dibaca valid oleh js/data.js maupun js/catatan.js
    echo json_encode([
        'status' => 'success',
        'notes' => $notes,
        'data' => ['notes' => $notes]
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'notes' => [], 'data' => ['notes' => []]]);
}
?>