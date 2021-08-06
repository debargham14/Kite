<?php
// Function to resolve path of assets
function getAssetsPath () {
    return 'assets/';
}

// Function to validate text
function validateText ($text, $required=false, $errMsg="Enter a valid text") {
    $text = trim ($text);
    if (empty($text))
        if ($required === true)
            return array("value" => $text, "error" => "This field cannot be empty");
        else
            return array("value" => $text);
    $data = [];
    $data["value"] = htmlspecialchars($text);

    // check if the entered text contains illegal characters
    if ($text !== $data["value"])
        $data["error"] = $errMsg;
    return $data;
}

// Function to validate email
function validateName ($name) {
    $name = trim($name);
    if (empty($name))
        return array("value" => $name, "error" => "This field cannot be empty");
    $data["value"] = htmlspecialchars($name);

    // use regex match to check if the naame is valid
    if (!preg_match("/^[a-zA-Z-' ]*$/",$data['value']))
        $data["error"] = "Only letters and white spaces allowed";
    return $data;
}

// Function to validate email
function validateEmail ($email) {
    $email = trim($email);
    if (empty($email))
        return array("value" => $email, "error" => "This field cannot be empty");
    $data["value"] = htmlspecialchars($email);
    
    // validate email address
    if (!filter_var($data["value"], FILTER_VALIDATE_EMAIL))
        $data["error"] = "Enter a valid email address";
    return $data;
}

// Function to validate password
function validatePassword ($password, $applyRule = true) {
    if (empty($password))
        return array("value" => $password, "error" => "This field cannot be empty");
    $data = [];
    $data["value"] = htmlspecialchars($password);

    // check if the password contains illegal characters
    if ($password !== $data["value"] || (strlen($password) < 5 && $applyRule === true))
        $data["error"] = "Enter a valid password of length at least 5";
    return $data;
}

// Function to validate confirm password
function validateConfirmPassword ($confirmPassword, $password = "") {
    if (empty($confirmPassword))
        return array("value" => $confirmPassword, "error" => "This field cannot be empty");
    $data = [];
    $data["value"] = $confirmPassword;

    // check if password and confirm password values are same
    if ($password != $confirmPassword)
        $data["error"] = "Passwords don't match";
    return $data;
}


// Function to get all the categories present
function getCategories ($conn) {
    $data = array();        // to store the categories
    try {
        $query = "SELECT * FROM categories ORDER BY id DESC";
        $result = $conn -> query ($query);
        while ($row = $result -> fetch_assoc())
            $data[] = $row;
    } catch (Error $e) {
        return false;
    } finally {
        return $data;
    }
}

// Function to get the categories a particular post by its post id
function getCategoriesByPost ($conn, $postId) {
    $data = array();        // to store the categories
    try {
        $query = "SELECT * FROM categories WHERE id IN (SELECT category_id FROM `post-category` WHERE post_id = ?)";
        $stmt = $conn->prepare($query);
        $stmt ->bind_param("i", $postId);
        $stmt -> execute();
        $result = $stmt -> get_result();
        while ($row = $result -> fetch_assoc())
            $data[] = $row;
    } catch (Error $e) {
        $data[] = array("id" => "#", "name" => "Not available");
    } finally {
        return $data;
    }
}

// Function to get comments on a particular post
function getCommentsByPost ($conn, $postId) {
    $data = array();        // to store the comments

    $query = "SELECT * FROM comments WHERE post_id=? ORDER BY id DESC";
    $stmt = $conn->prepare($query);
    $stmt ->bind_param("i", $postId);
    $stmt -> execute();
    $result = $stmt -> get_result();

    while ($row = $result -> fetch_assoc())
        $data[] = $row;
    return $data;
}

// get user info by email
function getUserInfo($conn, $email){
    $query = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($query);
    $stmt ->bind_param("s", $email);
    $stmt -> execute();
    $result = $stmt -> get_result();
    return $result -> fetch_assoc();
}

