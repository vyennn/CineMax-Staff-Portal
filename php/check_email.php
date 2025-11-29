<?php
// Database connection parameters
$host = "localhost";
$db_username = "root";
$db_password = "YourStrongPassword123!";
$db_name = "Basalo_101";

$con = new mysqli($host, $db_username, $db_password, $db_name);

if ($con->connect_error) {
    die(json_encode(['error' => "Database connection failed: " . $con->connect_error]));
}

$email = isset($_GET['email']) ? trim($_GET['email']) : '';

if ($email === '') {
    echo json_encode(['error' => 'Email parameter is missing.']);
    $con->close();
    exit();
}

$stmt = $con->prepare("SELECT COUNT(*) AS count FROM users WHERE email = ?");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo json_encode(['exists' => $row['count'] > 0]);
} else {
    echo json_encode(['error' => 'Failed to execute query.']);
}

$stmt->close();
$con->close();
?>
