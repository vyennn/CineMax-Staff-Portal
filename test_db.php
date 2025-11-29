<?php
// test_db.php - Test database connection
echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a2e; color: #fff; }
        .success { color: #00ff00; }
        .error { color: #ff0000; }
        pre { background: #000; padding: 20px; border-radius: 8px; }
    </style>
</head>
<body>
<h1>🔍 Database Connection Test</h1>
<pre>";

// Check extensions
echo "=== PHP Extensions ===\n";
echo "PDO: " . (extension_loaded('pdo') ? "<span class='success'>✅ Installed</span>" : "<span class='error'>❌ Missing</span>") . "\n";
echo "PDO PostgreSQL: " . (extension_loaded('pdo_pgsql') ? "<span class='success'>✅ Installed</span>" : "<span class='error'>❌ Missing</span>") . "\n\n";

// Check DATABASE_URL
echo "=== Environment Variables ===\n";
$dbUrl = getenv('DATABASE_URL');
echo "DATABASE_URL: " . ($dbUrl ? "<span class='success'>✅ Set</span>" : "<span class='error'>❌ Not Set</span>") . "\n\n";

// Test connection
if ($dbUrl && extension_loaded('pdo_pgsql')) {
    echo "=== Connection Test ===\n";
    
    try {
        $dbParts = parse_url($dbUrl);
        $host = $dbParts['host'];
        $port = $dbParts['port'] ?? 5432;
        $dbname = ltrim($dbParts['path'], '/');
        $username = $dbParts['user'];
        $password = $dbParts['pass'];
        
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        $conn = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        echo "<span class='success'>✅ Connection: SUCCESS!</span>\n";
        echo "Database: $dbname\n";
        echo "Host: $host\n\n";
        
        // Test query
        echo "=== Testing Query ===\n";
        $stmt = $conn->query("SELECT version()");
        $version = $stmt->fetchColumn();
        echo "PostgreSQL Version:\n$version\n\n";
        
        echo "<span class='success'>🎉 Everything is working perfectly!</span>\n";
        
    } catch (PDOException $e) {
        echo "<span class='error'>❌ Connection Failed</span>\n";
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "<span class='error'>❌ Cannot test connection - check extensions and DATABASE_URL</span>\n";
}

echo "</pre>
<p><a href='/' style='color: #00ff00;'>← Back to Home</a></p>
</body>
</html>";
?>