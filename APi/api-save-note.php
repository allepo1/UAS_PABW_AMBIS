<?php
header('Content-Type: application/json');

include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit; 
}

$user_id = $_SESSION['user_id'];

// Karena JS mengirim format JSON (Content-Type: application/json), kita baca lewat php://input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$id = isset($data['id']) ? trim($data['id']) : '';
$type = isset($data['type']) ? trim($data['type']) : '';
$title = isset($data['title']) ? trim($data['title']) : '';
$preview = isset($data['preview']) ? trim($data['preview']) : '';
$date = isset($data['date']) ? trim($data['date']) : '';

if (empty($title) || empty($type) || empty($preview) || empty($date)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

try {
    if (!empty($id)) {
        // Mode Edit (Update)
        $stmt = $pdo->prepare("UPDATE notes SET title = ?, type = ?, preview = ?, date = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $type, $preview, $date, $id, $user_id]);
        $msg = 'Catatan berhasil diperbarui!';
    } else {
        // Mode Tambah Baru (Insert)
        $stmt = $pdo->prepare("INSERT INTO notes (user_id, title, type, preview, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $type, $preview, $date]);
        $msg = 'Catatan baru berhasil ditambahkan!';
    }

    // DIUBAH: Tambahkan properti 'message' agar JavaScript modal tidak menampilkan 'undefined'
    echo json_encode(['status' => 'success', 'message' => $msg]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
}
?>