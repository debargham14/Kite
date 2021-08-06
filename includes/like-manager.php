<?php
require ('helper.php');
require ('dbconfig.php');

if (isset($conn)) {
    // create a new LikeHelper object
    $like = new LikeHelper($conn, $_POST['postId'], $_SESSION['userId']);
    if (isset($_POST['unliked'])) {
        // if post is unliked, add a like to make it liked
        echo $like->add();
    }
    if (isset($_POST['liked'])) {
        // if post is liked, remove the like to make it unliked
        echo $like->remove();
    }
}