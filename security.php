<?php
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validate_username($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

function validate_email_format($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) &&
           preg_match('/@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
}

function validate_idno($idno) {
    return preg_match('/^\d{4}-\d{4}$/', $idno);
}
?>
