function login() {
    var email = $("#email").val();
    var password = $("#password").val();

    if (email === "" || password === "") {
        alert("Please fill in all fields.");
        return;
    }

    $.ajax({
        url: './api/login.php',
        type: 'POST',
        data: { email, password },
        success: function(response) {
            const res = JSON.parse(response);
            if (res.status) {
                if (res.data.role === 'user' && !res.data.is_active) {
                    alert('Your account is pending approval.');
                } else {
                    window.location.href = res.data.redirect_url;
                }
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert('An error occurred. Please try again.');
        }
    });
}