// get user info by user id
function getUserInfoById ($conn, $id){
    $query = "SELECT * FROM users WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt ->bind_param("i", $id);
    $stmt -> execute();
    $result = $stmt -> get_result();

    // if found
    if ($result -> num_rows === 1)
        return $result -> fetch_assoc();
    else
        return false;
}

// generate card view of a post
function generatePostCardView ($conn, $post, $postAuthor = null) {
    // get author info if not provided by user
    if ($postAuthor === null)
        $postAuthor = getUserInfoById($conn, $post['created_by']);

    // if found
    if ($postAuthor !== false) {
        ?>
        <a href="post.php?id=<?=$post['id']?>" style="text-decoration: none; color: black;">
            <div class="card">
                <!-- Post thumbnail -->
                <div class="p-2">
                    <img src="<?=getAssetsPath()?>post/thumbnail/<?=$post["thumbnail"]?>" class="card-img-top" style="height: 250px" alt="...">
                </div>

                <!-- Post Author Details -->
                <div class="card-body">
                    <h5 class="card-title mb-3" style="color: #505050"><?=$post['title']?></h5>
                    <p class="card-text" style="color: #444444">
                        <a href="user.php?id=<?=$postAuthor['id']?>">
                            <img class="mh-100 mw-100 img-circle" style="height: 20px; width: 20px" src="<?=getAssetsPath()?>profile/<?=$postAuthor['profile_image']?>" alt="User Image">
                            <span class="username" style="color:grey"><?=$postAuthor['fullname']?></span>
                        </a>
                    </p>
                </div>

                <!-- Post Creation Date -->
                <div class="card-footer">
                    <small class="text-muted">Posted on <?=date('F jS, Y', strtotime($post['created_at']))?></small>
                </div>
            </div>
        </a>
        <?php
    }
}

// Function to create a content file
function createContentFile ($targetDir, $content, $id) {
    try {
        // rename the file
        $fileName = $id.'-content-'.time().'.txt';
        $file = fopen($targetDir.$fileName, "w");

        // write the html content into the file and save it
        fwrite($file, $content);
        fclose($file);
        return array("ok" => true, "name" => $fileName);
    } catch (Error $e) {
        return array("ok" => false, "error" => $e);
    }
}

function fetchContent ($targetDir, $fileName) {
    try {
        // resolve file path
        $filePath = $targetDir.$fileName;
        $file = fopen($filePath, "r");

        // read the file content and return the same
        $content = fread($file, filesize($filePath));
        fclose($file);
        return $content;

    } catch (Error $e) {
        return $e;
    }
}

// upload a file
function uploadFile ($targetDir, $file, $allowType, $category, $id){
    // get the filename
    $filename = basename($file['name']);
    if(!empty($filename)) {
        // get the file extension
        $fileType = pathinfo($filename, PATHINFO_EXTENSION);

        // check if the file extension is allowed
        if(in_array($fileType, $allowType)){
            $mb = 1024*1024;    // 1 mb = 1024*1024 bytes

            // set max limit as 3mb
            if ($file['size'] <= 3*$mb) {
                // rename the file
                $newFileName = $id.'-'.$category.'-'.time().'.'.$fileType;
                $targetFilePath = $targetDir.$newFileName;

                // upload file to the server
                if(move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                    return array("ok" => true, "name" => $newFileName);
                }
                else
                    return array("ok" => false, "error" => "Possible file upload attack");
            }
            else
                return array("ok" => false, "error" => "File size must be less than 10 MB");
        }
        else
            return array("ok" => false, "error" => "File type must be jpg, png, jpeg, gif or pdf");
    }
    else
        return array("ok" => false, "error" => "File name cannot be empty");
}

// class to manage the like and unlike action by user on a post
class LikeHelper {
    private mysqli $db;
    private int $postId;
    private int $userId;

    function __construct ($db, $postId, $userId) {
        $this->db = $db;
        $this->postId = $postId;
        $this->userId = $userId;
    }

