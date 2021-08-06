<?php
require ('includes/helper.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Kite | Login</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
        html, body {
            height: fit-content;
            min-height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            padding-top: 50px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        a {
            text-decoration: none;
        }

        .passEye {
            cursor: pointer;
        }
    </style>
</head>
<body class="hold-transition login-page">
<?php
require ('includes/dbconfig.php');
if(isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true)
    header('location: index.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    require ('includes/login-process.php');
}
?>
<div class="login-box">
    <div class="login-logo">
        <a href="index.php"><img src="<?=getAssetsPath()?>brand-logo.png" height="50" width="50"></a> Login
    </div>
    <form class="g-3" action="login.php" method="post" enctype="multipart/form-data">

        <div class="row mb-3">
            <label for="email" class="form-label">Email Id</label>
            <div class="input-group has-validation">
                <!-- Email Id Input -->
                <input
                        type="text"
                        class="form-control <?=(isset($error['email']) && $error['email'] !== '')?'is-invalid':''?>"
                        id="email"
                        name="email"
                        value="<?php if(isset($_POST['email'])) echo $_POST['email'];?>"
                        aria-describedby="emailError"
                        required
                />
                <!-- Email id Error -->
                <div id="emailError" class="invalid-feedback">
                    <?php if (isset($error['email'])) echo $error['email'];?>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group has-validation">
                <!-- Password Input -->
                <input
                        type="password"
                        class="form-control <?=(isset($error['password']) && $error['password'] !== '')?'is-invalid':''?>"
                        id="password"
                        name="password"
                        aria-describedby="pwd-eye passwordError"
                        required
                />
                <!-- Eye icon to toggle between text and password view-->
                <span class="passEye input-group-text">
                    <i class="fas fa-eye-slash"></i>
                </span>
                <!-- Email id Error -->
                <div id="passwordError" class="invalid-feedback">
                    <?php if (isset($error['password'])) echo $error['password'];?>
                </div>
            </div>
        </div>
        <!-- Login button -->
        <div class="row mb-3 justify-content-center">
            <button class="col-5 btn btn-primary" id="submitBtn" type="submit">Login</button>
        </div>
        <!-- Not a member - redirect to Registration page -->
        <p class="mb-0 text-center">
            Not yet registered? <a href="register.php">Register</a>
        </p>
    </form>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script src="dist/js/helper.js"></script>
<script src="dist/js/pages/login.js"></script>
</body>
</html>