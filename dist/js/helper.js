// Update the error messages accordingly
function update ($obj, $errObj, errMsg=null, option=true) {
    if($obj.val() === '') {
        $obj.removeClass('is-valid');
        $obj.addClass('is-invalid');
        $errObj.text('This field cannot be empty');
        return false;
    }
    else {
        if(option) {
            $obj.removeClass('is-invalid');
            $obj.addClass('is-valid');
            return true;
        }
        else {
            $obj.removeClass('is-valid');
            $obj.addClass('is-invalid');
            $errObj.text(errMsg);
            return false;
        }
    }
}

function validateFullName ($fullName, $fullNameError) {
    return update($fullName, $fullNameError);
}

function validateEmail ($email, $emailError) {
    const errMsg = "Enter a valid email address";
    let pattern = /^([_\-\.0-9a-zA-Z]+)@([_\-\.0-9a-zA-Z]+)\.([a-zA-Z]){2,7}$/;
    const verdict = pattern.test ($email.val());
    return update($email, $emailError, errMsg, verdict);
}

function validatePassword ($pwd, $pwdError, applyRule = true) {
    const errMsg = "Enter a valid password of length at least 5";
    const verdict = ($pwd.val().length >= 5 || applyRule === false);
    return update($pwd, $pwdError, errMsg, verdict);
}

function validateConfirmPassword ($pwd, $confpwd, $confpwdError) {
    const errMsg = "Passwords do not match";
    const verdict = ($pwd.val() === $confpwd.val() && $pwd.val() !== '');
    return update($confpwd, $confpwdError, errMsg, verdict);
}

function validateTnC ($tncCheckBox) {
    if ($tncCheckBox.is(":checked")) {
        $tncCheckBox.removeClass('is-invalid');
        return true;
    }
    else {
        $tncCheckBox.addClass('is-invalid');
        return false;
    }
}

function togglePassIcon ($passIcon) {
    $passInput = $passIcon.parents(".input-group").find("input");
    $passIcon = $passIcon.find(".fas");
    if ($passInput.attr("type") === "password") {
        $passInput.attr("type", "text");
        $passIcon.attr("class", "fas fa-eye");
    }
    else {
        $passInput.attr("type", "password");
        $passIcon.attr("class", "fas fa-eye-slash");
    }
}
