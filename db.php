<?php
// db.php - Database connection (Works for both local and Render)

function getConnection() {
    // Check if we're on Render (production) or local development
    // Render will have DB_HOST environment variable set
    $isProduction = getenv('DB_HOST') !== false;
    
    if ($isProduction) {
        // PRODUCTION (Render) - Use environment variables
        $servername = getenv('DB_HOST');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASS');
        $dbname = getenv('DB_NAME');
        $port = getenv('DB_PORT') ?: 3306;
    } else {
        // LOCAL DEVELOPMENT - Use hardcoded values
        $servername = "localhost";
        $username = "root";
        $password = "YourStrongPassword123!";
        $dbname = "Basalo_101";
        $port = 3306;
    }

    try {
        // Check if MySQLi extension is loaded
        if (!extension_loaded('mysqli')) {
            throw new Exception("MySQLi extension is not loaded. Please install php-mysqli.");
        }

        // Enable mysqli error reporting
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        // Create connection with port
        $conn = new mysqli($servername, $username, $password, $dbname, $port);
        
        // Check connection
        if ($conn->connect_error) {
            throw new mysqli_sql_exception("Connection failed: " . $conn->connect_error);
        }
        
        // Set charset to prevent SQL injection via encoding
        if (!$conn->set_charset("utf8mb4")) {
            throw new mysqli_sql_exception("Error setting charset: " . $conn->error);
        }
        
        return $conn;
        
    } catch (Exception $e) {
        // Log the actual error
        $log_file = __DIR__ . '/../logs/php_errors.log';
        
        // Create logs directory if it doesn't exist
        if (!is_dir(dirname($log_file))) {
            @mkdir(dirname($log_file), 0755, true);
        }
        
        // Log error with timestamp and environment info
        $error_message = date('[Y-m-d H:i:s]') . " [DB_ERROR] ";
        $error_message .= ($isProduction ? "[PRODUCTION] " : "[LOCAL] ");
        $error_message .= $e->getMessage();
        $error_message .= " | Host: " . ($servername ?? 'N/A');
        $error_message .= " | DB: " . ($dbname ?? 'N/A') . "\n";
        
        @error_log($error_message, 3, $log_file);
        
        // Also log to PHP error log
        error_log("Database Connection Error: " . $e->getMessage());
        
        // Show generic error to user
        http_response_code(503);
        die('<!DOCTYPE html>
        <html>
        <head>
            <title>Service Unavailable</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    text-align: center; 
                    padding: 50px 20px; 
                    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .error-container { 
                    background: white; 
                    padding: 40px; 
                    border-radius: 20px; 
                    box-shadow: 0 20px 60px rgba(0,0,0,0.5); 
                    max-width: 500px; 
                    margin: 0 auto;
                    border: 2px solid rgba(234, 88, 12, 0.3);
                }
                h1 { 
                    color: #ea580c; 
                    margin-bottom: 20px;
                    font-size: 2rem;
                }
                p { 
                    color: #333; 
                    line-height: 1.6;
                    margin-bottom: 15px;
                }
                .emoji { font-size: 3rem; margin-bottom: 20px; }
                small { color: #666; font-size: 0.9rem; }
                .back-button {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 12px 30px;
                    background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: 600;
                    transition: transform 0.3s;
                }
                .back-button:hover {
                    transform: translateY(-2px);
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="emoji">🔌</div>
                <h1>Database Connection Error</h1>
                <p>We\'re unable to connect to the database at the moment.</p>
                <p>Please try again in a few moments.</p>
                <small>If you are the administrator, check the error logs at:<br><code>/logs/php_errors.log</code></small>
                <br>
                <a href="index.php" class="back-button">← Back to Home</a>
            </div>
        </body>
        </html>');
    }
}

// Create global $conn for backward compatibility
$conn = getConnection();
?>