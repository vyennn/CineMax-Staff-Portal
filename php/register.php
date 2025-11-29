<?php
session_start();
include("db.php");
include("csrf_protection.php");
include("security_logger.php");

$logger = new SecurityLogger();

// Validate CSRF token FIRST
if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
    die("Invalid request. CSRF token validation failed.");
}

if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// SANITIZE INPUT FUNCTION
function sanitize_input($data) {
    return trim(stripslashes($data));
}

// VALIDATION FUNCTIONS
function validate_username($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) && 
           preg_match('/@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
}

function validate_idno($idno) {
    return preg_match('/^\d{4}-\d{4}$/', $idno);
}

// SANITIZE ALL INPUTS
$idno = sanitize_input($_POST['id'] ?? '');
$firstname = sanitize_input($_POST['firstname'] ?? '');
$middlename = sanitize_input($_POST['middlename'] ?? '');
$lastname = sanitize_input($_POST['lastname'] ?? '');
$suffix = ($_POST['suffix'] === "custom") ? sanitize_input($_POST['custom-input']) : sanitize_input($_POST['suffix']);
$sex = sanitize_input($_POST['sex'] ?? '');
$email = strtolower(sanitize_input($_POST['email'] ?? ''));
$username = strtolower(sanitize_input($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';
$repassword = $_POST['repassword'] ?? '';
$birthdate = sanitize_input($_POST['birthdate'] ?? '');
$purok = sanitize_input($_POST['purok'] ?? '');
$barangay = sanitize_input($_POST['barangay'] ?? '');
$municipality = sanitize_input($_POST['municipality'] ?? '');
$province = sanitize_input($_POST['province'] ?? '');
$country = sanitize_input($_POST['country'] ?? '');
$zipcode = sanitize_input($_POST['zipcode'] ?? '');

$errors = [];

// USERNAME VALIDATION
if (!validate_username($username)) {
    $errors['username'] = "Username must be 3-20 characters, alphanumeric only.";
}

// IDNO VALIDATION
if (!validate_idno($idno)) {
    $errors['idno'] = "ID must be in format: xxxx-xxxx";
}

// REQUIRED FIELDS VALIDATION
$required_fields = ['idno', 'firstname', 'lastname', 'sex', 'email', 'username', 'password', 'repassword', 'birthdate'];
foreach ($required_fields as $field) {
    if (empty($$field)) {
        $errors[$field] = ucfirst($field) . " is required.";
    }
}

// PASSWORD VALIDATION
if (!isset($errors['password']) && !isset($errors['repassword'])) {
    if ($password !== $repassword) {
        $errors['password_mismatch'] = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $errors['password_weak'] = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors['password_weak'] = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors['password_weak'] = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password_weak'] = "Password must contain at least one number.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }
}

// EMAIL VALIDATION
if (!isset($errors['email']) && !validate_email($email)) {
    $errors['invalid_email'] = "Invalid email address.";
}

// BIRTHDATE VALIDATION
if (!isset($errors['birthdate'])) {
    $birthdateObj = DateTime::createFromFormat('Y-m-d', $birthdate);
    if (!$birthdateObj || $birthdateObj->format('Y-m-d') !== $birthdate) {
        $errors['invalid_birthdate'] = "Invalid birthdate format.";
    } else {
        $today = new DateTime();
        $age = $today->diff($birthdateObj)->y;
    }
} else {
    $age = null;
}

// DUPLICATE CHECK FUNCTION
function check_duplicate($conn, $field, $value, &$errors) {
    $value = trim($value);
    error_log("Checking duplicate $field: '$value'");
    $query = $conn->prepare("SELECT $field FROM users WHERE TRIM($field) = ?");
    if (!$query) {
        die("Prepare failed: " . $conn->error);
    }
    $query->bind_param("s", $value);
    $query->execute();
    $query->store_result();
    if ($query->num_rows > 0) {
        $errors[$field . '_exists'] = ucfirst($field) . " already exists.";
    }
    $query->close();
}

// CHECK DUPLICATES
if (!isset($errors['email'])) check_duplicate($conn, 'email', $email, $errors);
if (!isset($errors['username'])) check_duplicate($conn, 'username', $username, $errors);
if (!isset($errors['idno'])) check_duplicate($conn, 'idno', $idno, $errors);

// HANDLE ERRORS
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header("Location: registration.php");
    exit();
}

// INSERT NEW USER
$stmt = $conn->prepare("INSERT INTO users (
    idno, firstname, middlename, lastname, suffix, sex, email, username, password, birthdate, age, purok, barangay, municipality, province, country, zipcode
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Statement preparation failed: " . $conn->error);
}

$stmt->bind_param(
    "sssssssssssssssss",
    $idno, $firstname, $middlename, $lastname, $suffix, $sex, $email,
    $username, $hashed_password, $birthdate, $age, $purok, $barangay,
    $municipality, $province, $country, $zipcode
);

if ($stmt->execute()) {
    $logger->logRegistration($username, $email);
    $_SESSION['success'] = "Registration successful.";
} else {
    $_SESSION['error'] = "Registration failed. Please try again.";
}

$stmt->close();
$conn->close();

header("Location: registration.php");
exit();
?>
