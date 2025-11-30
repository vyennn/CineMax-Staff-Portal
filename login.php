<?php //login.php
// Start session FIRST
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CRITICAL: If already logged in, redirect to dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'db.php';
require_once 'security_logger.php';

$conn = getConnection();
$logger = new SecurityLogger();
$register_disabled = false;

// Session messages
if (isset($_GET['invalid']) && $_GET['invalid'] == 1) {
    $_SESSION['session_message'] = "Invalid session. Please log in again.";
}
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $_SESSION['session_message'] = "Session expired. You have been logged out.";
}

/* ------------------------
   INITIAL SESSION SETTINGS
------------------------- */
$_SESSION['login_attempts']     = $_SESSION['login_attempts']     ?? 0;
$_SESSION['lockout_time']       = $_SESSION['lockout_time']       ?? 0;
$_SESSION['user_blocked']       = $_SESSION['user_blocked']       ?? false;

$max_attempts       = 30;
$lockout_durations  = [15, 30, 60];

$login_error        = "";
$forgot_password_link = false;
$login_disabled     = false;
$remaining_lock_time = 0;

$current_time = time();

/* ------------------------
   IF ACCOUNT IS BLOCKED
------------------------- */
if ($_SESSION['user_blocked']) {
    $login_disabled = true;
    $login_error = "<div style='text-align: center; margin-top: 20px;'>Your account has been blocked due to too many failed attempts.</div>";
}

/* ------------------------
   IF USER IS CURRENTLY LOCKED OUT
------------------------- */
elseif ($_SESSION['lockout_time'] > $current_time) {
    $remaining_lock_time = $_SESSION['lockout_time'] - $current_time;
    $login_disabled = true;
    $login_error = "<div style='text-align: center; margin-top: 20px;'>Too many failed attempts.<br>Please wait for <span id='countdown'>$remaining_lock_time</span> seconds.</div>";
}

