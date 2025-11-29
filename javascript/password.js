document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const repasswordInput = document.getElementById('repassword');
    const passwordStrength = document.getElementById('password-strength');
    const repasswordError = document.getElementById('repassword-error');
    const strengthBar = document.getElementById('strength-bar');
    const matchBar = document.getElementById('match-bar');
    const asterisk = document.getElementById("password-asterisk");

    // Password strength checking
    passwordInput.addEventListener('input', function () {
        const password = passwordInput.value;
    
        if (password === '') {
            // Clear the message and bar when input is empty
            passwordStrength.textContent = '';
            strengthBar.style.width = '0';
            strengthBar.style.backgroundColor = '';
        } else if (password.length < 8) {
            passwordStrength.textContent = 'Weak password: please include uppercase, numbers, and special characters.';
            passwordStrength.style.color = 'red';
            strengthBar.style.width = '25%';
            strengthBar.style.backgroundColor = 'red';
        } else if (/[a-zA-Z]/.test(password) && /\d/.test(password) && !/[!@#$%^&*]/.test(password)) {
            passwordStrength.textContent = 'Medium password';
            passwordStrength.style.color = 'orange';
            strengthBar.style.width = '50%';
            strengthBar.style.backgroundColor = 'orange';
        } else if (/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}/.test(password)) {
            passwordStrength.textContent = 'Strong password';
            passwordStrength.style.color = 'green';
            strengthBar.style.width = '100%';
            strengthBar.style.backgroundColor = 'green';
        }
    
        // Reset repassword validation if password changes
        repasswordError.textContent = '';
        matchBar.style.width = '0'; // Reset match bar on password input
    });
    

    // Check if passwords match
    repasswordInput.addEventListener('input', function() {
        repasswordError.textContent = '';
        if (repasswordInput.value === passwordInput.value) {
            repasswordError.textContent = 'Passwords match.';
            repasswordError.style.color = 'green';
            matchBar.style.width = '100%';
            matchBar.style.backgroundColor = 'green';
        } else {
            repasswordError.textContent = 'Passwords do not match.';
            repasswordError.style.color = 'red';
            matchBar.style.width = '50%';
            matchBar.style.backgroundColor = 'red';
        }
    });

    // Form submission validation
    document.getElementById('registration-form').addEventListener('submit', function(event) {
        if (repasswordInput.value !== passwordInput.value) {
            event.preventDefault(); // Prevent form submission
            repasswordError.textContent = 'Passwords do not match.';
            repasswordError.style.color = 'red';
            repasswordInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else if (passwordStrength.style.color === 'red') {
            event.preventDefault(); // Prevent form submission
            passwordStrength.textContent = 'Please choose a stronger password.';
            passwordInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    function toggleAsterisk() {
        if (passwordInput.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    passwordInput.addEventListener("input", toggleAsterisk);

});