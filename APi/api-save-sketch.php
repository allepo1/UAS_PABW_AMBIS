<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (isset($data['title']) && isset($data['image'])) {
    $title = trim($data['title']);
    $date = date('Y-m-d');
    
    // Decode string Base64 gambar dari canvas
    $imgData = $data['image'];
    $imgData = str_replace('data:image/png;base64,', '', $imgData);
    $imgData = str_replace(' ', '+', $imgData);
    $fileData = base64_decode($imgData);

    $fileName = 'sketch_' . time() . '_' . uniqid() . '.png';
    $targetPath = 'uploads/' . $fileName;

    if (file_put_contents($targetPath, $fileData)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO sketches (user_id, title, image_path, date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $title, $targetPath, $date]);
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menulis berkas gambar fisik.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
}
?>