<?php
require 'db.php';

// Fetch all users to update passwords
$result = $conn->query("SELECT id, password FROM users");

while ($row = $result->fetch_assoc()) {
    $userId = $row['id'];
    $plainPassword = $row['password'];
    
    // Check if the password is already hashed (bcrypt hashes are 60 characters long)
    if (strlen($plainPassword) != 60) {
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password = '$hashedPassword' WHERE id = $userId");
    }
}

echo "Passwords have been hashed and updated.";
$conn->close();
?>
