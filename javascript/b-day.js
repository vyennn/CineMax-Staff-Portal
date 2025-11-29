document.addEventListener("DOMContentLoaded", function () {
    const birthdateInput = document.getElementById('birthdate');
    const ageInput = document.getElementById('age');
    const ageError = document.getElementById('age-error');
    const birthdateError = document.createElement('small'); // Error for birthdate

    // Style the birthdate error
    birthdateError.id = 'birthdate-error';
    birthdateError.style.color = 'red';
    birthdateError.style.fontSize = 'small';
    birthdateError.style.display = 'none';
    birthdateInput.parentNode.appendChild(birthdateError); // Append below birthdate input

    // Validate birthdate and calculate age
    birthdateInput.addEventListener('change', function () {
        const birthdate = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birthdate.getFullYear();
        const monthDifference = today.getMonth() - birthdate.getMonth();

        if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthdate.getDate())) {
            age--;
        }

        ageInput.value = age > 0 ? age : '';

        if (age < 18) {
            ageError.style.display = 'block';
            ageError.textContent = "Allow legal age only (18+).";
        } else {
            ageError.style.display = 'none';
            ageError.textContent = "";
        }

        // Clear birthday error if valid
        if (birthdateInput.value) {
            birthdateError.style.display = 'none';
            birthdateError.textContent = '';
        }
    });

    // Handle form submission
    const form = document.getElementById("registration-form");
    form.addEventListener('submit', function (event) {
        const age = parseInt(ageInput.value, 10);
        let isValid = true;

        // Check if birthdate is empty
        if (!birthdateInput.value) {
            isValid = false;
            birthdateError.style.display = 'block';
            birthdateError.textContent = "Birthdate is required.";
            birthdateInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            birthdateError.style.display = 'none';
        }

        // Check age validation
        // if (isNaN(age) || age < 18) {
        //     isValid = false;
        //     ageError.style.display = 'block';
        //     ageError.textContent = "Allow legal age only (18+).";
        //     birthdateInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // }

        // Strictly prevent submission if not valid
        if (!isValid) {
            event.preventDefault();
        }
    });
});