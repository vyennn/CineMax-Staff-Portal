function setupSuffixAndValidation() {
    const suffixSelect = document.getElementById('suffix');
    const customSuffixInput = document.getElementById('custom-suffix');
    const customInputField = document.getElementById('custom-input');
    const userLoginContainer = document.getElementById("user_login_container");
    const sexSelect = document.getElementById('sex');
    const registrationForm = document.getElementById('registration-form');

    // Handle custom suffix display
    suffixSelect.addEventListener('change', function () {
        if (suffixSelect.value === 'custom') {
            customSuffixInput.style.display = 'block'; // Show custom suffix input
            userLoginContainer.style.marginTop = "60px"; // Adjust layout
        } else {
            customSuffixInput.style.display = 'none'; // Hide custom suffix input
            customInputField.value = ''; // Clear only when hiding
            userLoginContainer.style.marginTop = "0"; // Reset margin
        }
    });

    // Form submission validation
    registrationForm.addEventListener('submit', function (event) {
        let isValid = true;

        // Custom suffix validation only when "custom" is selected
        if (suffixSelect.value === 'custom' && customInputField.value.trim() === '') {
            isValid = false;
            alert('Please enter a custom suffix.');
        }

        // Ensure "none", "jr", and "sr" are treated as valid options
        if (suffixSelect.value === '' || !suffixSelect.value) {
            isValid = false;
            //alert('Please select a valid suffix.');
        }

        // Sex validation
        if (!sexSelect.value) {
            isValid = false;
            // alert('Please select your sex.');
        }

        // Prevent form submission if any validation fails
        if (!isValid) {
            event.preventDefault();
        }
    });
}

// Call the setup function on page load
document.addEventListener('DOMContentLoaded', setupSuffixAndValidation);
