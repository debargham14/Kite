<?php
require ("includes/dbconfig.php");
require ("includes/helper.php");
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
    <!-- Toastr -->
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
    <!-- Filepond stylesheet -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css" />
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css"/>
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <!-- CodeMirror -->
    <link rel="stylesheet" href="plugins/codemirror/codemirror.css">
    <link rel="stylesheet" href="plugins/codemirror/theme/monokai.css">
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
        <section class="content">
            <div class="container pt-4">
                <?php
                // get details of the user
                $userId = $_GET["id"];
                $user = getUserInfoById($conn, $userId);
                if ($user === false) {
                    header("location: error404.php");
                }
                $postCount = 0;
                $likeCount = 0;

                // get details of the posts made by the user
                $query = "SELECT * from posts WHERE created_by=$userId ORDER BY id DESC";
                $posts = $conn -> query($query);
                ?>
                <div class="row">
                    <div class="col-md-3">
                        <!-- Profile Image Section -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-avatar profile-user-img img-fluid img-circle"
                                         src="<?=getAssetsPath()?>profile/<?=$user["profile_image"]?>"
                                         alt="User profile picture">
                                </div>

                                <h3 class="profile-username text-center"><?=$user["fullname"]?></h3>

                                <p class="short-description text-muted text-center"><?=$user['short_description']?></p>

                                <?php
                                if ($userId == $_SESSION['userId']) {
                                    ?>
                                    <form class="form-horizontal" action="user/update-profile-image.php" method="post" id="profile-image-form" enctype="multipart/form-data">
                                        <div class="form-group row">
                                            <div class="col-12">
                                                <!-- Profile Image Upload Area-->
                                                <input id="profile-image-upload" class="filepond" name="profileImage" type="file"/>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                }
                                ?>

                                <!-- Posts and Likes received in the user's history -->
                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Posts</b> <a class="post-count float-right">0</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Likes</b> <a class="like-count float-right">0</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- /. Profile Image Section -->

                        <!-- About Me Section -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">About Me</h3>
                            </div>
                            <div class="card-body">
                                <strong><i class="far fa-file-alt mr-1"></i> Bio</strong>
                                <p class="bio text-muted"><?=$user['bio']?></p>
                                <hr>
                                <strong><i class="fas fa-book mr-1"></i> Education</strong>
                                <p class="education text-muted"><?=$user['education']?></p>
                                <hr>
                                <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>
                                <p class="skills text-muted"><?=$user['skills']?></p>
                            </div>
                        </div>
                        <!-- /.About Me Section -->
                    </div>
                    <!-- /.col -->

                    <div class="col-md-9">
                        <?php
                        // show this section only if the user logs into his own profile
                        if ($userId == $_SESSION['userId']) {
                            ?>
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"><a class="nav-link active" href="#create_post" data-toggle="tab">Create Post</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#my_posts" data-toggle="tab">My Posts</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#change_password" data-toggle="tab">Change Password</a></li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">

                                        <!-- Create Post Section -->
                                        <div class="active tab-pane" id="create_post">
                                            <section class="content">
                                                <form class="form-horizontal" action="user/add-post.php" method="post" id="create-post-form">

                                                    <!-- Title input -->
                                                    <div class="form-group row">
                                                        <label for="post-title" class="col-lg-2 col-form-label">Title</label>
                                                        <div class="col-lg-10">
                                                            <input type="text" name="title" class="form-control" id="post-title" value="" placeholder="Title">
                                                        </div>
                                                    </div>

                                                    <!-- Upload Thumbnail Area -->
                                                    <div class="form-group row">
                                                        <label for="post-thumbnail" class="col-lg-2 col-form-label">Thumbnail</label>
                                                        <div class="col-lg-10">
                                                            <input id="post-thumbnail" class="filepond" name="thumbnail" type="file"/>
                                                        </div>
                                                    </div>

                                                    <!-- Select categories of the post -->
                                                    <div class="form-group row">
                                                        <label for="post-categories" class="col-lg-2 col-form-label">Categories</label>
                                                        <div class="col-lg-10">
                                                            <select class="select2" id="post-categories" multiple="multiple" data-placeholder="Select categories" style="width: 100%;">
                                                                <?php
                                                                $categories = getCategories($conn);
                                                                foreach ($categories as $category) {
                                                                    echo '<option value="'.$category['id'].'">'.$category['name'].'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Summernote Editor for Post Content -->
                                                    <div class="form-group row">
                                                        <label for="post-content" class="col-lg-2 col-form-label">Content</label>
                                                        <div class="col-lg-10">
                                                            <textarea id="post-content" name="content"></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- Create Post Button -->
                                                    <div class="form-group row">
                                                        <div class="offset-lg-2 col-lg-10">
                                                            <button type="submit" class="btn btn-outline-primary">Create Post</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </section>
                                        </div>
                                        <!-- /. Create Post Section -->

                                        <!-- My Posts Section -->
                                        <div class="tab-pane" id="my_posts">
                                            <!-- The timeline -->
                                            <div class="timeline timeline-inverse">
                                                <?php
                                                if ($posts -> num_rows > 0) {
                                                    while ($post = $posts -> fetch_assoc()) {
                                                        $postCount += 1;
                                                        $likeCount += $post['likes'];
                                                        ?>
                                                        <!-- timeline item -->
                                                        <div>
                                                            <i class="fas fa-rss bg-primary"></i>
                                                            <div class="timeline-item">
                                                                <!-- Post Creation Time -->
                                                                <span class="time"><i class="far fa-clock"></i> <?=$post['created_at']?></span>

                                                                <!-- Post Categories -->
                                                                <h3 class="timeline-header"><b>Categories: </b>
                                                                    <?php
                                                                    $categories = getCategoriesByPost($conn, $post['id']);
                                                                    if ($categories !== false) {
                                                                        foreach ($categories as $category) {
                                                                            ?>
                                                                            <a href="index.php?category=<?=$category['id']?>" class="badge badge-warning"><?=$category["name"]?></a>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </h3>

                                                                <!-- Post Title -->
                                                                <div class="timeline-body"><b>Title: </b>
                                                                    <?=$post['title']?>
                                                                </div>

                                                                <!-- User actions on Post -->
                                                                <div class="timeline-footer">
                                                                    <a href="post.php?id=<?=$post['id']?>" class="btn btn-primary btn-sm">View Post</a>
                                                                    <button data-id="<?=$post['id']?>" class="delete-post-btn btn btn-danger btn-sm">Delete Post</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- END timeline item -->
                                                        <?php
                                                    }
                                                }
                                                ?>
                                                <div>
                                                    <i class="far fa-clock bg-gray"></i>
                                                    <div class="timeline-item" style="background-color: inherit; border: none;">
                                                        <?php if($postCount === 0) echo '<h5>You have not posted yet</h5>';?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /. My Posts Section -->

                                        <!-- User Settings Section -->
                                        <div class="tab-pane" id="settings">
                                            <form class="form-horizontal" action="user/update-settings.php" method="post" id="settings-form">
                                                <!-- Short Description -->
                                                <div class="form-group row">
                                                    <label for="shortDescription" class="col-sm-4 col-form-label">Short Description</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="shortDescription" class="form-control" id="shortDescription" value="<?=$user['short_description']?>" placeholder="Short Description">
                                                    </div>
                                                </div>

                                                <!-- Education -->
                                                <div class="form-group row">
                                                    <label for="education" class="col-sm-4 col-form-label">Education</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="education" class="form-control" id="education" value="<?=$user['education']?>" placeholder="Education">
                                                    </div>
                                                </div>

                                                <!-- Skills -->
                                                <div class="form-group row">
                                                    <label for="skills" class="col-sm-4 col-form-label">Skills</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="skills" class="form-control" id="skills" value="<?=$user['skills']?>" placeholder="Skills">
                                                    </div>
                                                </div>

                                                <!-- Bio -->
                                                <div class="form-group row">
                                                    <label for="bio" class="col-sm-4 col-form-label">Bio</label>
                                                    <div class="col-sm-8">
                                                        <textarea class="form-control" name="bio" id="bio" placeholder="Bio"><?=$user['bio']?></textarea>
                                                    </div>
                                                </div>

                                                <!-- Save Button -->
                                                <div class="form-group row">
                                                    <div class="offset-sm-4 col-sm-8">
                                                        <button type="submit" class="btn btn-outline-primary">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- /.User Settings Section -->

                                        <!-- Change Password Section -->
                                        <div class="tab-pane" id="change_password">
                                            <form action="user/update-password.php" method="post" id="password-form" class="form-horizontal" enctype="multipart/form-data">
                                                <!-- New Password Input -->
                                                <div class="form-group row">
                                                    <label for="password" class="col-sm-4 col-form-label">New Password</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="password" class="form-control" id="password" placeholder="Enter Password">
                                                    </div>
                                                </div>

                                                <!-- Confirm New Password-->
                                                <div class="form-group row">
                                                    <label for="confirmPassword" class="col-sm-4 col-form-label">Confirm New Password</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="Confirm New Password">
                                                    </div>
                                                </div>

                                                <!-- Submit button -->
                                                <div class="form-group row">
                                                    <div class="offset-sm-4 col-sm-8">
                                                        <button type="submit" class="btn btn-danger">Submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- /. Change Password Section -->

                                    </div>
                                    <!-- /.tab-content -->
                                </div><!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                            <?php
                        }
                        // if the user visits some other user's profile, display the posts in card view
                        else {
                            if ($posts -> num_rows > 0) {
                                echo '<h4>'.$user['fullname'].'\'s posts</h4><br>';
                                echo '<div class="row">';
                                while ($post = $posts -> fetch_assoc()) {
                                    echo '<div class="col-sm-6">';
                                    $postCount += 1;
                                    $likeCount += $post['likes'];
                                    generatePostCardView($conn, $post, $user);
                                    echo '</div>';
                                }
                                echo '</div>';
                            }
                            else {
                                echo '<h5>No posts yet</h5>';
                            }
                        }
                        ?>
                        <p class="get-post-count d-none"><?=$postCount?></p>
                        <p class="get-like-count d-none"><?=$likeCount?></p>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
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
<!-- Load FilePond library -->
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<!-- Babel polyfill, contains Promise -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.6.15/browser-polyfill.min.js"></script>


<!-- Get FilePond polyfills from the CDN -->
<script src="https://unpkg.com/filepond-polyfill/dist/filepond-polyfill.js"></script>


<!-- Get FilePond JavaScript and its plugins from the CDN -->
<script src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-crop/dist/filepond-plugin-image-crop.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>

<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- CodeMirror -->
<script src="plugins/codemirror/codemirror.js"></script>
<script src="plugins/codemirror/mode/css/css.js"></script>
<script src="plugins/codemirror/mode/xml/xml.js"></script>
<script src="plugins/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<!-- jquery-validation -->
<script src="plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="plugins/jquery-validation/additional-methods.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script src="dist/js/pages/user-profile.js"></script>
</body>
</html>