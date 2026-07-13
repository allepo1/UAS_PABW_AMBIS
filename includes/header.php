<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);


function getInitial($name) {
    return strtoupper(substr(trim($name), 0, 1)) ?: 'U';
}

if (!isset($pageTitle)) $pageTitle = 'Dashboard';
if (!isset($activePage)) $activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ambis — <?php echo htmlspecialchars($pageTitle); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
<script>
    if (!localStorage.getItem('ambis_jwt')) {
    window.location.href = 'login.php';
    }
</script>
</head>
<body>

<button class="mobile-toggle" id="mobileToggle" type="button" aria-label="Buka menu">
    <i class="fa-solid fa-bars"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="app">