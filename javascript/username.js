// Username Input Validation
document.addEventListener("DOMContentLoaded", function () {
    const usernameInput = document.getElementById('username');
    const usernameError = document.getElementById('username-error');
    const form = document.getElementById('registration-form');
    const submitButton = form.querySelector('button[type="submit"]');
    const asterisk = document.getElementById("username-asterisk");

    const minLength = 8;
    const maxLength = 50;

    usernameInput.addEventListener('input', function () {
        const username = this.value.trim();


        // Show/hide asterisk
        asterisk.style.visibility = username === "" ? "visible" : "hidden";

        // ✅ Clear error if input is empty
        if (username === "") {
            usernameError.textContent = "";
            usernameError.style.display = "none";
            submitButton.disabled = false;
            return;
        }

        // Reset error state
        usernameError.style.display = 'none';
        usernameError.textContent = '';
        submitButton.disabled = false;

        // Validation rules
        if (!/^[a-zA-Z]/.test(username.charAt(0))) {
            usernameError.textContent = 'The first character must be a letter.';
            usernameError.style.color = 'red';
            usernameError.style.display = 'block';
            submitButton.disabled = true;
            return;
        }

        if (/[^a-zA-Z.]/.test(username)) {
            usernameError.textContent = 'Username can only contain letters and dots.';
            usernameError.style.color = 'red';
            usernameError.style.display = 'block';
            submitButton.disabled = true;
            return;
        }

        if (/^\./.test(username) || /\.$/.test(username)) {
            usernameError.textContent = 'Dot (.) cannot be the first or last character.';
            usernameError.style.color = 'red';
            usernameError.style.display = 'block';
            submitButton.disabled = true;
            return;
        }

        if (username.length < minLength) {
            usernameError.textContent = `Username must be at least ${minLength} characters.`;
            usernameError.style.color = 'red';
            usernameError.style.display = 'block';
            submitButton.disabled = true;
            return;
        }

        if (username.length > maxLength) {
            usernameError.textContent = `Username must not exceed ${maxLength} characters.`;
            usernameError.style.color = 'red';
            usernameError.style.display = 'block';
            submitButton.disabled = true;
            return;
        }

        // Valid username
        usernameError.textContent = '';
        usernameError.style.display = 'none';
        submitButton.disabled = false;
    });

    form.addEventListener('submit', function (event) {
        const username = usernameInput.value.trim();

        if (username.length === 0) {
            usernameError.textContent = 'Username is required.';
            usernameError.style.color = 'red';
            usernameError.style.display = 'block';

            usernameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            event.preventDefault();
        }
    });
});
