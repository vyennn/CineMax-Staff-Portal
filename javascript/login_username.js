document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("login-form");
    const username = document.getElementById("username");
    const usernameError = document.getElementById("username_error");
    const asterisk = document.getElementById("username-asterisk");

    // Remove username error when user types
    username.addEventListener("input", function () {
        if (username.value.trim() !== "") {
            usernameError.textContent = "";
        }
    });

    form.addEventListener("submit", function (e) {
        if (username.value.trim() === "") {
            usernameError.textContent = "Username is required";
            e.preventDefault();
        }
    });

     function toggleAsterisk() {
        if (username.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    username.addEventListener("input", toggleAsterisk);
});