    // function to check if user has liked on the post
    public function getUserLikedStatus () {
        $query = "SELECT created_by FROM likes WHERE post_id = ? AND created_by = ?";
        $stmt = $this->db ->prepare($query);
        $stmt ->bind_param("ii",$this->postId, $this->userId);
        $stmt -> execute();
        $result = $stmt -> get_result();
        if ($result -> num_rows === 1)
            return true;
        else
            return false;
    }

    // function to manage like a post event
    public function add () {
        try {
            $data = [];
            $data["success"] = false;
            if($this->getUserLikedStatus() === false) {
                // register a new entry in the likes table
                $query = "INSERT INTO likes(post_id, created_by) VALUES (?, ?)";
                $stmt = $this->db ->prepare($query);
                $stmt ->bind_param("ii",$this->postId, $this->userId);
                $stmt -> execute();

                if ($stmt->affected_rows === 1) {
                    // update total no of likes for the post
                    $query = "UPDATE posts SET likes = likes + 1 WHERE id = ?";
                    $stmt = $this->db ->prepare($query);
                    $stmt ->bind_param("i",$this->postId);
                    $stmt -> execute();

                    if ($stmt->affected_rows === 1) {
                        $data["success"] = true;
                    }
                }
            }
        }
        catch (Error $e) {
            $data["error"] = "Failed to connect to server: ".($this->db) ->connect_errno;
        } finally {
            return json_encode($data);
        }
    }

    public function remove () {
        try {
            $data = [];
            $data["success"] = false;
            // delete the entry in the likes table
            $query = "DELETE FROM likes WHERE post_id=? AND created_by=?";
            $stmt = $this->db ->prepare($query);
            $stmt ->bind_param("ii",$this->postId, $this->userId);
            $stmt -> execute();

            if ($stmt->affected_rows === 1) {
                // update total no of likes for the post
                $query = "UPDATE posts SET likes = likes - 1 WHERE id = ?";
                $stmt = $this->db ->prepare($query);
                $stmt ->bind_param("i",$this->postId);
                $stmt -> execute();

                if ($stmt->affected_rows === 1) {
                    $data["success"] = true;
                }
            }
            else
                $data["error"] = "Error while updating in database";
        }
        catch (Error $e) {
            $data["error"] = "Failed to connect to server: ".($this->db) ->connect_errno;
        } finally {
            return json_encode($data);
        }
    }
}

class CommentHelper {
    private mysqli $db;
    private int $postId;
    private int $userId;

    function __construct ($db, $postId, $userId) {
        $this->db = $db;
        $this->postId = $postId;
        $this->userId = $userId;
    }

    public function add ($commentText) {
        try {
            $data = [];
            $data["success"] = false;

            // validate the comment text
            $comment_data = validateText($commentText, true);
            $commentText = $comment_data["value"];
            if (isset($comment_data['error']))
                $data["error"] = $comment_data['error'];

            else {
                // register a new entry in the comments table
                $query = "INSERT INTO comments(text, post_id, created_by) VALUES (?, ?, ?)";
                $stmt = $this->db ->prepare($query);
                $stmt ->bind_param("sii", $commentText, $this->postId, $this->userId);
                $stmt -> execute();

                if ($stmt -> affected_rows === 1) {
                    // update total no of comments for the post
                    $query = "UPDATE posts SET comments = comments + 1 WHERE id = ?";
                    $stmt = $this->db ->prepare($query);
                    $stmt ->bind_param("i",$this->postId);
                    $stmt -> execute();
                    if ($stmt -> affected_rows === 1) {
                        $data["success"] = true;
                    }
                }
                else
                    $data["error"] = "Error while updating in database";
            }
        }
        catch (Error $e) {
            $data["error"] = "Failed to connect to server: ".($this->db) ->connect_errno;
        } finally {
            return json_encode($data);
        }
    }
}