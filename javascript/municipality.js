document.addEventListener("DOMContentLoaded", function () {
    const municipalityInput = document.getElementById("municipality");
    const municipalityError = document.getElementById("municipality-error");
    const asterisk = document.getElementById("municipality-asterisk");

    const patterns = {
        consecutiveUppercase: /[A-Z]{2,}/,
        doubleSpace: / {2,}/,
        containsNumbers: /\d/,
        containsSpecialCharacters: /[^a-zA-Z\s]/,
    };

    function isValidMunicipalityWord(word) {
        return /^[A-Z][a-zA-Z]*$/.test(word);
    }

    function hasThreeConsecutiveSameLettersIgnoreCase(str) {
        for (let i = 0; i < str.length - 2; i++) {
            const c1 = str[i].toLowerCase();
            const c2 = str[i + 1].toLowerCase();
            const c3 = str[i + 2].toLowerCase();

            if (c1 === ' ' || c2 === ' ' || c3 === ' ') {
                continue;
            }

            if (c1 === c2 && c2 === c3) {
                return true;
            }
        }
        return false;
    }

    function getFirstError(value) {
        const trimmed = value.trim();

        if (trimmed === "") {
            return "required";
        }

        if (trimmed.length === 1) {
            return "tooShort";
        }

        if (hasThreeConsecutiveSameLettersIgnoreCase(trimmed)) {
            return "consecutiveLetters";
        }
        if (patterns.containsNumbers.test(value)) {
            return "containsNumbers";
        }
        if (patterns.containsSpecialCharacters.test(value)) {
            return "containsSpecialChars";
        }
        
        if (patterns.doubleSpace.test(trimmed)) {
            return "doubleSpace";
        }
        if (trimmed.length > 50) {
            return "tooLong";
        }

        const words = trimmed.split(" ");
        for (const word of words) {
            if (word.length > 0) {
                if (patterns.consecutiveUppercase.test(word)) {
                    return "consecutiveUppercase";
                }
                if (!isValidMunicipalityWord(word)) {
                    return "capitalization";
                }
            }
        }

        return null;
    }

    function getErrorMessage(type) {
        const messages = {
            required: "Municipality is required.",
            consecutiveLetters: "Municipality cannot have three consecutive identical letters.",
            containsNumbers: "Municipality cannot contain numbers.",
containsSpecialChars: "Municipality cannot contain special characters.",

            doubleSpace: "Municipality cannot contain double spaces.",
            tooShort: "Municipality must be at least 2 characters long.",
            tooLong: "Municipality cannot exceed 50 characters.",
            consecutiveUppercase: "First letter must be followed by lowercase.",
            capitalization: "Every word must start with a capital letter.",
        };
        return messages[type] || "";
    }

    let lockedErrorType = null;

    municipalityInput.addEventListener("input", function () {
        const value = municipalityInput.value;

        if (value.trim() === "") {
            municipalityError.textContent = "";
            municipalityError.style.display = "none";
            lockedErrorType = null;
            return;
        }

        if (!lockedErrorType) {
            lockedErrorType = getFirstError(value);
        }

        if (lockedErrorType && getFirstError(value) !== lockedErrorType) {
            const stillLocked = getFirstError(value);
            lockedErrorType = stillLocked || null;
        }

        if (lockedErrorType) {
            municipalityError.textContent = getErrorMessage(lockedErrorType);
            municipalityError.style.display = "inline";
        } else {
            municipalityError.style.display = "none";
        }
    });

    const form = document.getElementById("registration-form");
    form.addEventListener("submit", function (event) {
        const value = municipalityInput.value;

        const errorType = getFirstError(value);
        if (errorType) {
            event.preventDefault();
            lockedErrorType = errorType;
            municipalityError.textContent = getErrorMessage(errorType);
            municipalityError.style.display = "inline";
            municipalityInput.scrollIntoView({ behavior: "smooth", block: "center" });
        } else {
            municipalityError.style.display = "none";
        }
    });

    function toggleAsterisk() {
        if (municipalityInput.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    municipalityInput.addEventListener("input", toggleAsterisk);
});
