document.addEventListener("DOMContentLoaded", function () {
    const provinceInput = document.getElementById("province");
    const provinceError = document.getElementById("province-error");
    const asterisk = document.getElementById("province-asterisk");

    const patterns = {
        consecutiveUppercase: /[A-Z]{2,}/,
        doubleSpace: / {2,}/,
        containsNumbers: /\d/,
        containsSpecialCharacters: /[^a-zA-Z\s]/,
    };

    function isValidFirstWord(word) {
        return /^[A-Z][a-zA-Z]*$/.test(word);
    }

    function isValidOtherWord(word) {
        return /^[a-zA-Z]+$/.test(word);
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

        const words = trimmed.split(" ").filter(Boolean);
        for (let i = 0; i < words.length; i++) {
            const word = words[i];
            if (patterns.consecutiveUppercase.test(word)) {
                return "consecutiveUppercase";
            }
            if (i === 0 && !isValidFirstWord(word)) {
                return "capitalization";
            }
            if (i > 0 && !isValidOtherWord(word)) {
                return "capitalization";
            }
        }

        return null;
    }

    function getErrorMessage(type) {
        const messages = {
            required: "Province is required.",
            consecutiveLetters: "Province cannot have three consecutive identical letters.",
            containsNumbers: "Province cannot contain numbers.",
            containsSpecialChars: "Province cannot contain special characters.",
            
            doubleSpace: "Province cannot contain double spaces.",
            tooShort: "Province must be at least 2 characters long.",
            tooLong: "Province cannot exceed 50 characters.",
            consecutiveUppercase: "First letter must be followed by lowercase.",
            capitalization: "First word must start with a capital letter; other words can start with any case.",
        };
        return messages[type] || "";
    }

    let lockedErrorType = null;

    provinceInput.addEventListener("input", function () {
        const value = provinceInput.value;

        if (value.trim() === "") {
            provinceError.textContent = "";
            provinceError.style.display = "none";
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
            provinceError.textContent = getErrorMessage(lockedErrorType);
            provinceError.style.display = "inline";
        } else {
            provinceError.style.display = "none";
        }
    });

    const form = document.getElementById("registration-form");
    form.addEventListener("submit", function (event) {
        const value = provinceInput.value;

        const errorType = getFirstError(value);
        if (errorType) {
            event.preventDefault();
            lockedErrorType = errorType;
            provinceError.textContent = getErrorMessage(errorType);
            provinceError.style.display = "inline";
            provinceInput.scrollIntoView({ behavior: "smooth", block: "center" });
        } else {
            provinceError.style.display = "none";
        } 
    });

    function toggleAsterisk() {
        if (provinceInput.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    provinceInput.addEventListener("input", toggleAsterisk);
});
