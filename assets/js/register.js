$(document).ready(function () {
    $('#registrationForm').on('submit', function (event) {
        event.preventDefault();

        // Collect form data
        const name = $('#name').val();
        const email = $('#email').val();
        const password = $('#password').val();

        // Validate inputs
        if (!name || !email || !password) {
            alert('All fields are required.');
            return;
        }

        // Send data to the server
        $.ajax({
            url: './api/register.php',
            type: 'POST',
            data: {
                name: name,
                email: email,
                password: password
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert('Registration successful! Redirecting to login page.');
                    window.location.href = 'login.php';
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    });
});
