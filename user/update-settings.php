<?php
require ("../includes/dbconfig.php");
require ("../includes/helper.php");
// error variable.
$value = [];
$error = [];
$data = [];
$userId = $_SESSION["userId"];

// validate the new details
$shortDescription_data = validateText($_POST['shortDescription']);
$value['shortDescription'] = $shortDescription_data['value'];
if (isset($shortDescription_data['error']))
    $error['shortDescription'] = $shortDescription_data['error'];

$education_data = validateText($_POST['education']);
$value['education'] = $education_data['value'];
if (isset($education_data['error']))
    $error['education'] = $education_data['error'];

$skills_data = validateText($_POST['skills']);
$value['skills'] = $skills_data['value'];
if (isset($skills_data['error']))
    $error['skills'] = $skills_data['error'];

$bio_data = validateText($_POST['bio']);
$value['bio'] = $bio_data['value'];
if (isset($bio_data['error']))
    $error['bio'] = $bio_data['error'];

$data["success"] = false;
$data["values"] = $value;

try {
    if(empty($error)) {
        // update into the table of users
        $query = "UPDATE users SET short_description=?, education=?, skills=?, bio=? WHERE id = $userId";
        $stmt = $conn->prepare($query);
        $stmt ->bind_param("ssss", $value['shortDescription'], $value['education'], $value['skills'], $value['bio']);
        $stmt -> execute();
        $data["success"] = true;
    }
    else {
        $data["errors"] = $error;
    }
} catch (Error $e) {
    $error["special"] = $e;
    $data["errors"] = $error;
} finally {
    echo json_encode($data);
}