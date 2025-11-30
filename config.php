<?php
// Database configuration for Render deployment
$db_url = getenv('DATABASE_URL'); // Render provides this

if ($db_url) {
    // Parse Render's DATABASE_URL
    $url = parse_url($db_url);
    define('DB_HOST', $url['host']);
    define('DB_USER', $url['user']);
    define('DB_PASS', $url['pass']);
    define('DB_NAME', ltrim($url['path'], '/'));
} else {
    // Local development fallback
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', 'YourStrongPassword123!');
    define('DB_NAME', 'Basalo_101');
}

// Create connection
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        die("Connection failed. Please check database configuration.");
    }
    
    return $conn;
}
?>