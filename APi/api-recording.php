<?php
header('Content-Type: application/json');
require_once 'includes/koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'POST') {
        // PROSES SIMPAN REKAMAN
        $title = trim($input['title']) ?: 'Rekaman tanpa judul';
        $duration = $input['duration'];
        $audio = $input['audio']; // Data URL Base64 dari JS
        $date = date('Y-m-d');

        $stmt = $pdo->prepare("INSERT INTO recordings (title, date, duration, demo, audio) VALUES (?, ?, ?, 0, ?)");
        $stmt->execute([$title, $date, $duration, $audio]);

        // Catat Aktivitas
        $logText = "Rekaman <b>\"" . htmlspecialchars($title) . "\"</b> ditambahkan";
        $stmtLog = $pdo->prepare("INSERT INTO activities (icon, cls, text) VALUES ('fa-microphone', 'add', ?)");
        $stmtLog->execute([$logText]);

        echo json_encode(['status' => 'success']);
    } 
    
    else if ($method === 'DELETE') {
        // PROSES HAPUS REKAMAN
        $id = (int)$input['id'];

        // Ambil judul untuk log
        $stmtRec = $pdo->prepare("SELECT title FROM recordings WHERE id = ?");
        $stmtRec->execute([$id]);
        $rec = $stmtRec->fetch();

        if ($rec) {
            $title = $rec['title'];
            
            $stmtDel = $pdo->prepare("DELETE FROM recordings WHERE id = ?");
            $stmtDel->execute([$id]);

            // Catat Aktivitas
            $logText = "Rekaman <b>\"" . htmlspecialchars($title) . "\"</b> dihapus";
            $stmtLog = $pdo->prepare("INSERT INTO activities (icon, cls, text) VALUES ('fa-trash', 'delete', ?)");
            $stmtLog->execute([$logText]);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Rekaman tidak ditemukan']);
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>