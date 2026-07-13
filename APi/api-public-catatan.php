<?php
// Izinkan akses dari semua domain luar (CORS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');

include 'includes/koneksi.php';


$api_key = isset($_GET['api_key']) ? trim($_GET['api_key']) : '';


if ($api_key !== 'ambis-public-2026') {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Akses ditolak. API Key tidak valid.'
    ]);
    exit;
}

try {
    
    $stmt = $pdo->prepare("SELECT id, title, type, preview, date FROM notes ORDER BY date DESC LIMIT 10");
    $stmt->execute();
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'total_data' => count($notes),
        'data' => $notes
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada database.']);
}
?>