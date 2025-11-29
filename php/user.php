<?php //users.php
$require_login = true;
require_once 'secure_session.php';

header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "YourStrongPassword123!";
$dbname = "Basalo_101";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["username"])) {
    $check_username = $conn->real_escape_string($_POST["username"]);

    $sql = "SELECT * FROM users WHERE username = '$check_username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "exists"]);
    } else {
        echo json_encode(["status" => "available"]);
    }
}

$conn->close();
?>