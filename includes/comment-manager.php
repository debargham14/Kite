<?php
require ('dbconfig.php');
require ('helper.php');

if (isset($_POST['add_comment']) && isset($conn)) {
    $comment = new CommentHelper($conn, $_POST['postId'], $_SESSION['userId']);
    echo $comment -> add ($_POST['commentText']);
}