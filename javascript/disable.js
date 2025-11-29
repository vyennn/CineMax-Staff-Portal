document.addEventListener("DOMContentLoaded", function () {
    // Check if remainingLockTime was defined in the HTML page
    if (typeof remainingLockTime !== 'undefined' && remainingLockTime > 0) {
        const countdownElement = document.getElementById('countdown');
        const loginErrorElement = document.getElementById('loginError');
        const loginButton = document.getElementById('loginButton');
        const registerLink = document.getElementById('registerLink');
        const homeLink = document.getElementById('homeLink');
        const signupLink = document.getElementById('signupLink');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');

        // Disable elements during countdown
        if (loginButton) loginButton.disabled = true;
        if (usernameInput) usernameInput.disabled = true;
        if (passwordInput) passwordInput.disabled = true;
        if (registerLink) registerLink.classList.add('disabled-link');
        if (homeLink) homeLink.classList.add('disabled-link');
        if (signupLink) signupLink.classList.add('disabled-link');
        if (loginButton) loginButton.classList.add('disabled-link');

        // Show the initial countdown
        if (countdownElement) {
            countdownElement.textContent = remainingLockTime;
        }

        // Countdown logic
        const countdownInterval = setInterval(() => {
            remainingLockTime--;
            if (countdownElement) {
                countdownElement.textContent = remainingLockTime;
            }

            if (remainingLockTime <= 0) {
                clearInterval(countdownInterval);

                // Re-enable elements
                if (loginButton) loginButton.disabled = false;
                if (usernameInput) usernameInput.disabled = false;
                if (passwordInput) passwordInput.disabled = false;
                if (registerLink) registerLink.classList.remove('disabled-link');
                if (homeLink) homeLink.classList.remove('disabled-link');
                if (signupLink) signupLink.classList.remove('disabled-link');
                if (loginButton) loginButton.classList.remove('disabled-link');

                // Clear the error message
                if (loginErrorElement) {
                    loginErrorElement.innerHTML = '';
                }
            }
        }, 1000);
    }
});
