<?php
if (ob_get_level() === 0) { ob_start(); }

$host     = 'localhost';
$db_name  = 'db-ambis';
$username = 'root';
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    $current_script = basename($_SERVER['PHP_SELF']);
    if (strpos($current_script, 'api-') === 0) {
        register_shutdown_function(function() {
            $output = ob_get_clean();
            $json_start = strpos($output, '{');
            if ($json_start === false) { $json_start = strpos($output, '['); }
            if ($json_start !== false) { echo substr($output, $json_start); } 
            else { echo $output; }
        });
    }
} catch (PDOException $e) {
    die("Koneksi gagal");
}

// --- GLOBAL HACK SESSION DARI JWT ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$token = '';
if (isset($_COOKIE['ambis_jwt'])) {
    $token = $_COOKIE['ambis_jwt'];
} else {
    $authHeader = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : (function_exists('apache_request_headers') ? (apache_request_headers()['Authorization'] ?? '') : '');
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) { $token = $matches[1]; }
}

if (!empty($token)) {
    $tokenParts = explode('.', $token);
    if (count($tokenParts) === 3) {
        // Decode base64url standar JWT
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);
        if (isset($payload['user_id'])) {
            $_SESSION['user_id'] = $payload['user_id'];
            $_SESSION['nama'] = $payload['nama'] ?? 'Pengguna';
            $_SESSION['prodi'] = $payload['peran'] ?? 'Mahasiswa';
        }
    }
}
?>