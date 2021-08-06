<?php
require ("../includes/helper.php");
require ("../includes/dbconfig.php");

$data = [];
$userId = $_SESSION["userId"];
$file = $_FILES["profileImage"];

$default = "avatar.png";
// set the directory to save the profile image
$targetDir = "../".getAssetsPath()."profile/";
// allowed file formats
$allowType = array('jpg', 'png', 'jpeg', 'gif');

$uploadFileInfo = uploadFile($targetDir, $file, $allowType, "profile-pic", $userId);
$data['success'] = false;

try {
    if ($uploadFileInfo['ok']) {
        // get name of the old profile image
        $query = "SELECT profile_image FROM users WHERE id = $userId";
        $result = $conn -> query($query);
        $row = $result -> fetch_assoc();
        $oldFileName = $row["profile_image"];

        // delete the old profile image
        if ($oldFileName != $default) {
            unlink($targetDir.$oldFileName);
        }

        // update to the new image
        $query = "UPDATE users SET profile_image = '".$uploadFileInfo['name']."' WHERE id = $userId";
        $conn -> query($query);
        if ($conn -> affected_rows === 1) {
            $data['success'] = true;

            // get the image url
            $data['imgUrl'] = $uploadFileInfo['name'];
            $_SESSION['avatar'] = $data['imgUrl'];
        }
    }
    else {
        $data['errors'] = $uploadFileInfo["error"];
    }
} catch (Error $e) {
    $data['errors'] = $e;
} finally {
    echo json_encode($data);
}
