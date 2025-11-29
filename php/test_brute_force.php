<?php
// Brute force test script (for testing ONLY)
$url = 'http://localhost/Basalo/login.php';
$username = 'testuser';

echo "Testing brute force protection...\n\n";

for ($i = 1; $i <= 10; $i++) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'username' => $username,
        'password' => 'wrongpassword' . $i
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo "Attempt $i: ";
    if (strpos($response, 'lockout') !== false || strpos($response, 'wait') !== false) {
        echo "LOCKED OUT ✓\n";
        break;
    } else {
        echo "Failed (no lockout yet)\n";
    }
    
    sleep(1); // Wait 1 second between attempts
}

echo "\nCheck logs/security.log for logged attempts\n";
?>