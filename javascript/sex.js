document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registration-form');
    const sexSelect = document.getElementById('sex');
    const sexError = document.createElement('small'); // Create error message container
    const asterisk = document.getElementById("sex-asterisk");

    // Style the error message
    sexError.id = 'sex-error';
    sexError.style.color = 'red';
    sexError.style.fontSize = 'small';
    sexError.style.display = 'none'; // Initially hidden
    sexSelect.parentNode.appendChild(sexError); // Append below the dropdown

    form.addEventListener('submit', function (event) {
        let isValid = true;

        // Reset the error message
        sexError.style.display = 'none';
        sexError.textContent = '';

        // Check if 'sex' is selected
        if (!sexSelect.value || sexSelect.value.trim() === "") {
            isValid = false;
            sexError.textContent = "Sex is required."; // Set error message
            sexError.style.display = 'block'; // Show the error
            sexSelect.focus(); // Focus on the 'Sex' field
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            event.preventDefault();
        }
    });

    // Hide the error message when the user interacts with the dropdown
    sexSelect.addEventListener('change', function () {
        if (sexSelect.value.trim() !== "") {
            sexError.style.display = 'none';
        }
    });

    function toggleAsterisk() {
        if (sexSelect.value.trim() === "") {
            asterisk.style.visibility = "visible";
        } else {
            asterisk.style.visibility = "hidden";
        }
    }

    toggleAsterisk();
    sexSelect.addEventListener("input", toggleAsterisk);
});
