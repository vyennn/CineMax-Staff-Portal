document.addEventListener("DOMContentLoaded", function () {
    const firstnameInput = document.getElementById("firstname");
    const firstnameError = document.getElementById("firstname-error");
    const asterisk = document.getElementById("firstname-asterisk");

    const patterns = {
        consecutiveUppercase: /[A-Z]{2,}/,
        doubleSpace: / {2,}/,
        containsNumbers: /\d/,
        containsSpecialCharacters: /[^a-zA-Z\s]/,
    };

    function isValidFirstnameWord(word) {
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
        if (patterns.containsNumbers.test(trimmed)) {
            return "containsNumbers";
        }
        if (patterns.containsSpecialCharacters.test(trimmed)) {
            return "containsSpecialCharacters";
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
                if (!isValidFirstnameWord(word)) {
                    return "capitalization";
                }
            }
        }

        return null;
    }

    function getErrorMessage(type) {
        const messages = {
            required: "Firstname is required.",
            consecutiveLetters: "Firstname cannot have three consecutive identical letters.",
            containsNumbers: "Firstname cannot contain numbers.",
            containsSpecialCharacters: "Firstname cannot contain special characters.",
            doubleSpace: "Firstname cannot contain double spaces.",
            tooShort: "Firstname must be at least 2 characters long.",
            tooLong: "Firstname cannot exceed 50 characters.",
            consecutiveUppercase: "First letter must be followed by lowercase.",
            capitalization: "Every word must start with a capital letter.",
        };
        return messages[type] || "";
    }

    let lockedErrorType = null;

    firstnameInput.addEventListener("input", function () {
        const value = firstnameInput.value;

        if (value.trim() === "") {
            firstnameError.textContent = "";
            firstnameError.style.display = "none";
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
            firstnameError.textContent = getErrorMessage(lockedErrorType);
            firstnameError.style.display = "inline";
        } else {
            firstnameError.style.display = "none";
        }
    });

    const form = document.getElementById("registration-form");
    form.addEventListener("submit", function (event) {
        const value = firstnameInput.value;

        const errorType = getFirstError(value);
        if (errorType) {
            event.preventDefault();
            lockedErrorType = errorType;
            firstnameError.textContent = getErrorMessage(errorType);
            firstnameError.style.display = "inline";
            firstnameInput.scrollIntoView({ behavior: "smooth", block: "center" });
        } else {
            firstnameError.style.display = "none";
        }
    });

    function toggleAsterisk() {
        if (firstnameInput.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    firstnameInput.addEventListener("input", toggleAsterisk);
});
