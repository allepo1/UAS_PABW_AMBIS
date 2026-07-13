<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

if (isset($_FILES['audio_data']) && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $date = date('Y-m-d');
    
    // Bikin nama file unik
    $fileName = 'rec_' . time() . '_' . uniqid() . '.wav';
    $targetPath = 'uploads/' . $fileName;

    // Pindahkan rekaman dari memori browser ke folder uploads proyek
    if (move_uploaded_file($_FILES['audio_data']['tmp_path'] ?? $_FILES['audio_data']['tmp_name'], $targetPath)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO recordings (user_id, title, file_path, date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $title, $targetPath, $date]);
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah file rekaman ke folder uploads.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
}
?>