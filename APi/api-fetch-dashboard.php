<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

try {
    // 1. Hitung total catatan
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE user_id = ?");
    $stmtTotal->execute([$user_id]);
    $totalCatatan = (int)$stmtTotal->fetchColumn();

    // 2. Hitung jumlah catatan per kategori/workspace
    $counts = ['a' => 0, 'b' => 0, 'c' => 0, 'd' => 0];
    $stmtGroup = $pdo->prepare("SELECT type, COUNT(*) as total FROM notes WHERE user_id = ? GROUP BY type");
    $stmtGroup->execute([$user_id]);
    foreach ($stmtGroup->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (array_key_exists($row['type'], $counts)) {
            $counts[$row['type']] = (int)$row['total'];
        }
    }

    // 3. Ambil 3 catatan terbaru untuk ditampilkan di grid
    $stmtLatest = $pdo->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY date DESC, id DESC LIMIT 3");
    $stmtLatest->execute([$user_id]);
    $latestNotes = $stmtLatest->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'total_catatan' => $totalCatatan,
        'counts' => $counts,
        'latest_notes' => $latestNotes
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'total_catatan' => 0,
        'counts' => ['a' => 0, 'b' => 0, 'c' => 0, 'd' => 0],
        'latest_notes' => []
    ]);
}
?>