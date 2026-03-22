<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "cyclesense";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate CSRF token if not exists
if (session_status() !== PHP_SESSION_NONE && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
