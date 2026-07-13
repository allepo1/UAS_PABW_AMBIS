<?php
header('Content-Type: application/json');

include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default user_id jika sesi kosong
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$user_id = $_SESSION['user_id'];

// Ambil data dari POST request
$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$preview = isset($_POST['preview']) ? trim($_POST['preview']) : '';
$date = isset($_POST['date']) ? trim($_POST['date']) : '';

if (empty($title) || empty($type) || empty($preview) || empty($date)) {
    echo json_encode(['status' => 'error', 'message' => 'Semua data wajib diisi!']);
    exit;
}

try {
    if (!empty($id)) {
        // Jika ID ada, lakukan UPDATE (Edit)
        $stmt = $pdo->prepare("UPDATE notes SET title = ?, type = ?, preview = ?, date = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $type, $preview, $date, $id, $user_id]);
        $message = 'Catatan berhasil diperbarui!';
    } else {
        // Jika ID kosong, lakukan INSERT (Tambah Baru)
        $stmt = $pdo->prepare("INSERT INTO notes (user_id, title, type, preview, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $type, $preview, $date]);
        $message = 'Catatan baru berhasil ditambahkan!';
    }

    echo json_encode(['status' => 'success', 'message' => $message]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
}
?>