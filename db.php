<?php
// db.php - Database connection for Render (PostgreSQL) and local (MySQL)

function getConnection() {
    // Check if we're in production by looking for DATABASE_URL
    $databaseUrl = getenv('DATABASE_URL');
    
    if ($databaseUrl !== false && !empty($databaseUrl)) {
        // PRODUCTION (Render) - PostgreSQL
        // Parse the DATABASE_URL to extract connection details
        $dbParts = parse_url($databaseUrl);
        
        $host = $dbParts['host'];
        $port = isset($dbParts['port']) ? $dbParts['port'] : 5432;
        $dbname = ltrim($dbParts['path'], '/');
        $username = $dbParts['user'];
        $password = $dbParts['pass'];

        try {
            // Create PostgreSQL connection with SSL
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
            $conn = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
            return $conn;

        } catch (PDOException $e) {
            // Log the error
            $log_file = __DIR__ . '/../logs/php_errors.log';
            if (!is_dir(dirname($log_file))) { 
                @mkdir(dirname($log_file), 0755, true); 
            }
            $error_message = date('[Y-m-d H:i:s]') . " [DB_ERROR] [PRODUCTION] " . $e->getMessage() . "\n";
            @error_log($error_message, 3, $log_file);
            error_log("Database Connection Error: " . $e->getMessage());

            // Show user-friendly error page
            http_response_code(503);
            die('<!DOCTYPE html>
<html>
<head>
<title>Service Unavailable</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif;text-align:center;padding:50px 20px;background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;}
.error-container{background:white;padding:40px;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.5);max-width:500px;margin:0 auto;border:2px solid rgba(234,88,12,0.3);}
h1{color:#ea580c;margin-bottom:20px;font-size:2rem;}
p{color:#333;line-height:1.6;margin-bottom:15px;}
.emoji{font-size:3rem;margin-bottom:20px;}
small{color:#666;font-size:0.9rem;}
.back-button{display:inline-block;margin-top:20px;padding:12px 30px;background:linear-gradient(135deg,#ea580c 0%,#dc2626 100%);color:white;text-decoration:none;border-radius:8px;font-weight:600;transition:transform 0.3s;}
.back-button:hover{transform:translateY(-2px);}
</style>
</head>
<body>
<div class="error-container">
<div class="emoji">🔌</div>
<h1>Database Connection Error</h1>
<p>We\'re unable to connect to the database at the moment.</p>
<p>Please try again in a few moments.</p>
<small>If you are the administrator, check the Render logs for details.</small>
<br>
<a href="/index.php" class="back-button">← Back to Home</a>
</div>
</body>
</html>');
        }

    } else {
        // LOCAL DEVELOPMENT - MySQL
        $host = "localhost";
        $port = 3306;
        $dbname = "Basalo_101";
        $username = "root";
        $password = "YourStrongPassword123!";

        try {
            if (!extension_loaded('mysqli')) {
                throw new Exception("MySQLi extension is not loaded.");
            }
            
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $conn = new mysqli($host, $username, $password, $dbname, $port);
            
            if ($conn->connect_error) {
                throw new mysqli_sql_exception("Connection failed: " . $conn->connect_error);
            }
            
            if (!$conn->set_charset("utf8mb4")) {
                throw new mysqli_sql_exception("Error setting charset: " . $conn->error);
            }
            
            return $conn;
            
        } catch (Exception $e) {
            die("Local Database Connection Error: " . $e->getMessage());
        }
    }
}

// Create global connection
$conn = getConnection();
?>