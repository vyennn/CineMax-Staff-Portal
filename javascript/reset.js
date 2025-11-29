document.addEventListener("DOMContentLoaded", function () {
    const resetButton = document.getElementById('resetButton');

    resetButton.addEventListener('click', function () {
        // Confirm with the user before resetting
        if (confirm("Are you sure you want to reset your login attempts?")) {
            // Send an AJAX request to reset the login attempts
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../php/reset_attempts.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert("Login attempts have been reset.");
                    // Reload the page to reflect the changes
                    location.reload();
                } else {
                    alert("Failed to reset login attempts.");
                }
            };
            xhr.send('action=reset'); // Send action as reset to the server
        }
    });
});
