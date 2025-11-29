// Email Input Validation
document.addEventListener("DOMContentLoaded", function () {
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    const form = document.getElementById('registration-form');
    const submitButton = form.querySelector('button[type="submit"]');
    const asterisk = document.getElementById("email-asterisk");

    const minLength = 8;
    const maxLength = 50;

    emailInput.addEventListener('input', function () {
        const email = this.value.trim();

        // Show/hide asterisk
        asterisk.style.visibility = email === "" ? "visible" : "hidden";

        // If the input is empty, clear errors and do not block submission
        if (email === "") {
            emailError.textContent = "";
            emailError.style.display = "none";
            submitButton.disabled = false;
            return;
        }

        // Validation rules
        if (!/^[a-zA-Z]/.test(email.charAt(0))) {
            emailError.textContent = 'The first character must be a letter.';
        } else if (/[^a-zA-Z0-9._@]/.test(email)) {
            emailError.textContent = 'Email can only contain letters, numbers, dot (.), underscore (_), and @.';
        } else if (email.length < minLength) {
            emailError.textContent = `Email must be at least ${minLength} characters.`;
        } else if (email.length > maxLength) {
            emailError.textContent = `Email must not exceed ${maxLength} characters.`;
        } else {
            // No error
            emailError.textContent = "";
            emailError.style.display = "none";
            submitButton.disabled = false;
            return;
        }

        // If any error occurred, show it and disable the button
        emailError.style.color = 'red';
        emailError.style.display = 'block';
        submitButton.disabled = true;
    });

    // On form submission, check if email is empty
    form.addEventListener('submit', function (event) {
        const email = emailInput.value.trim();
        if (email.length === 0) {
            emailError.textContent = 'Email is required.';
            emailError.style.color = 'red';
            emailError.style.display = 'block';
            emailInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            event.preventDefault();
        }
    });
});
