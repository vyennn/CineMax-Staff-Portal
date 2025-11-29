<?php
// db.php - Database connection

function getConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "YourStrongPassword123!";  // WITH the ! 
    $dbname = "Basalo_101";

    try {
        // Enable mysqli error reporting
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Set charset
        $conn->set_charset("utf8mb4");
        
        return $conn;
        
    } catch (mysqli_sql_exception $e) {
        // Log the actual error
        $log_file = __DIR__ . '/../logs/php_errors.log';
        if (!is_dir(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }
        error_log(date('[Y-m-d H:i:s]') . " [DB_ERROR] " . $e->getMessage() . "\n", 3, $log_file);
        
        // Show generic error to user
        http_response_code(503);
        die('<!DOCTYPE html>
        <html>
        <head>
            <title>Service Unavailable</title>
            <style>
                body { font-family: Arial; text-align: center; padding: 50px; background: #f5f5f5; }
                .error-container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto; }
                h1 { color: #e74c3c; }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>🔌 Database Connection Error</h1>
                <p>Unable to connect to the database. Please try again later.</p>
                <p><small>If you are the administrator, check the error logs.</small></p>
            </div>
        </body>
        </html>');
    }
}

// Create global $conn for backward compatibility
$conn = getConnection();
?>