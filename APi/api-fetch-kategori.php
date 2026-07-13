<?php
ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include 'includes/koneksi.php';

$user_id = 1;

$counts = [
    'a' => 0,
    'b' => 0,
    'c' => 0,
    'd' => 0
];

try {
    if (isset($pdo) && $pdo !== null) {
        $stmt = $pdo->prepare("SELECT type, COUNT(*) as total FROM notes WHERE user_id = ? GROUP BY type");
        $stmt->execute([$user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($rows as $row) {
            $type = $row['type'];
            if (array_key_exists($type, $counts)) {
                $counts[$type] = (int)$row['total'];
            }
        }
    }
} catch (Throwable $e) {
    // Abaikan error agar tidak merusak format JSON
}

ob_clean();

echo json_encode([
    'status' => 'success',
    'counts' => $counts
]);
exit;
?>