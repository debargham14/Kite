<?php
require ("../includes/helper.php");
require ("../includes/dbconfig.php");

$data = [];
$value = [];
$error = [];

// get user id - the post author's id
$userId = $_SESSION['userId'];

// validate title
$title_data = validateText($_POST['title'], true);
$value['title'] = $title_data['value'];
if (isset($title_data['error']))
    $error['title'] = $title_data['error'];

// get the categories for the post
$value['categories'] = json_decode(stripslashes($_POST["categories"]));
if (empty($value['categories']))
    $value['categories'] = array(1);

$value['thumbnail'] = "default-thumbnail.jpg";

// upload thumbnail (if any, else default)
if (isset($_FILES["thumbnail"]) && empty($error)) {
    // set the directory to save the thumbnail
    $targetDir = "../".getAssetsPath()."post/thumbnail/";

    // allowed file extensions
    $allowType = array('jpg', 'png', 'jpeg');

    // upload the file
    $uploadFileInfo = uploadFile($targetDir, $_FILES["thumbnail"], $allowType, "thumbnail", $userId);
    if ($uploadFileInfo['ok'] === true) {
        // get uploaded filename
        $value['thumbnail'] = $uploadFileInfo['name'];
    }
    else
        $error['thumbnail'] = $uploadFileInfo['error'];
}

// validate post content
$content = $_POST['content'];
$defaultContent = "<p><br></p>";
if ($content === $defaultContent || $content === "")
    $error["content"] = "This field cannot be empty";

else if (empty($error)) {
    // set the directory to save the content file
    $targetDir = "../".getAssetsPath()."post/content/";

    // // upload the file
    $createContentFileInfo = createContentFile($targetDir, $content, $userId);
    if ($createContentFileInfo['ok'] === false) {
        $error["content"] = "Content could not be uploaded";

        // if error while uploading content file, delete the thumbnail file as well
        $targetDir = "../".getAssetsPath()."post/thumbnail/";
        unlink($targetDir.$value['thumbnail']);
        $value['thumbnail'] = "default-thumbnail.jpg";
    }
    else
        // get uploaded filename
        $value['content'] = $createContentFileInfo['name'];
}

$data["success"] = false;

try {
    if (empty($error)) {
        // insert a new entry for the post
        $query = "INSERT INTO posts (title, thumbnail, content, created_by) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt ->bind_param("sssi", $value['title'], $value['thumbnail'], $value['content'], $userId);
        $stmt -> execute();
        if ($stmt -> affected_rows === 1) {
            // get the id of the inserted post
            $postId = $stmt -> insert_id;
            foreach ($value['categories'] as $categoryId) {
                // register pairs of post - category
                $query = "INSERT into `post-category` (post_id, category_id) VALUES ($postId, $categoryId)";
                $conn -> query($query);
            }
            $data['success'] = true;
            // get the post url
            $data['url'] = "post.php?id=".$postId;
        }
        else {
            $error['special'] = "Error while updating in the database";
        }
    }
} catch (Error $e) {
    $error['special'] = $e;
} finally {
    $data['errors'] = $error;
    // if any error occurs while post creation, delete both thumbnail and content files
    if (isset($error['special'])) {
        $targetDir = "../".getAssetsPath()."post/thumbnail/";
        unlink($targetDir.$value['thumbnail']);
        $targetDir = "../".getAssetsPath()."post/content/";
        unlink($targetDir.$value['content']);
    }
    echo json_encode($data);
}
