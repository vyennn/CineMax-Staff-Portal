<?php
echo "<h1>Direct MySQL Connection Test</h1>";

$servername = "localhost";
$username = "root";

// Passwords to test
$passwords = [
    "YourStrongPassword123!",
    "YourStrongPassword123",
    "",
    "root",
    "password"
];

echo "<h2>Testing Passwords...</h2>";

$working = false;

foreach ($passwords as $password) {
    $displayPassword = empty($password) ? "(empty password)" : htmlspecialchars($password);
    echo "<p><strong>Testing:</strong> <code>{$displayPassword}</code> ... ";

    // Suppress errors with @ and handle exceptions
    mysqli_report(MYSQLI_REPORT_OFF); // Turn off default warnings
    $conn = @new mysqli($servername, $username, $password);

    if ($conn->connect_errno) {
        echo "<span style='color: red;'>❌ Failed: " . htmlspecialchars($conn->connect_error) . "</span></p>";
    } else {
        echo "<span style='color: green;'>✅ SUCCESS!</span></p>";
        echo "<hr><h2>🎉 Working Password Found!</h2>";
        echo "<p>Use this password in your <code>db.php</code>:</p>";
        echo "<pre>\$password = \"" . htmlspecialchars($password) . "\";</pre>";

        // Check database existence
        $result = $conn->query("SHOW DATABASES LIKE 'Basalo_101'");
        if ($result && $result->num_rows > 0) {
            echo "<p>✅ Database 'Basalo_101' exists!</p>";
        } else {
            echo "<p>⚠️ Database 'Basalo_101' does NOT exist!</p>";
        }

        $conn->close();
        $working = true;
        break;
    }
}

if (!$working) {
    echo "<hr><h2>❌ No Working Password Found</h2>";
    echo "<p>MySQL might not be running or your password is incorrect.</p>";

    // Check if MySQL is running
    echo "<h2>MySQL Status Check</h2>";
    $output = shell_exec('tasklist /FI "IMAGENAME eq mysqld.exe" 2>&1');
    if (strpos($output, 'mysqld.exe') !== false) {
        echo "<p>✅ MySQL process is running</p>";
    } else {
        echo "<p>❌ MySQL process is NOT running! Start it in XAMPP Control Panel.</p>";
    }
}
?>
