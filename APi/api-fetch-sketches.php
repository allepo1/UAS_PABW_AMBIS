<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

try {
    $stmt = $pdo->prepare("SELECT * FROM sketches WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    $sketches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'sketches' => $sketches]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage(), 'sketches' => []]);
}
?>