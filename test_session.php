<?php
session_start();
echo "<h1>Session Debug Info</h1>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p><a href='login.php'>Go to Login</a></p>";
echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
?>