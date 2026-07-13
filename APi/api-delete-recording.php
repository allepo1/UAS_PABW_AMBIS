<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);
$id = isset($data['id']) ? (int)$data['id'] : 0;

if ($id > 0) {
    try {
        // Ambil info file dulu agar bisa dihapus dari folder uploads
        $stmt = $pdo->prepare("SELECT file_path FROM recordings WHERE id = ?");
        $stmt->execute([$id]);
        $rec = $stmt->fetch();
        
        if ($rec) {
            if (file_exists($rec['file_path'])) {
                unlink($rec['file_path']); // Hapus file fisik rekaman
            }
            
            $stmtDelete = $pdo->prepare("DELETE FROM recordings WHERE id = ?");
            $stmtDelete->execute([$id]);
            echo json_encode(['status' => 'success']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
?>