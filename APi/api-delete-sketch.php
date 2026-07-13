<?php
header('Content-Type: application/json');
include 'includes/koneksi.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);
$id = isset($data['id']) ? (int)$data['id'] : 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM sketches WHERE id = ?");
        $stmt->execute([$id]);
        $sketch = $stmt->fetch();
        
        if ($sketch) {
            if (file_exists($sketch['image_path'])) {
                unlink($sketch['image_path']);
            }
            
            $stmtDelete = $pdo->prepare("DELETE FROM sketches WHERE id = ?");
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