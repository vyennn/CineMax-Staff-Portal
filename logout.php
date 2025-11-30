<?php
// logout.php - Properly destroy session and redirect
session_start();

// Include security logger if you want to log logouts
require_once 'security_logger.php';
$logger = new SecurityLogger();

// Log the logout event
if (isset($_SESSION['username'])) {
    $logger->logLogout($_SESSION['username']);
}

// Destroy all session data
session_unset();
session_destroy();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Redirect to home page
header("Location: index.php");
exit();
?>