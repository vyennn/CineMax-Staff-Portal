<?php
// test_connection.php - Test database connection
echo "<h2>Database Connection Test</h2>";
echo "<pre>";

// Check for DATABASE_URL
$databaseUrl = getenv('DATABASE_URL');
if ($databaseUrl) {
    echo "✅ DATABASE_URL found\n";
    echo "First 30 chars: " . substr($databaseUrl, 0, 30) . "...\n\n";
    
    // Try to connect
    try {
        $dbParts = parse_url($databaseUrl);
        $dsn = "pgsql:host={$dbParts['host']};port=" . ($dbParts['port'] ?? 5432) . ";dbname=" . ltrim($dbParts['path'], '/') . ";sslmode=require";
        $conn = new PDO($dsn, $dbParts['user'], $dbParts['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        echo "✅ PostgreSQL Connection: SUCCESS!\n";
        echo "Database: " . ltrim($dbParts['path'], '/') . "\n";
    } catch (PDOException $e) {
        echo "❌ PostgreSQL Connection: FAILED\n";
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    // Check individual variables
    echo "DATABASE_URL: NOT FOUND\n\n";
    echo "Checking individual variables:\n";
    echo "DB_HOST: " . (getenv('DB_HOST') ?: '❌ NOT SET') . "\n";
    echo "DB_PORT: " . (getenv('DB_PORT') ?: '❌ NOT SET') . "\n";
    echo "DB_NAME: " . (getenv('DB_NAME') ?: '❌ NOT SET') . "\n";
    echo "DB_USER: " . (getenv('DB_USER') ?: '❌ NOT SET') . "\n";
    echo "DB_PASS: " . (getenv('DB_PASS') ? '✅ SET' : '❌ NOT SET') . "\n";
}

echo "</pre>";
?>