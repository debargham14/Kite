<?php

// error variable.
$error = [];

// valiadate name
$fullName_data = validateName($_POST["fullName"]);
$fullName = $fullName_data["value"];
if (isset($fullName_data["error"]))
    $error["fullName"] = $fullName_data["error"];

// validate email
$email_data = validateEmail($_POST["email"]);
$email = $email_data["value"];
if (isset($email_data["error"]))
    $error["email"] = $email_data["error"];
else {
    // check if user is already registered
    $data = getUserInfo($conn, $email);
    if (!empty($data))
        $error["email"] = "Email id already registered";
}

// validate password
$password_data = validatePassword($_POST["password"]);
$password = $password_data["value"];
if (isset($password_data["error"]))
    $error["password"] = $password_data["error"];

// validate confirm password
$confirmPassword_data = validateConfirmPassword($_POST["confirmPassword"], $_POST["password"]);
$confirmPassword = $confirmPassword_data["value"];
if (isset($confirmPassword_data["error"]))
    $error["confirmPassword"] = $confirmPassword_data["error"];

try {
    if(empty($error)) {
        // encrypt the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // add an entry to the table of users
        $query = "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)";
        $stmt = $conn -> prepare($query);
        $stmt -> bind_param("sss", $fullName, $email, $hashedPassword);
        $stmt -> execute();

        // redirect to login page on successful login
        if($stmt -> affected_rows === 1)
            header("location: login.php");
        else
            $error["special"] = "Error while registration...!";
    }
} catch (Error $e) {
    $error["special"] = $e;
}