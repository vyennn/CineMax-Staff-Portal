<?php //registration.php
session_start();    
include('csrf_protection.php');

if (isset($_SESSION['form_data'])) {
    $_POST = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
}

$csrf_token = generateCSRFToken();
?>  

<!DOCTYPE html>
<html>
<head>
<title>Register - CineMax Movie Booking</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
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
    
    section {
        flex: 1;
        padding: 40px 20px;
    }
    
    .wrapper {
        max-width: 1400px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.98);
        padding: 35px 40px;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        border: 2px solid rgba(234, 88, 12, 0.3);
        max-height: calc(100vh - 220px);
        overflow-y: auto;
    }
    
    .wrapper h1 {
        text-align: center;
        color: #1a1a2e;
        margin-bottom: 10px;
        font-size: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }
    
    .wrapper h1 .emoji {
        color: #ea580c;
    }
    
    hr {
        margin-bottom: 25px;
        border: none;
        border-top: 2px solid #ea580c;
    }
    
    .personal_info, .user_login, .address {
        margin-bottom: 25px;
    }
    
    h4 {
        color: #ea580c;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #ea580c;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .form-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .field-container {
        display: flex;
        flex-direction: column;
    }
    
    .field-container label {
        font-size: 13px;
        color: #333;
        margin-bottom: 6px;
        font-weight: 600;
    }
    
    .required-asterisk {
        color: #dc2626;
        margin-left: 2px;
    }
    
    input, select {
        padding: 10px 12px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        width: 100%;
        transition: all 0.3s;
    }
    
    input:focus, select:focus {
        outline: none;
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
    }
    
    small {
        color: #dc2626;
        font-size: 11px;
        margin-top: 3px;
        display: block;
        min-height: 14px;
    }
    
    .custom-suffix {
        margin-top: 8px;
    }
    
    .submit-container {
        text-align: center;
        margin-top: 30px;
    }
    
    .submit-btn {
        padding: 16px 60px;
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
    
    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(234, 88, 12, 0.5);
    }
    
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
    
    #strength-bar, #match-bar {
        height: 4px;
        margin-top: 5px;
        border-radius: 3px;
        transition: all 0.3s;
    }
    
    .success-message {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        text-align: center;
        border: 3px solid #15803d;
        font-weight: 600;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
    }

    .error-message {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        border: 3px solid #7f1d1d;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
    }

    .error-message h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
    }

    .error-message p {
        margin: 8px 0;
        font-size: 14px;
        padding-left: 10px;
    }

    .warning-message {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        text-align: center;
        border: 3px solid #c2410c;
        font-weight: 600;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
    }

    /* Scrollbar styling */
    .wrapper::-webkit-scrollbar {
        width: 8px;
    }

    .wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .wrapper::-webkit-scrollbar-thumb {
        background: #ea580c;
        border-radius: 10px;
    }

    .wrapper::-webkit-scrollbar-thumb:hover {
        background: #dc2626;
    }
    
    @media (max-width: 1200px) {
    .form-grid {
        grid-template-columns: repeat(2, 1fr); /* 2 columns instead of 3 */
    }
}

