<?php
session_start(); // Ensure the session starts at the beginning

// Check if the action is "reset"
if ($_POST['action'] == 'reset') {
    // Reset login attempts, lockout time, and blocked status
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_time'] = 0;
    $_SESSION['user_blocked'] = false; // Unblock the user

    // Debugging output: check session variables after reset
    // Remove this in production
    // var_dump($_SESSION);

    // Return a success response
    echo "Login attempts reset successfully.";
}
?>
