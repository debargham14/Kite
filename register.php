<?php
require ('includes/helper.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Kite | Register</title>
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
<body class="hold-transition register-page">
<?php
require ('includes/dbconfig.php');
if(isset($_SESSION['userLoggedIn']) && $_SESSION['userLoggedIn'] === true)
    header('location: index.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    require ('includes/register-process.php');
}
?>
<div class="register-box">
    <div class="register-logo">
        <a href="index.php"><img src="<?=getAssetsPath()?>brand-logo.png" height="50" width="50"></a> Register
    </div>
    <?php if (isset($error["special"])) echo '<h5 id="specialError">'.$error["special"].'</h5>'?>
    <form class="g-3" action="register.php" method="post" enctype="multipart/form-data">

        <div class="row mb-3">
            <label for="fullName" class="form-label">Full Name</label>
            <div class="input-group has-validation">
                <!-- Fullname Input -->
                <input
                        type="text"
                        class="form-control <?=(isset($error['fullName']) && $error['fullName'] !== '')?'is-invalid':''?>"
                        id="fullName"
                        name="fullName"
                        value="<?php if(isset($_POST['fullName'])) echo $_POST['fullName'];?>"
                        aria-describedby="fullNameError"
                        required
                />
                <!-- FullName Error -->
                <div id="fullNameError" class="invalid-feedback">
                    <?php if (isset($error['fullName'])) echo $error['fullName'];?>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="email" class="form-label">Email Id</label>
            <div class="input-group has-validation">
                <!-- Email id Input -->
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

        <!-- Password Input -->
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
                <!-- Password Error -->
                <div id="passwordError" class="invalid-feedback">
                    <?php if (isset($error['password'])) echo $error['password'];?>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <div class="input-group has-validation">
                <!-- Confirm Password Input -->
                <input
                        type="password"
                        class="form-control <?=(isset($error['confirmPassword']) && $error['confirmPassword'] !== '')?'is-invalid':''?>"
                        id="confirmPassword"
                        name="confirmPassword"
                        aria-describedby="confirmPasswordError"
                        required
                />
                <!-- Eye icon to toggle between text and password view-->
                <span class="passEye input-group-text">
                    <i class="fas fa-eye-slash"></i>
                </span>

                <!-- Confirm Password Error -->
                <div id="confirmPasswordError" class="invalid-feedback">
                    <?php if (isset($error['confirmPassword'])) echo $error['confirmPassword'];?>
                </div>
            </div>
        </div>

        <!-- T&C CheckBox -->
        <div class="row mb-3">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="terms" class="custom-control-input" id="tnc">
                <label class="custom-control-label" for="tnc">I agree to the <a href="#">terms of service</a>.</label>
                <div id="tncError" class="invalid-feedback">You must agree</div>
            </div>
        </div>

        <!-- Register button -->
        <div class="row mb-3 justify-content-center">
            <button class="col-5 btn btn-primary" id="submitBtn" type="submit">Register</button>
        </div>
        <!-- Already a member - redirect to Login page -->
        <p class="mb-0 text-center">
            Already a member? <a href="login.php">Login</a>
        </p>
    </form>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/helper.js"></script>
<script src="dist/js/pages/register.js"></script>
</body>
</html>