@media (max-width: 968px) {
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

    .form-grid, .form-grid-3 {
        grid-template-columns: 1fr; /* Single column for better readability */
    }

    .form-grid-2 {
        grid-template-columns: 1fr; /* Single column too */
    }

    .wrapper {
        padding: 25px 20px;
        margin: 0 10px; /* Add margin */
    }

    section {
        padding: 30px 15px; /* Reduced padding */
    }

    .wrapper h1 {
        font-size: 1.7rem; /* Adjusted */
    }

    h4 {
        font-size: 1.1rem;
    }

    input, select {
        padding: 11px; /* Slightly reduced */
        font-size: 13px;
    }

    .field-container label {
        font-size: 12px;
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

    .form-grid, .form-grid-3, .form-grid-2 {
        grid-template-columns: 1fr;
        gap: 12px; /* Reduced gap */
    }
    
    .wrapper {
        padding: 20px 15px;
        margin: 0 5px;
    }

    section {
        padding: 20px 10px;
    }

    h4 {
        font-size: 1rem;
        gap: 8px;
    }

    .wrapper h1 {
        font-size: 1.5rem;
        gap: 8px;
    }

    .submit-btn {
        width: 100%;
        padding: 13px 35px;
        font-size: 15px;
    }

    input, select {
        padding: 10px;
        font-size: 13px;
    }

    .field-container label {
        font-size: 12px;
        margin-bottom: 5px;
    }

    small {
        font-size: 10px;
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

    .wrapper h1 {
        font-size: 1.3rem;
    }

    .submit-btn {
        padding: 12px 30px;
        font-size: 14px;
    }
}
</style>
</head>
<body>

<header>
<nav>
    <a href="index.php" class="logo"><span>🎬</span> CineMax <span>Portal</span></a> 
    <ul>
        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
    </ul>
</nav>
</header>

<section>
<div class="wrapper">
    <h1><span class="emoji">🎟️</span> Create Staff Account</h1>
    <hr>

    <?php
    // Display errors
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        echo "<div class='error-message'>";
        echo "<h3>❌ Registration Errors - Please Fix The Following:</h3>";
        foreach ($_SESSION['errors'] as $key => $error) {
            echo "<p><strong>• " . htmlspecialchars($error) . "</strong></p>";
        }
        echo "</div>";
        unset($_SESSION['errors']);
    }

    // Display success
    if (isset($_SESSION['success'])) {
        echo "<div class='success-message'>";
        echo "✅ " . htmlspecialchars($_SESSION['success']);
        echo "</div>";
        unset($_SESSION['success']);
    }

    // Display general error
    if (isset($_SESSION['error'])) {
        echo "<div class='warning-message'>";
        echo "⚠️ " . htmlspecialchars($_SESSION['error']);
        echo "</div>";
        unset($_SESSION['error']);
    }
    ?>

    <form action="register.php" method="POST" id="registration-form">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <!-- PERSONAL INFORMATION -->
        <div class="personal_info">
            <h4>👤 Personal Information</h4>
            
            <div class="form-grid">
                <div class="field-container">
                    <label>ID No.<span class="required-asterisk">*</span></label>
                    <input type="text" id="idno" name="id" placeholder="xxxx-xxxx" value="<?php echo isset($_POST['id']) ? htmlspecialchars($_POST['id']) : ''; ?>">
                    <small id="idno-error"></small>
                </div>

                <div class="field-container">
                    <label>Firstname<span class="required-asterisk">*</span></label>
                    <input type="text" id="firstname" name="firstname" placeholder="John" value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : ''; ?>">
                    <small id="firstname-error"></small>
                </div>

                <div class="field-container">
                    <label>Middlename (Optional)</label>
                    <input type="text" id="middlename" name="middlename" placeholder="Michael" value="<?php echo isset($_POST['middlename']) ? htmlspecialchars($_POST['middlename']) : ''; ?>">
                    <small id="middlename-error"></small>
                </div>

                <div class="field-container">
                    <label>Lastname<span class="required-asterisk">*</span></label>
                    <input type="text" id="lastname" name="lastname" placeholder="Doe" value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : ''; ?>">
                    <small id="lastname-error"></small>
                </div>
            </div>

            <div class="form-grid">
                <div class="field-container">
                    <label>Suffix (Optional)</label>
                    <select id="suffix" name="suffix" onchange="showCustomSuffix()">
                        <option value="none" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] === 'none') ? 'selected' : ''; ?>>None</option>
                        <option value="jr" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] === 'jr') ? 'selected' : ''; ?>>Jr</option>
                        <option value="sr" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] === 'sr') ? 'selected' : ''; ?>>Sr</option>
                        <option value="custom" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] === 'custom') ? 'selected' : ''; ?>>Custom</option>
                    </select>
                    <div class="custom-suffix" id="custom-suffix" style="display: <?php echo (isset($_POST['suffix']) && $_POST['suffix'] === 'custom') ? 'block' : 'none'; ?>;">
                        <input type="text" id="custom-input" name="custom-input" placeholder="Enter Suffix" value="<?php echo isset($_POST['custom-input']) ? htmlspecialchars($_POST['custom-input']) : ''; ?>">
                    </div>
                </div>

                <div class="field-container">
                    <label>Sex<span class="required-asterisk">*</span></label>
                    <select id="sex" name="sex">
                        <option value="" <?php echo empty($_POST['sex']) ? 'selected' : ''; ?>>Select Sex</option>
                        <option value="male" <?php echo (isset($_POST['sex']) && $_POST['sex'] === 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo (isset($_POST['sex']) && $_POST['sex'] === 'female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>

                <div class="field-container">
                    <label>Birthday<span class="required-asterisk">*</span></label>
                    <input type="date" id="birthdate" name="birthdate" value="<?php echo isset($_POST['birthdate']) ? htmlspecialchars($_POST['birthdate']) : ''; ?>">
                </div>

                <div class="field-container">
                    <label>Age</label>
                    <input type="number" id="age" name="age" readonly value="<?php echo isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''; ?>">
                    <small id="age-error"></small>
                </div>
            </div>
        </div>

        <!-- USER LOGIN -->
        <div class="user_login">
            <h4>🔐 Account Credentials</h4>
            
            <div class="form-grid">
                <div class="field-container">
                    <label>Email<span class="required-asterisk">*</span></label>
                    <input type="email" id="email" name="email" placeholder="john.doe@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <small id="email-error"></small>
                </div>

                <div class="field-container">
                    <label>Username<span class="required-asterisk">*</span> <small style="color:#666;">(letters, numbers, underscore only)</small></label>
                    <input type="text" id="username" name="username" placeholder="johndoe123" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <small id="username-error"></small>
                </div>

                <div class="field-container">
                    <label>Password<span class="required-asterisk">*</span></label>
                    <input type="password" id="password" name="password" placeholder="••••••••">
                    <small id="password-error"></small>
                    <small id="password-strength"></small>
                    <div id="strength-bar"></div>
                </div>

                <div class="field-container">
                    <label>Re-Password<span class="required-asterisk">*</span></label>
                    <input type="password" id="repassword" name="repassword" placeholder="••••••••">
                    <small id="repassword-error"></small>
                    <div id="match-bar"></div>
                </div>
            </div>
        </div>

        <!-- ADDRESS -->
        <div class="address">
            <h4>📍 Address Details</h4>
            
            <div class="form-grid">
                <div class="field-container">
                    <label>Purok<span class="required-asterisk">*</span></label>
                    <input type="text" id="purok" name="purok" placeholder="Purok 1" value="<?php echo isset($_POST['purok']) ? htmlspecialchars($_POST['purok']) : ''; ?>">
                    <small id="purok-error"></small>
                </div>

                <div class="field-container">
                    <label>Barangay<span class="required-asterisk">*</span></label>
                    <input type="text" id="barangay" name="barangay" placeholder="San Jose" value="<?php echo isset($_POST['barangay']) ? htmlspecialchars($_POST['barangay']) : ''; ?>">
                    <small id="barangay-error"></small>
                </div>

                <div class="field-container">
                    <label>Municipality<span class="required-asterisk">*</span></label>
                    <input type="text" id="municipality" name="municipality" placeholder="Quezon City" value="<?php echo isset($_POST['municipality']) ? htmlspecialchars($_POST['municipality']) : ''; ?>">
                    <small id="municipality-error"></small>
                </div>

                <div class="field-container">
                    <label>Province<span class="required-asterisk">*</span></label>
                    <input type="text" id="province" name="province" placeholder="Metro Manila" value="<?php echo isset($_POST['province']) ? htmlspecialchars($_POST['province']) : ''; ?>">
                    <small id="province-error"></small>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="field-container">
                    <label>Country<span class="required-asterisk">*</span></label>
                    <input type="text" id="country" name="country" placeholder="Philippines" value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>">
                    <small id="country-error"></small>
                </div>

                <div class="field-container">
                    <label>Zipcode<span class="required-asterisk">*</span></label>
                    <input type="text" id="zipcode" name="zipcode" placeholder="1100" value="<?php echo isset($_POST['zipcode']) ? htmlspecialchars($_POST['zipcode']) : ''; ?>">
                    <small id="zipcode-error"></small>
                </div>
            </div>
        </div>

        <div class="submit-container">
            <button type="submit" class="submit-btn">🎬 Create Account & Start Booking</button>
        </div>
    </form>
</div>
</section>

<div class="footer">
    <p>&copy; 2024 CineMax Portal. All Rights Reserved.</p>
    <p>Designed for seamless movie booking management</p>
</div>

<script src="../javascript/sex.js"></script>
<script src="../javascript/password.js"></script>
<script src="../javascript/zipcode.js"></script>
<script src="../javascript/b-day.js"></script>
<script src="../javascript/barangay.js"></script>
<script src="../javascript/email.js"></script>
<script src="../javascript/municipality.js"></script>
<script src="../javascript/username.js"></script>
<script src="../javascript/purok.js"></script>
<script src="../javascript/suffix.js"></script>
<script src="../javascript/country.js"></script>
<script src="../javascript/lastname.js"></script>
<script src="../javascript/middlename.js"></script>
<script src="../javascript/province.js"></script>
<script src="../javascript/idno-validation.js"></script>
<script src="../javascript/firstname-validation.js"></script>

</body>
</html>