/* ------------------------
   LOGIN PROCESS (PDO VERSION)
------------------------- */
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // PDO QUERY - Changed from MySQLi
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    if (!$stmt) {
        die("DB Error: Query preparation failed");
    }
    
    // PDO EXECUTE - Changed from bind_param
    $stmt->execute([$username]);
    
    // PDO FETCH - Changed from bind_result
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $stored_password = $user['password'] ?? null;
    $user_exists = ($user !== false);

    $remaining_attempts_in_set = 2 - ($_SESSION['login_attempts'] % 3);

    if ($user_exists) {

        if (password_verify($password, $stored_password)) {
            // Successful login
            $_SESSION['username'] = $username;
            $_SESSION['login_attempts'] = 0;

            $logger->logLoginAttempt($username, true);

            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['login_attempts']++;
            $login_error = "<div style='text-align: center; margin-top: 20px;'>Invalid password.<br>You have $remaining_attempts_in_set remaining attempts.</div>";

            $logger->logLoginAttempt($username, false);
        }

    } else {
        $_SESSION['login_attempts']++;
        $login_error = "<div style='text-align: center; margin-top: 20px;'>Incorrect username or password.<br>You have $remaining_attempts_in_set remaining attempts.</div>";

        $logger->logLoginAttempt($username, false);
    }

    if ($_SESSION['login_attempts'] >= 2) {
        $forgot_password_link = true;
    }

    $set_index = floor($_SESSION['login_attempts'] / 3);
    if ($_SESSION['login_attempts'] % 3 == 0 && $set_index > 0) {
        $set_index = min($set_index, count($lockout_durations));
        $lock_duration = $lockout_durations[$set_index - 1] ?? 60;

        $_SESSION['lockout_time'] = $current_time + $lock_duration;
        $remaining_lock_time = $lock_duration;
        $login_disabled = true;

        $login_error = "<div style='text-align: center; margin-top: 20px;'>Too many failed attempts.<br>Please wait for <span id='countdown'>$remaining_lock_time</span> seconds.</div>";
    }

    if ($_SESSION['login_attempts'] >= $max_attempts) {
        $_SESSION['user_blocked'] = true;
        $login_disabled = true;
        $login_error = "<div style='text-align: center; margin-top: 20px;'>Your account has been blocked due to too many failed attempts.</div>";

        $logger->logSuspiciousActivity(
            "Account blocked after $max_attempts failed attempts",
            $username
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CineMax Movie Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script type="module" src="../javascript/login.js"></script>
    <script src="../javascript/disable_back_button.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }

        /* HEADER */
        header {
            background: rgba(0, 0, 0, 0.95);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 20px rgba(234, 88, 12, 0.3);
            border-bottom: 2px solid #ea580c;
        }

        nav {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
        }

        .logo span {
            color: #ea580c;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 15px;
            margin: 0;
            flex-wrap: wrap;
        }

        nav ul li a {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3);
        }

        nav ul li a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(234, 88, 12, 0.5);
        }

        .disabled-link {
            opacity: 0.5;
            pointer-events: none;
        }

        /* MAIN CONTAINER */
        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 40px 60px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            overflow: hidden;
            flex-wrap: wrap;
        }

        /* CONTENT */
        .content {
            flex: 1;
            color: white;
            padding-right: 40px;
            min-width: 300px;
        }

        .content h1 {
            font-size: 3rem;
            line-height: 1.2;
            margin-bottom: 20px;
            text-shadow: 2px 2px 15px rgba(234, 88, 12, 0.5);
        }

        .content .highlight {
            color: #ea580c;
            font-weight: bold;
        }

        .content p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-top: 15px;
        }

        .movie-icons {
            font-size: 2.5rem;
            margin-top: 20px;
            display: flex;
            gap: 20px;
        }

        /* FORM CONTAINER */
        .form-container {
            background: rgba(255, 255, 255, 0.98);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 450px;
            max-height: 90vh;
            overflow-y: auto;
            border: 2px solid rgba(234, 88, 12, 0.3);
        }

        /* FORM TEXT */
        .text h5 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .text h5 .icon {
            color: #ea580c;
        }

        /* FORM FIELDS */
        .field {
            margin-bottom: 22px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .required-asterisk {
            color: #dc2626;
            margin-left: 3px;
        }

        .field input {
            width: 100%;
            padding: 14px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .field input:focus {
            outline: none;
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
        }

        .field input:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            display: block;
            margin-top: 5px;
        }

        .show-password-container {
            margin-bottom: 25px;
        }

        .show-password-container label {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #555;
            cursor: pointer;
        }

        .show-password-container input[type="checkbox"] {
            margin-right: 8px;
            cursor: pointer;
        }

        /* BUTTON */
        .btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(234, 88, 12, 0.3);
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(234, 88, 12, 0.5);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* FORGOT PASSWORD */
        .forgot-reset-container {
            margin: 20px 0;
            text-align: center;
        }

        .forgot-password a {
            color: #ea580c;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .form-switch {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .form-switch a {
            color: #ea580c;
            text-decoration: none;
            font-weight: bold;
        }

        .form-switch a:hover {
            text-decoration: underline;
        }

        /* FOOTER */
        .footer {
            background: rgba(0, 0, 0, 0.95);
            color: white;
            text-align: center;
            padding: 15px;
            border-top: 2px solid #ea580c;
        }

        .footer p {
            margin: 5px 0;
            font-size: 14px;
        }

        .footer a {
            color: #ea580c;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* SCROLLBAR */
        .form-container::-webkit-scrollbar {
            width: 6px;
        }

        .form-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .form-container::-webkit-scrollbar-thumb {
            background: #ea580c;
            border-radius: 10px;
        }

        @media (max-width: 968px) {
    .main-container {
        flex-direction: column;
        padding: 20px 15px; /* Reduced padding */
        justify-content: center;
        align-items: center;
    }

    .content {
        text-align: center;
        padding-right: 0;
        margin-bottom: 25px;
        padding: 0 15px; /* Add horizontal padding */
    }

    .content h1 {
        font-size: 2rem; /* More readable size */
        line-height: 1.3;
    }

    .content p {
        font-size: 1rem; /* Adjusted */
    }

    .movie-icons {
        font-size: 1.8rem; /* Slightly smaller */
        justify-content: center;
    }

    .form-container {
        width: 100%;
        max-width: 420px;
        padding: 25px;
    }

    nav {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px; /* Add padding */
    }

    .logo {
        font-size: 1.4rem; /* Reduced */
    }

    nav ul {
        flex-direction: row;
        width: auto;
        gap: 8px;
    }

    nav ul li a {
        padding: 8px 14px;
        font-size: 12px;
    }

    .field input {
        padding: 12px; /* Slightly reduced */
    }
}

@media (max-width: 600px) {
    nav {
        padding: 0 15px;
    }

    .logo {
        font-size: 1.2rem;
        gap: 5px;
    }

    nav ul {
        gap: 6px;
    }

    nav ul li a {
        padding: 7px 12px;
        font-size: 11px;
        gap: 4px;
    }

    nav ul li a i {
        font-size: 10px;
    }

    .content h1 {
        font-size: 1.6rem;
    }

    .content p {
        font-size: 0.9rem;
    }

    .movie-icons {
        font-size: 1.5rem;
    }

    .form-container {
        padding: 20px;
        max-width: 100%;
        margin: 0 10px; /* Add margin */
    }

    .text h5 {
        font-size: 1.6rem;
    }

    .btn {
        font-size: 15px;
        padding: 13px;
    }

    .field input {
        font-size: 13px;
        padding: 11px;
    }

    .field label {
        font-size: 13px;
    }

    .show-password-container label {
        font-size: 13px;
    }

    .main-container {
        padding: 15px 10px;
    }
}

@media (max-width: 400px) {
    .logo {
        font-size: 1rem;
    }

    nav ul li a {
        padding: 6px 10px;
        font-size: 10px;
    }

    .content h1 {
        font-size: 1.4rem;
    }

    .form-container {
        padding: 18px;
    }

    .text h5 {
        font-size: 1.4rem;
    }
}
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo"><span>🎬</span> CineMax <span>Portal</span></a> 
            <ul>
                <li><a href="index.php" id="homeLink" class="<?php if ($register_disabled) echo 'disabled-link'; ?>"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="registration.php" id="signupLink" class="<?php if ($register_disabled) echo 'disabled-link'; ?>"><i class="fas fa-user-plus"></i> Sign Up</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="main-container">
        <div class="content">
            <h1>Welcome Back to<br><span class="highlight">CineMax Portal</span></h1>
            <p>🎬 Manage customer bookings with ease</p>
            <p>🍿 Assist guests and ensure a smooth cinema experience</p>
            <div class="movie-icons">
                🎥 🎞️ 🎭 🎪
            </div>
        </div>
        
        <div class="form-container">
            <div class="text">
                <h5><span class="icon">🎟️</span> Login</h5>
            </div>
            <form id="login-form" method="POST">
                <div class="field">
                    <label for="username">Username<span class="required-asterisk">*</span></label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" <?php if ($login_disabled) echo 'disabled'; ?>>
                    <small id="username_error" class="error-message"></small>
                </div>

                <div class="field">
                    <label for="password">Password<span class="required-asterisk">*</span></label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" <?php if ($login_disabled) echo 'disabled'; ?>>
                    <small id="password_error" class="error-message"></small>
                </div>

                <div class="show-password-container">
                    <label for="showPassword">
                        <input class="pass" type="checkbox" id="showPassword"> Show Password
                    </label>
                </div>

                <button class="btn" type="submit" id="loginButton" <?php if ($login_disabled) echo 'disabled'; ?>>🎬 Login</button>

                <div class="forgot-reset-container">
                    <?php if ($forgot_password_link): ?>
                        <div class="forgot-password">
                            <a href="forgot_password.php">Forgot Password?</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-switch">
                    <p>New to CineMax? <a href="registration.php" id="registerLink" <?php if ($register_disabled) echo 'class="disabled-link"'; ?>>Create Account</a></p>    
                </div>

                <?php if (!empty($login_error)): ?>
                    <div id="loginError" class="error-message"><?php echo $login_error; ?></div>
                <?php endif; ?>   
            </form>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 CineMax Portal. All Rights Reserved.</p>
        <p>Designed for seamless movie booking management</p>
    </div>

    <script>
        var remainingLockTime = <?php echo $remaining_lock_time; ?>;
    </script>

    <script src="../javascript/disable.js"></script>
    <script src="../javascript/showpass.js"></script>
    <script src="../javascript/reset.js"></script>
    <script src="../javascript/login_username.js"></script>
    <script src="../javascript/login_password.js"></script>
</body>
</html>