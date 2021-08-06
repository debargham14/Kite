<?php
// error variable.
$error = [];

// validate email
$email_data = validateEmail($_POST["email"]);
$email = $email_data["value"];
if (isset($email_data["error"]))
    $error["email"] = $email_data["error"];

// validate password
$password_data = validatePassword($_POST["password"], false);
$password = $password_data["value"];
if (isset($password_data["error"]))
    $error["password"] = $password_data["error"];

if(empty($error)){
    $data = getUserInfo($conn, $email);
    if (!empty($data)){
        // verify password
        if(password_verify($password, $data['password'])){
            // set session variables on successful login
            $_SESSION['userLoggedIn'] = true;
            $_SESSION['userId'] = $data['id'];
            $_SESSION['userName'] = $data['fullname'];
            $_SESSION['avatar'] = $data['profile_image'];
            header("location: index.php");
            exit();
        }
        else {
            $error['password'] = "Enter correct password";
        }
    }
    else {
        $error['email'] = "Email id not registered";
    }
}