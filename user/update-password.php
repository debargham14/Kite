<?php
require ("../includes/dbconfig.php");
require ("../includes/helper.php");
// error variable.
$error = [];
$data = [];
$userId = $_SESSION["userId"];

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

$data["success"] = false;

try {
    if(empty($error)) {
        // encrypt the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // update the password of the user
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt ->bind_param("si", $hashedPassword, $userId);
        $stmt -> execute();
        if ($stmt->affected_rows === 1) {
            $data["success"] = true;
        } else {
            $data["errors"] = "Internal error. Please try later !";
        }
    }
    else {
        $data["errors"] = $error;
    }
} catch (Error $e) {
    $data["errors"] = $e;
} finally {
    echo json_encode($data);
}