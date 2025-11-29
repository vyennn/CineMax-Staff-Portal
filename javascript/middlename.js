document.addEventListener("DOMContentLoaded", function () {
    const middlenameInput = document.getElementById("middlename");
    const middlenameError = document.getElementById("middlename-error");
    

    const patterns = {
        consecutiveUppercase: /[A-Z]{2,}/,
        doubleSpace: / {2,}/,
        containsNumbers: /\d/,
        containsSpecialCharacters: /[^a-zA-Z\s]/,
    };

    function isValidMiddlenameWord(word) {
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
        // If empty input (optional field), no error
        if (value.trim() === "") {
            return null;
        }

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
        if (value.trim().length < 2) {
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
                if (!isValidMiddlenameWord(word)) {
                    return "capitalization";
                }
            }
        }

        return null; // no error
    }

    function getErrorMessage(type) {
        const messages = {
            consecutiveLetters: "Middlename cannot have three consecutive identical letters.",
            containsNumbers: "Middlename cannot contain numbers.",
containsSpecialChars: "Middlename cannot contain special characters.",

            doubleSpace: "Middlename cannot contain double spaces.",
            tooShort: "Middlename must be at least 2 characters long.",
            tooLong: "Middlename cannot exceed 50 characters.",
            consecutiveUppercase: "First letter must be followed by lowercase.",
            capitalization: "Every word must start with a capital letter.",
        };
        return messages[type] || "";
    }

    let lockedErrorType = null;

    middlenameInput.addEventListener("input", function () {
        const value = middlenameInput.value;

        // Clear everything if input is empty (optional)
        if (value.trim() === "") {
            middlenameError.textContent = "";
            middlenameError.style.display = "none";
            lockedErrorType = null;
            return;
        }

        if (!lockedErrorType) {
            lockedErrorType = getFirstError(value);
        }

        if (lockedErrorType && getFirstError(value) !== lockedErrorType) {
            const stillLocked = getFirstError(value);
            if (!stillLocked) {
                lockedErrorType = null;
            } else {
                lockedErrorType = stillLocked;
            }
        }

        if (lockedErrorType) {
            middlenameError.textContent = getErrorMessage(lockedErrorType);
            middlenameError.style.display = "inline";
        } else {
            middlenameError.style.display = "none";
        }
    });

    const form = document.getElementById("registration-form");
    form.addEventListener("submit", function (event) {
        const value = middlenameInput.value;

        const errorType = getFirstError(value);

        // If error AND not empty (because empty is allowed)
        if (errorType) {
            event.preventDefault();
            lockedErrorType = errorType;
            middlenameError.textContent = getErrorMessage(errorType);
            middlenameError.style.display = "inline";
            middlenameInput.scrollIntoView({ behavior: "smooth", block: "center" });
        } else {
            middlenameError.style.display = "none";
        }
    });

  
});
