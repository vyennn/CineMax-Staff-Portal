document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("login-form");
    const password = document.getElementById("password");
    const passwordError = document.getElementById("password_error");
    const asterisk = document.getElementById("password-asterisk");

    // Remove password error when user types
    password.addEventListener("input", function () {
        if (password.value.trim() !== "") {
            passwordError.textContent = "";
        }
    });

    form.addEventListener("submit", function (e) {
        if (password.value.trim() === "") {
            passwordError.textContent = "Password is required";
            e.preventDefault();
        }
    });

       function toggleAsterisk() {
        if (password.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    password.addEventListener("input", toggleAsterisk);
});
