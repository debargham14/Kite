<?php
require ("../includes/helper.php");
require ("../includes/dbconfig.php");

$userId = $_SESSION["userId"];
$postId = $_POST["postId"];
$data = [];

try {
    // get the post details
    $query = "SELECT thumbnail, content, created_by FROM posts WHERE id = ?";
    $stmt = $conn -> prepare($query);
    $stmt -> bind_param("i", $postId);
    $stmt -> execute();
    $result = $stmt -> get_result();
    $postDetails = $result -> fetch_assoc();

    // verify if the user requesting delete is the author herself
    if ($postDetails['created_by'] !== $userId) {
        throw new Exception("You are not authorized to delete this post");
    }
    if ($result -> num_rows === 1) {
        $thumbnailFile = $postDetails["thumbnail"];
        $contentFile = $postDetails["content"];
        // delete the post
        $query = "DELETE FROM posts WHERE posts.id=?";
        $stmt = $conn -> prepare($query);
        $stmt -> bind_param("i", $postId);
        $stmt -> execute();

        // delete the thumbnail and content file
        if ($stmt -> affected_rows > 0) {
            if ($thumbnailFile !== 'default-thumbnail.jpg') {
                $targetDir = "../".getAssetsPath()."post/thumbnail/";
                unlink($targetDir.$thumbnailFile);
            }
            $targetDir = "../".getAssetsPath()."post/content/";
            unlink($targetDir.$contentFile);
            $data["success"] = true;
        }
        else
            throw new Exception("Post could not be deleted. Database updation failed");
    }
    else {
        throw new Exception("Post was not found.");
    }
} catch (Exception $e) {
    $data["success"] = false;
    $data["error"] = $e->getMessage();
} catch (Error $e) {
    $data["success"] = false;
    $data["error"] = $e->getMessage();
} finally {
    echo json_encode($data);
}
