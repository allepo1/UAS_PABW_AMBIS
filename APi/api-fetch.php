<?php
header('Content-Type: application/json');

// Mencegah PHP menampilkan eror HTML mentah yang merusak JSON
error_reporting(0); 
ini_set('display_errors', 0);

require_once 'includes/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika belum login, langsung kirim struktur data kosong yang valid (agar JS tidak crash)
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'notes' => [],
        'recordings' => [],
        'canvases' => [],
        'activities' => [],
        'settings' => [
            'nama' => 'Tamu',
            'peran' => 'Belum Login',
            'email' => '',
            'notifEmail' => false,
            'notifAktivitas' => false
        ]
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Inisialisasi array penampung
    $notes = [];
    $recordings = [];
    $canvases = [];
    $activities = [];

    // Gunakan try-catch mini di setiap query agar jika ada tabel yang belum dibuat di db-ambis, API tidak mati total (500)
    try {
        $stmtNotes = $pdo->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY date DESC, id DESC");
        $stmtNotes->execute([$user_id]);
        $notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {}

    try {
        $stmtRecordings = $pdo->prepare("SELECT * FROM recordings WHERE user_id = ? ORDER BY timestamp DESC");
        $stmtRecordings->execute([$user_id]);
        $recordings = $stmtRecordings->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {}

    try {
        $stmtCanvases = $pdo->prepare("SELECT * FROM canvases WHERE user_id = ? ORDER BY timestamp DESC");
        $stmtCanvases->execute([$user_id]);
        $canvases = $stmtCanvases->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {}

    try {
        $stmtActivities = $pdo->prepare("SELECT * FROM activities WHERE user_id = ? ORDER BY timestamp DESC LIMIT 10");
        $stmtActivities->execute([$user_id]);
        $activities = $stmtActivities->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {}

    $settings = [
        'nama' => isset($_SESSION['nama']) ? $_SESSION['nama'] : 'User',
        'peran' => isset($_SESSION['peran']) ? $_SESSION['peran'] : 'Mahasiswa',
        'email' => isset($_SESSION['email']) ? $_SESSION['email'] : '',
        'notifEmail' => true,
        'notifAktivitas' => true
    ];

    echo json_encode([
        'notes' => $notes,
        'recordings' => $recordings,
        'canvases' => $canvases,
        'activities' => $activities,
        'settings' => $settings
    ]);

} catch (Exception $e) {
    // Jika ada eror besar, tetap return format JSON agar JS di front-end tidak mogok kerja
    echo json_encode([
        'notes' => [],
        'recordings' => [],
        'canvases' => [],
        'activities' => [],
        'settings' => ['nama' => 'User']
    ]);
}
?>