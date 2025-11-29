document.addEventListener("DOMContentLoaded", function () {
    const countryInput = document.getElementById("country");
    const countryError = document.getElementById("country-error");
    const asterisk = document.getElementById("country-asterisk");

    const patterns = {
        consecutiveUppercase: /[A-Z]{2,}/,
        doubleSpace: / {2,}/,
        containsNumbers: /\d/,
        containsSpecialCharacters: /[^a-zA-Z\s]/,
    };

    function isValidCountryWord(word) {
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
                if (!isValidCountryWord(word)) {
                    return "capitalization";
                }
            }
        }

        return null;
    }

    function getErrorMessage(type) {
        const messages = {
            required: "Country is required.",
            consecutiveLetters: "Country cannot have three consecutive identical letters.",
            containsNumbers: "Country cannot contain numbers.",
            containsSpecialChars: "Country cannot contain special characters.",
            
            doubleSpace: "Country cannot contain double spaces.",
            tooShort: "Country must be at least 2 characters long.",
            tooLong: "Country cannot exceed 50 characters.",
            consecutiveUppercase: "First letter must be followed by lowercase.",
            capitalization: "Every word must start with a capital letter.",
        };
        return messages[type] || "";
    }

    let lockedErrorType = null;

    countryInput.addEventListener("input", function () {
        const value = countryInput.value;

        if (value.trim() === "") {
            countryError.textContent = "";
            countryError.style.display = "none";
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
            countryError.textContent = getErrorMessage(lockedErrorType);
            countryError.style.display = "inline";
        } else {
            countryError.style.display = "none";
        }
    });

    const form = document.getElementById("registration-form");
    form.addEventListener("submit", function (event) {
        const value = countryInput.value;

        const errorType = getFirstError(value);
        if (errorType) {
            event.preventDefault();
            lockedErrorType = errorType;
            countryError.textContent = getErrorMessage(errorType);
            countryError.style.display = "inline";
            countryInput.scrollIntoView({ behavior: "smooth", block: "center" });
        } else {
            countryError.style.display = "none";
        }
    });

    function toggleAsterisk() {
        if (countryInput.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    countryInput.addEventListener("input", toggleAsterisk);
});
