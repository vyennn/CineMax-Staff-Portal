document.addEventListener("DOMContentLoaded", function () {
    const purokInput = document.getElementById("purok");
    const purokError = document.getElementById("purok-error");
    const asterisk = document.getElementById("purok-asterisk");

    const patterns = {
        doubleSpace: / {2,}/,
        containsSpecialCharacters: /[^a-zA-Z0-9\s]/,

    };

    // Each word must start with a capital letter or digit, followed by lowercase letters, digits, or hyphens
    function isValidCapitalization(word) {
        return /^[A-Z0-9][a-z0-9\-]*$/.test(word);
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
    
        if (trimmed === "") return "required";
        if (hasThreeConsecutiveSameLettersIgnoreCase(trimmed)) return "consecutiveLetters";
        if (patterns.doubleSpace.test(trimmed)) return "doubleSpace";
        if (trimmed.length > 50) return "tooLong";
        if (patterns.containsSpecialCharacters.test(trimmed)) {
            return "containsSpecialCharacters";
        }
    
    
        const words = trimmed.split(" ");
        
        // New rule: prevent number followed by a word longer than 1 letter
        for (let i = 0; i < words.length - 1; i++) {
            const current = words[i];
            const next = words[i + 1];
    
            if (/^\d+$/.test(current) && /^[A-Za-z]+$/.test(next) && next.length > 1) {
                return "numberFollowedByWord";
            }
        }
    
        for (const word of words) {
            if (word.length > 0 && !isValidCapitalization(word)) {
                return "capitalization";
            }
        }
    
        return null;
    }
    
    

    function getErrorMessage(type) {
        const messages = {
            required: "Purok is required.",
            consecutiveLetters: "Purok cannot have three consecutive identical letters.",
            doubleSpace: "Purok cannot contain double spaces.",
            // tooShort: "Purok must be at least 2 characters long.",
            tooLong: "Purok cannot exceed 50 characters.",
            capitalization: "Each word must start with a capital letter or number, followed by lowercase letters.",
            numberFollowedByWord: "A number must only be followed by a single uppercase letter.",
            containsSpecialCharacters: "Purok cannot contain special characters.",

        };
        return messages[type] || "";
    }

    let lockedErrorType = null;

    purokInput.addEventListener("input", function () {
        const value = purokInput.value;

        if (value.trim() === "") {
            purokError.textContent = "";
            purokError.style.display = "none";
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
            purokError.textContent = getErrorMessage(lockedErrorType);
            purokError.style.display = "inline";
        } else {
            purokError.style.display = "none";
        }
    });

    const form = document.getElementById("registration-form");
    form.addEventListener("submit", function (event) {
        const value = purokInput.value;

        const errorType = getFirstError(value);
        if (errorType) {
            event.preventDefault();
            lockedErrorType = errorType;
            purokError.textContent = getErrorMessage(errorType);
            purokError.style.display = "inline";
            purokInput.scrollIntoView({ behavior: "smooth", block: "center" });
        } else {
            purokError.style.display = "none";
        }
    });

    function toggleAsterisk() {
        if (purokInput.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    purokInput.addEventListener("input", toggleAsterisk);
});
