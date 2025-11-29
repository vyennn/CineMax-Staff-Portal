document.addEventListener("DOMContentLoaded", function () {
    const lastnameInput = document.getElementById("lastname");
    const lastnameError = document.getElementById("lastname-error");
    const asterisk = document.getElementById("lastname-asterisk");

    const patterns = {
        consecutiveUppercase: /[A-Z]{2,}/,
        doubleSpace: / {2,}/,
        containsNumbers: /\d/,
        containsSpecialCharacters: /[^a-zA-Z\s]/,
    };

    function isValidLastnameWord(word) {
        return /^[A-Z][a-zA-Z]*$/.test(word);
    }

    function hasThreeConsecutiveSameLettersIgnoreCase(str) {
        for (let i = 0; i < str.length - 2; i++) {
            const c1 = str[i].toLowerCase();
            const c2 = str[i + 1].toLowerCase();
            const c3 = str[i + 2].toLowerCase();
    
            // Skip if any of the three characters is a space
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
        
        if (hasThreeConsecutiveSameLettersIgnoreCase(value)) {
            return "consecutiveLetters";
        }
        if (patterns.containsNumbers.test(value)) {
            return "containsNumbers";
        }
        if (patterns.containsSpecialCharacters.test(value)) {
            return "containsSpecialChars";
        }
        
        if (patterns.doubleSpace.test(value)) {
            return "doubleSpace";
        }

        if (trimmed === "") {
            return "required";
        }
        
        if (trimmed.length === 1) {
            return "tooShort";
        }
        
        if (value.trim().length > 50) {
            return "tooLong";
        }

        const words = value.trim().split(" ");
        for (const word of words) {
            if (word.length > 0) {
                if (patterns.consecutiveUppercase.test(word)) {
                    return "consecutiveUppercase";
                }
                if (!isValidLastnameWord(word)) {
                    return "capitalization";
                }
            }
        }

        return null; // no error
    }

    function getErrorMessage(type) {
        const messages = {
            required: "Lastname is required.",
            consecutiveLetters: "Lastname cannot have three consecutive identical letters.",
            containsNumbers: "Lastname cannot contain numbers.",
            containsSpecialChars: "Lastname cannot contain special characters.",
            
            doubleSpace: "Lastname cannot contain double spaces.",
            tooShort: "Lastname must be at least 2 characters long.",
            tooLong: "Lastname cannot exceed 50 characters.",
            consecutiveUppercase: "First letter must be follow by lowercase.",
            capitalization: "Every word must start with a capital letter.",
        };
        return messages[type] || "";
    }

    let lockedErrorType = null;

    lastnameInput.addEventListener("input", function () {
        const value = lastnameInput.value;

        // Clear everything if input is empty
        if (value.trim() === "") {
            lastnameError.textContent = "";
            lastnameError.style.display = "none";
            lockedErrorType = null;
            return;
        }

        // Lock the first error type
        if (!lockedErrorType) {
            lockedErrorType = getFirstError(value);
        }

        // If the locked error is resolved, unlock
        if (lockedErrorType && getFirstError(value) !== lockedErrorType) {
            const stillLocked = getFirstError(value);
            if (!stillLocked) {
                lockedErrorType = null;
            } else {
                lockedErrorType = stillLocked;
            }
        }

        // Display error if any
        if (lockedErrorType) {
            lastnameError.textContent = getErrorMessage(lockedErrorType);
            lastnameError.style.display = "inline";
        } else {
            lastnameError.style.display = "none";
        }
    });

    // Form submit logic (respect the locked error)
    const form = document.getElementById("registration-form");
    form.addEventListener("submit", function (event) {
        const value = lastnameInput.value;

        const errorType = getFirstError(value);
        if (errorType) {
            event.preventDefault();
            lockedErrorType = errorType;
            lastnameError.textContent = getErrorMessage(errorType);
            lastnameError.style.display = "inline";
            lastnameInput.scrollIntoView({ behavior: "smooth", block: "center" });
        } else {
            lastnameError.style.display = "none";
        }
    });

    function toggleAsterisk() {
        if (lastnameInput.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    lastnameInput.addEventListener("input", toggleAsterisk);

});
