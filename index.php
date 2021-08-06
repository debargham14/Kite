<?php
require ('includes/dbconfig.php');
require('includes/helper.php');
if (isset($_GET['page'])) {
    $curPage = $_GET['page'];
}
else
    $curPage = 1;
$posts_per_page = 6;
$currentPosts = ($curPage - 1) * $posts_per_page;
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
            <div class="container pt-2">
                <div class="row">
                    <!-- Main Posts -->
                    <div class="col-md-8">
                        <div class="row mx-auto">
                            <?php
                            // get the posts according to the url
                            $postQuery = "SELECT * FROM posts";

                            // if category is selected
                            if (isset($_GET['category'])) {
                                $catId = $_GET['category'];
                                $postQuery .= " WHERE id IN (SELECT post_id FROM `post-category` WHERE category_id = $catId)";

                                // and there is also a search request
                                if (isset($_GET['search'])) {
                                    $keyword = $_GET['search'];
                                    $postQuery .= " AND title LIKE '%$keyword%'";
                                }
                            }

                            // if only search request is there
                            else if (isset($_GET['search'])) {
                                $keyword = $_GET['search'];
                                $postQuery .= " WHERE title LIKE '%$keyword%'";
                            }
                            $result = $conn -> query($postQuery);
                            $total_posts = $result -> num_rows;

                            // sort the posts by their creation time, show the latest on top
                            $postQuery .= " ORDER BY id DESC LIMIT $currentPosts, $posts_per_page";
                            $result = $conn -> query($postQuery);

                            // if no posts are found
                            if ($result -> num_rows === 0)
                                echo '<h5>Sorry no posts to display</h5>';
                            echo '<div class="row">';

                            // show the posts
                            while ($post = $result -> fetch_assoc()) {
                                echo '<div class="col-sm-6">';
                                generatePostCardView($conn, $post);
                                echo '</div>';
                            }
                            echo '</div>';
                            ?>
                        </div>
                        <div class="row mt-auto justify-content-center">
                            <!-- Pagination -->
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-center">
                                    <?php
                                    // get the total pages needed to show the posts matching current criteria
                                    $total_pages = ceil ($total_posts/$posts_per_page);

                                    // manipulate the previous and next links based on current page
                                    if ($curPage > 1)
                                        $prev_switch = "";
                                    else
                                        $prev_switch = "disabled";

                                    if ($curPage < $total_pages)
                                        $next_switch = "";
                                    else
                                        $next_switch = "disabled";

                                    // get the page link according to the criteria
                                    $page_link = "?";
                                    if (isset($_GET['search'])) {
                                        $keyword = $_GET['search'];
                                        $page_link .= "search=$keyword&";
                                    }
                                    if (isset($_GET['category'])) {
                                        $catId = $_GET['category'];
                                        $page_link .= "category=$catId&";
                                    }
                                    ?>
                                    <li class="page-item <?=$prev_switch?>">
                                        <a class="page-link"  href="<?=$page_link?>page=<?=$curPage-1?>" tabindex="-1" aria-disabled="true">Previous</a>
                                    </li>
                                    <?php
                                    for ($page=1; $page <= $total_pages; $page++) {
                                        ?>
                                        <li class="page-item"><a class="page-link" href="<?=$page_link?>page=<?=$page?>"><?=$page?></a></li>
                                        <?php
                                    }
                                    ?>
                                    <li class="page-item <?=$next_switch?>">
                                        <a class="page-link" href="<?=$page_link?>page=<?=$curPage+1?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                            <!-- Pagination -->
                        </div>
                    </div>
                    <!-- /. Main Posts -->

                    <div class="col-md-4">
                        <!-- Featured Posts -->
                        <?php include_once ('includes/featured.php');?>
                        <!-- /. Featured Posts -->
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
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>