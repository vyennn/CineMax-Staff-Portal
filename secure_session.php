<?php // secure_session.php
// Include this at the top of every page (protected pages set $require_login = true)

/* ==========================
   SESSION SECURITY SETTINGS
========================== */
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Use HTTPS cookies in production
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
} else {
    ini_set('session.cookie_secure', 0);
}

/* ==========================
   START SESSION
========================== */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ==========================
   SESSION FIXATION PREVENTION
========================== */
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

/* ==========================
   SESSION HIJACKING PROTECTION
========================== */
$user_signature = $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'];

if (!isset($_SESSION['signature'])) {
    $_SESSION['signature'] = $user_signature;
} else {
    if ($_SESSION['signature'] !== $user_signature) {
        session_unset();
        session_destroy();
        header("Location: login.php?invalid=1");
        exit();
    }
}

/* ==========================
   SESSION TIMEOUT
========================== */
$timeout_duration = 1800; // 30 minutes
if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > $timeout_duration
) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}

$_SESSION['last_activity'] = time();

/* ==========================
   REQUIRE LOGIN CHECK - ONLY CHECK IF VARIABLE IS SET
========================== */
if (isset($require_login) && $require_login === true) {
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
}

/* ==========================
   OPTIONAL: FORCE REGENERATE ID EVERY X MINUTES
========================== */
$regenerate_interval = 900; // 15 minutes
if (!isset($_SESSION['regen_time'])) {
    $_SESSION['regen_time'] = time();
} elseif (time() - $_SESSION['regen_time'] > $regenerate_interval) {
    session_regenerate_id(true);
    $_SESSION['regen_time'] = time();
}
?>