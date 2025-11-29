document.addEventListener("DOMContentLoaded", function () {
    const zipcodeInput = document.getElementById("zipcode");
    const zipcodeError = document.getElementById("zipcode-error");
    const asterisk = document.getElementById("zipcode-asterisk");

    const isNumeric = /^\d+$/;

    // Input event: only validate length and numeric format (not "required")
    zipcodeInput.addEventListener("input", function () {
        zipcodeInput.value = zipcodeInput.value.replace(/[^0-9]/g, "");

        const zipcodeValue = zipcodeInput.value;

        zipcodeError.style.display = "none";

        if (zipcodeValue.length > 0 && zipcodeValue.length < 4) {
            zipcodeError.textContent = "Zipcode must be at least 4 characters long.";
            zipcodeError.style.display = "inline";
        } else if (zipcodeValue.length > 6) {
            zipcodeError.textContent = "Zipcode cannot exceed 6 characters.";
            zipcodeError.style.display = "inline";
        } else if (zipcodeValue.length > 0 && !isNumeric.test(zipcodeValue)) {
            zipcodeError.textContent = "Zipcode must only contain numbers.";
            zipcodeError.style.display = "inline";
        }
    });

    // Submit event: includes the "required" check
    const form = document.getElementById("registration-form");
    form.addEventListener("submit", function (event) {
        const zipcodeValue = zipcodeInput.value.trim();
        let isValid = true;
        let errorMessages = [];

        if (zipcodeValue === "") {
            errorMessages.push("Zipcode is required.");
            isValid = false;
        } else if (zipcodeValue.length < 4) {
            errorMessages.push("Zipcode must be at least 4 characters long.");
            isValid = false;
        } else if (zipcodeValue.length > 6) {
            errorMessages.push("Zipcode cannot exceed 6 characters.");
            isValid = false;
        } else if (!isNumeric.test(zipcodeValue)) {
            errorMessages.push("Zipcode must only contain numbers.");
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
            zipcodeError.textContent = errorMessages.join("\n");
            zipcodeError.style.display = "inline";
            zipcodeInput.scrollIntoView({ behavior: "smooth", block: "center" });
        }
    });

    function toggleAsterisk() {
        if (zipcodeInput.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    zipcodeInput.addEventListener("input", toggleAsterisk);
});
