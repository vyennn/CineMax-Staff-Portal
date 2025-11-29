<?php
session_start();
session_unset();
session_destroy();
setcookie('PHPSESSID', '', time() - 3600, '/');
echo "<h1>✅ Session Cleared!</h1>";
echo "<p>You are now logged out.</p>";
echo "<p><a href='home.php'>Go to Home</a></p>";
?>