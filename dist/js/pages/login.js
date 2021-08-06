// Document is ready
$(document).ready(function () {
    const $email = $('#email');
    const $emailError = $('#emailError');
    const $pwd = $('#password');
    const $pwdError = $('#passwordError');

    // Validate email
    $email.keyup(function () {
        validateEmail($email, $emailError);
    });

    // Validate password
    $pwd.keyup(function () {
        validatePassword($pwd, $pwdError, false);
    });

    // Toggle Password eye icon
    $(".passEye").click (function() {
        togglePassIcon($(this));
    });

    // Submit button
    $("#submitBtn").click(function (event) {
        const emailCheck = validateEmail($email, $emailError);
        const pwdCheck = validatePassword($pwd, $pwdError, false);
        if (emailCheck && pwdCheck)
            return true;
        else {
            event.preventDefault();
        }
    });
});