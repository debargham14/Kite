<?php
require ('includes/dbconfig.php');
require('includes/helper.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kite</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
        .like, .unlike {
            cursor: pointer;
        }
        .like:hover, .unlike:hover {
            color: #6c757d !important;
        }
    </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

    <!-- Navbar -->
    <?php include_once ('includes/navbar.php');?>
    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Main content -->
        <div class="content">
            <div class="container pt-4">
                <div class="row">
                    <!-- Main Post -->
                    <div class="col">
                        <!-- Box Comment -->
                        <div class="card card-widget">
                            <div class="card-header">
                                <?php
                                // Fetch post id from url
                                $postId = $_GET['id'];

                                // Fetch the posts matching the post id
                                $postQuery = "SELECT * FROM posts WHERE id=$postId";
                                $result = $conn -> query($postQuery);
                                $curPost = $result -> fetch_assoc();
                                ?>
                                <!-- Card Title -->
                                <div class="container">
                                    <div class="row mb-2">
                                        <h1 class="mb-1"><?=$curPost['title']?></h1>
                                    </div>
                                    <div class="row mb-2">
                                        <?php
                                        // display the categories to which the post belongs
                                        $categories = getCategoriesByPost($conn, $curPost['id']);
                                        if ($categories !== false) {
                                            foreach ($categories as $category) {
                                                ?>
                                                <a href="index.php?category=<?=$category['id']?>" class="badge badge-info mr-2 mb-2"><?=$category["name"]?></a>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <!-- ./ Card Title -->
                                <?php
                                $userQuery = "SELECT * FROM users WHERE id={$curPost['created_by']}";
                                $result = $conn -> query($userQuery);
                                $user = $result -> fetch_assoc();
                                ?>
                                <!-- user-block -->
                                <div class="user-block">
                                    <img class="img-circle" src="<?=getAssetsPath()?>profile/<?=$user['profile_image']?>" alt="User Image">
                                    <span class="username"><a href="user.php?id=<?=$user['id']?>"><?=$user['fullname']?></a></span>
                                    <span class="description">Posted on <?=date('F jS, Y', strtotime($curPost['created_at']))?> at <?=date('h:i a', strtotime($curPost['created_at']))?></span>
                                </div>
                                <!-- /.user-block -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <!-- Post Content -->
                                <p><?=fetchContent(getAssetsPath().'post/content/', $curPost['content'])?></p>
                                <!-- / .Post Content -->

                                <!-- Like Button -->
                                <?php
                                if (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true) {
                                    $like = @new LikeHelper($conn, $postId, $_SESSION['userId']);
                                    if ($like->getUserLikedStatus() === true) {
                                        ?>
                                        <a class="liked-btn like link-black text-sm" data-id="<?=$postId?>"><i class="fas fa-thumbs-up"> Liked</i></a>
                                        <a class="unliked-btn unlike d-none link-black text-sm" data-id="<?=$postId?>"><i class="far fa-thumbs-up"> Like</i></a>
                                        <?php
                                    }
                                    else {
                                        ?>
                                        <a class="liked-btn like d-none link-black text-sm" data-id="<?=$postId?>"><i class="fas fa-thumbs-up"> Liked</i></a>
                                        <a class="unliked-btn unlike link-black text-sm" data-id="<?=$postId?>"><i class="far fa-thumbs-up"> Like</i></a>
                                        <?php
                                    }
                                }
                                else {
                                    echo '<a href="login.php"><button class="btn btn-sm btn-default">Login to like and comment</button></a>';
                                }
                                ?>
                                <!-- ./ Like Button -->
                                <!-- Number of Likes and Comments -->
                                <span class="float-right text-muted"><span id="likeCount"><?=$curPost['likes']?></span> likes - <span id="commentCount"><?=$curPost['comments']?></span> comments</span>
                            </div>
                            <!-- /.card-body -->

                            <?php
                            if (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true) {
                                if ($curPost['comments'] < 1)
                                    $commentPlaceHolder = 'Be the first one to comment...';
                                else
                                    $commentPlaceHolder = 'Write your comment here...';
                                ?>
                                <!-- /.add-comment -->
                                <div class="card-footer">
                                    <form class="add-comment-form form-horizontal" action="includes/comment-manager.php" method="post">
                                        <img id="user-avatar" class="img-fluid img-circle img-sm" src="<?=getAssetsPath()?>profile/<?=$_SESSION["avatar"]?>" alt="Alt Text">
                                        <!-- .img-push is used to add margin to elements next to floating images -->
                                        <div class="img-push">
                                            <div class="input-group input-group-sm mb-0">
                                                <input type="text" name="commentText" class="add-comment-text form-control form-control-sm" placeholder="<?=$commentPlaceHolder?>">
                                                <div class="input-group-append">
                                                    <p class="user d-none"><?=$_SESSION["userName"]?></p>
                                                    <input type="text" name="postId" class="post-id d-none" value="<?=$postId?>">
                                                    <button type="submit" name="commentBtn" class="add-comment-btn btn btn-warning">Comment</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.add-comment -->
                                <?php
                            }
                            ?>
                            <!-- Posted comments -->
                            <div class="card-footer card-comments">
                                <?php
                                $comments = getCommentsByPost($conn, $postId);
                                foreach($comments as $comment) {
                                    $result = $conn -> query("SELECT fullname, profile_image FROM users WHERE id={$comment['created_by']}");
                                    $user = $result -> fetch_assoc();
                                    ?>
                                    <div class="card-comment">
                                        <!-- User image -->
                                        <img class="img-circle img-sm" src="<?=getAssetsPath()?>profile/<?=$user["profile_image"]?>" alt="User Image">
                                        <!-- Comment-Text -->
                                        <div class="comment-text">
                                            <span class="username"><?=$user["fullname"]?>
                                                <span class="text-muted float-right">
                                                    <?= $comment['created_at']?>
                                                </span>
                                            </span>
                                            <?=$comment["text"]?>
                                        </div>
                                        <!-- /.Comment-Text -->
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <!-- /.Posted Comments -->
                            <!-- Share Buttons -->
                            <div class="card-footer">
                                <!-- Go to www.addthis.com/dashboard to customize your tools -->
                                <div class="addthis_inline_share_toolbox"></div>
                            </div>
                            <!-- /.Share Buttons -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    <!-- /. Main Post -->

                    <div class="col-md-4">
                        <!-- Featured Posts -->
                        <?php include_once ('includes/featured.php');?>
                        <!-- /. Featured Posts -->

                        <!-- Related Posts -->
                        <?php
                        // Fetch posts from table 'posts' which have same category as the current category
                        $query = "SELECT * FROM posts WHERE id IN (SELECT DISTINCT post_id from `post-category` WHERE category_id IN (SELECT category_id from `post-category` WHERE post_id={$curPost['id']}) AND post_id <> {$curPost['id']}) ORDER BY id DESC";
                        $result = $conn -> query($query);
                        if ($result -> num_rows === 0)
                            echo '<h5>No related posts</h5>';
                        else
                            echo '<h4>Related Posts</h4>';
                        while ($relatedPost = $result -> fetch_assoc()) {
                            generatePostCardView ($conn, $relatedPost);
                        }
                        ?>
                        <!-- /. Related Posts -->
                    </div>
                </div>

            </div>
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Footer -->
    <?php include_once ('includes/footer.php');?>
    <!-- /.Footer -->

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Toastr -->
<script src="plugins/toastr/toastr.min.js"></script>
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-60ed4be244926228"></script>
<script src="dist/js/pages/post.js"></script>
</body>
</html>