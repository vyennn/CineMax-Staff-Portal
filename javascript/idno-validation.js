document.addEventListener("DOMContentLoaded", function () {
    const idnoInput = document.getElementById("idno");
    const idnoError = document.getElementById("idno-error");
    const form = document.getElementById("registration-form");
    const asterisk = document.getElementById("idno-asterisk");

    const idPattern = /^\d{4}-\d{4}$/; // Pattern: 4 digits, hyphen, 4 digits

    // Handle real-time input validation
    idnoInput.addEventListener("input", function () {
        const idnoValue = idnoInput.value;
        let errorMessages = [];

        if (idnoValue.trim() === "") {
            idnoError.textContent = "";
            idnoError.style.display = "none";
            return;
        }

        if (!idPattern.test(idnoValue)) {
            errorMessages.push("ID must be in the format YYYY-XXXX (e.g., 2021-0920).");
        }

        if (errorMessages.length > 0) {
            idnoError.textContent = errorMessages.join("\n");
            idnoError.style.display = "inline";
        } else {
            idnoError.style.display = "none";
        }
    });

    // Handle form submission
    form.addEventListener("submit", function (event) {
        const idnoValue = idnoInput.value;
        let isValid = true;
        let errorMessages = [];

        if (idnoValue.trim() === "") {
            errorMessages.push("ID No. is required.");
            isValid = false;
        } else if (!idPattern.test(idnoValue)) {
            errorMessages.push("ID must be in the format YYYY-XXXX (e.g., 2021-0920).");
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
            idnoInput.scrollIntoView({ behavior: "smooth", block: "center" });
            idnoError.textContent = errorMessages.join("\n");
            idnoError.style.display = "inline";
            idnoError.scrollIntoView({ behavior: "smooth", block: "center" });
        } else {
            idnoError.style.display = "none";
        }
    });

    function toggleAsterisk() {
        if (idnoInput.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    idnoInput.addEventListener("input", toggleAsterisk);

});
