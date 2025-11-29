// Show/Hide Password Functionality
// Add event listener to the checkbox to toggle password visibility
document.getElementById('showPassword').addEventListener('click', function() {
    let passwordField = document.getElementById('password'); // Get the password input field
    // Change the type of the password field based on the checkbox state
    if (this.checked) {
        passwordField.type = 'text'; // Show password
    } else {
        passwordField.type = 'password'; // Hide password
    }
});
