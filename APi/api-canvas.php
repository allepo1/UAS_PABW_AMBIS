<?php
header('Content-Type: application/json');
require_once 'includes/koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'POST') {
        // PROSES SIMPAN KANVAS
        $title = trim($input['title']) ?: 'Kanvas tanpa judul';
        $thumb = $input['thumb']; // Gambar Base64 PNG dari Canvas HTML5
        $date = date('Y-m-d');

        $stmt = $pdo->prepare("INSERT INTO canvases (title, date, thumb) VALUES (?, ?, ?)");
        $stmt->execute([$title, $date, $thumb]);

        // Catat Aktivitas
        $logText = "Kanvas <b>\"" . htmlspecialchars($title) . "\"</b> disimpan";
        $stmtLog = $pdo->prepare("INSERT INTO activities (icon, cls, text) VALUES ('fa-pen-nib', 'create', ?)");
        $stmtLog->execute([$logText]);

        echo json_encode(['status' => 'success']);
    } 
    
    else if ($method === 'DELETE') {
        // PROSES HAPUS KANVAS
        $id = (int)$input['id'];

        $stmtCanvas = $pdo->prepare("SELECT title FROM canvases WHERE id = ?");
        $stmtCanvas->execute([$id]);
        $canvas = $stmtCanvas->fetch();

        if ($canvas) {
            $title = $canvas['title'];
            
            $stmtDel = $pdo->prepare("DELETE FROM canvases WHERE id = ?");
            $stmtDel->execute([$id]);

            // Catat Aktivitas
            $logText = "Kanvas <b>\"" . htmlspecialchars($title) . "\"</b> dihapus";
            $stmtLog = $pdo->prepare("INSERT INTO activities (icon, cls, text) VALUES ('fa-trash', 'delete', ?)");
            $stmtLog->execute([$logText]);

            echo json_encode(['status' => 'success']);
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>