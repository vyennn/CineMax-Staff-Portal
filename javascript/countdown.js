    // Countdown logic for lockout timer
    // let remainingLockTime = <?php echo $remaining_lock_time; ?>;
    if (remainingLockTime > 0) {
        const countdownElement = document.getElementById('countdown'); // Target countdown element
        const loginButton = document.getElementById('loginButton'); // Target login button
        const registerLink = document.getElementById('registerLink'); // Target register link
        const usernameInput = document.getElementById('username'); // Target username input
        const passwordInput = document.getElementById('password'); // Target password input
        const errorMessageDiv = document.getElementById('loginError'); // Target the error message div

        // Disable login button, register link, and input fields initially
        loginButton.disabled = true;
        registerLink.classList.add('disabled-link');
        usernameInput.disabled = true;
        passwordInput.disabled = true;

        // Countdown interval that updates every second
        const countdownInterval = setInterval(() => {
            remainingLockTime--; // Decrement the remaining lock time

            // Update the countdown display
            countdownElement.textContent = remainingLockTime;

            // Hide the error message when 1 second is left
            if (remainingLockTime === 1 && errorMessageDiv) {
                errorMessageDiv.style.display = 'none'; // Hide the error message
            }

            // Re-enable form elements when countdown ends
            if (remainingLockTime <= 0) {
                clearInterval(countdownInterval); // Stop the countdown

                // Re-enable the login button, inputs, and register link
                loginButton.disabled = false;
                usernameInput.disabled = false;
                passwordInput.disabled = false;
                registerLink.classList.remove('disabled-link');

                // Clear the countdown message
                countdownElement.textContent = '';
            }
        }, 1000); // Update every second
    }