<nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">

        <!-- Brand Logo -->
        <a href="index.php" class="navbar-brand">
            <img src="<?=getAssetsPath()?>brand-logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light ml-2">Kite</span>
        </a>

        <!-- Navbar Collapse Control -->
        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <!-- Contact Us -->
                <li class="nav-item">
                    <a href="#" class="nav-link">Contact</a>
                </li>

                <!-- Categories -->
                <li class="nav-item dropdown">
                    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Categories</a>
                    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                        <?php
                        $categories = getCategories($conn);
                        if ($categories !== false) {
                            // Display every category with links attached
                            foreach ($categories as $category) {
                                ?>
                                <li><a href="index.php?category=<?=$category['id']?>" class="category-item dropdown-item"><?=$category["name"]?></a></li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </li>
            </ul>

            <!-- Search Form -->
            <form class="form-inline ml-0 ml-md-3" action="index.php">
                <div class="input-group input-group-sm">
                    <?php
                    if (isset($_GET['category'])) {
                        $catId = $_GET['category'];
                        echo '<input type="hidden" name="category" value="'.$catId.'"/>';
                    }
                    ?>
                    <!-- Search input -->
                    <input class="form-control form-control-navbar" name="search" type="search" value="<?=(isset($_GET['search']))?$_GET['search']:''?>" placeholder="Search" aria-label="Search">
                    <div class="input-group-append">
                        <!-- Search Button -->
                        <button class="btn btn-navbar" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">

            <!-- Fullscreen -->
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>

            <?php
            if (isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true) {
                $query = "SELECT profile_image from users WHERE id = {$_SESSION['userId']}";
                $result = $conn -> query ($query);
                $user = $result -> fetch_assoc();
                ?>
                <!-- My Profile Button -->
                <li class="nav-item">
                    <a class="nav-link" href="user.php?id=<?=$_SESSION['userId']?>" role="button">
                        <img src="<?=getAssetsPath()?>profile/<?=$user['profile_image']?>" class="profile-avatar mh-100 mw-100 img-circle" alt="!!">
                    </a>
                </li>

                <!-- Logout Button -->
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" role="button">
                        <i class="fas fa-power-off"></i>
                    </a>
                </li>
                <?php
            } else {
                ?>
                <!-- Login Button -->
                <li class="nav-item">
                    <a class="nav-link" href="login.php" role="button">
                        <i class="fas fa-sign-in-alt"></i>
                    </a>
                </li>

                <!-- Register Button -->
                <li class="nav-item">
                    <a class="nav-link" href="register.php" role="button">
                        <i class="fas fa-user-plus"></i>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
</nav>