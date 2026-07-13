<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$type = isset($data['type']) ? $data['type'] : '';
$id = isset($data['id']) ? (int)$data['id'] : 0;

if ($id > 0 && in_array($type, ['note', 'recording', 'sketch'])) {
    $table = '';
    if ($type === 'note') $table = 'notes';
    if ($type === 'recording') $table = 'recordings';
    if ($type === 'sketch') $table = 'sketches';

    try {
        // Ubah status is_archived menjadi 1 (Arsip)
        $stmt = $pdo->prepare("UPDATE {$table} SET is_archived = 1 WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak valid.']);
}
?>