// Document is ready
$(document).ready(function () {
    const $fullName = $('#fullName');
    const $fullNameError = $('#fullNameError');
    const $email = $('#email');
    const $emailError = $('#emailError');
    const $pwd = $('#password');
    const $pwdError = $('#passwordError');
    const $confpwd = $('#confirmPassword');
    const $confpwdError = $('#confirmPasswordError');
    const $tncCheckBox = $('#tnc');

    // Validate fullName
    $fullName.keyup(function () {
        validateFullName($fullName, $fullNameError);
    });

    // Validate email
    $email.keyup(function () {
        validateEmail($email, $emailError);
    });

    // Validate password
    $pwd.keyup(function () {
        validatePassword($pwd, $pwdError);
        if ($confpwd.val() !== '')
            validateConfirmPassword($pwd, $confpwd, $confpwdError);
    });

    // Toggle Password eye icon
    $('.passEye').click (function() {
        togglePassIcon($(this));
    });

    // Validate Confirm Password
    $confpwd.keyup(function () {
        validateConfirmPassword($pwd, $confpwd, $confpwdError);
    });

    // Toggle t&c not accepted error
    $tncCheckBox.click (function () {
        validateTnC($tncCheckBox);
    });

    // Submit button
    $("#submitBtn").click(function (event) {
        const nameCheck = validateFullName($fullName, $fullNameError);
        const emailCheck = validateEmail($email, $emailError);
        const pwdCheck = validatePassword($pwd, $pwdError);
        const confPwdCheck = validateConfirmPassword($pwd, $confpwd, $confpwdError);
        const tncCheck = validateTnC($tncCheckBox);
        if (nameCheck && emailCheck && pwdCheck && confPwdCheck && tncCheck)
            return true;
        else {
            event.preventDefault();
        }
    });
});