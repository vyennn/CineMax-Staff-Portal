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
    define('DB_PORT', isset($url['port']) ? $url['port'] : 5432);
    define('DB_TYPE', 'pgsql'); // PostgreSQL on Render
} else {
    // Local development fallback
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', 'YourStrongPassword123!');
    define('DB_NAME', 'Basalo_101');
    define('DB_PORT', 3306);
    define('DB_TYPE', 'mysql'); // MySQL locally
}

// Create PDO connection
function getConnection() {
    try {
        $dsn = DB_TYPE . ":host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        
        if (DB_TYPE === 'mysql') {
            $dsn .= ";charset=utf8mb4";
        }
        
        $conn = new PDO($dsn, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return $conn;
    } catch(PDOException $e) {
        error_log("Connection failed: " . $e->getMessage());
        die("Connection failed. Please check database configuration.");
    }
}

// Helper function to get current timestamp
function getCurrentTimestamp() {
    if (DB_TYPE === 'pgsql') {
        return "CURRENT_TIMESTAMP";
    }
    return "NOW()";
}
?>