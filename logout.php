<?php
session_start();
$_SESSION = [];
session_destroy();

// Bunuh cookie-nya
setcookie("ambis_jwt", "", time() - 3600, "/"); 

header("Location: login.php");
exit;
?>