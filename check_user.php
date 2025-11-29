<?php
// Database connection parameters
$host = "localhost"; // MySQL server address
$db_username = "root"; // MySQL username
$db_password = "YourStrongPassword123!"; // MySQL password
$db_name = "Basalo_101"; // Database name

// Create a new MySQLi connection
$con = new mysqli($host, $db_username, $db_password, $db_name);

// Check if the connection was successful
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error); // Exit if connection fails
}

// Get the username from the query string, defaulting to an empty string if not set
$username = isset($_GET['username']) ? $_GET['username'] : '';

if ($username !== '') { // Proceed only if a username is provided
    // Prepare a statement to check if the username exists
    $stmt = $con->prepare("SELECT COUNT(*) AS count FROM users WHERE username = ?");
    $stmt->bind_param("s", $username); // Bind the parameter

    $stmt->execute(); // Execute the prepared statement
    $result = $stmt->get_result(); // Get the result set
    $row = $result->fetch_assoc(); // Fetch the result as an associative array

    // Check if the username exists and return a JSON response
    if ($row['count'] > 0) {
        echo json_encode(['exists' => true]); // Username exists
    } else {
        echo json_encode(['exists' => false]); // Username does not exist
    }

    $stmt->close(); // Close the prepared statement
}

$con->close(); // Close the database connection
?>
