<?php
session_start();
require 'db_connect.php';

$error = ""; // Variable to store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_unset();
    session_destroy();
    session_start();
    
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']); // Check if "Remember Me" is selected

    if (empty($email) || empty($password)) {
        $error = "Email and password are required!";
    } else {
        // 1. Check in users table (regular users)
        $stmt = $conn->prepare("SELECT email, password FROM users WHERE email = ?");
        if(!$stmt){
            error_log("Prepare failed in users: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultUser = $stmt->get_result();

        if ($resultUser && $resultUser->num_rows === 1) {
            $user = $resultUser->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['email'] = $user['email'];
                $_SESSION['role']  = 'user';
                if ($remember) {
                    setcookie("user_email", $email, time() + (86400 * 30), "/");
                }
                header("Location: ../dashboard/main.php");
                exit();
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            // Not found in users table; close statement.
            $stmt->close();

            // 2. Check in mechanics table.
            $stmt = $conn->prepare("SELECT email, password FROM mechanics WHERE email = ?");
            if(!$stmt){
                error_log("Prepare failed in mechanics: " . $conn->error);
            }
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultMech = $stmt->get_result();

            if ($resultMech && $resultMech->num_rows === 1) {
                $mechanic = $resultMech->fetch_assoc();
                if (password_verify($password, $mechanic['password'])) {
                    $_SESSION['email'] = $mechanic['email'];
                    $_SESSION['role']  = 'mechanic';
                    if ($remember) {
                        setcookie("user_email", $email, time() + (86400 * 30), "/");
                    }
                    header("Location: mechanic_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid email or password!";
                }
            } else {
                // Not found in mechanics table; close statement.
                $stmt->close();

                // 3. Check in admins table.
                $stmt = $conn->prepare("SELECT email, password FROM admins WHERE email = ?");
                if(!$stmt){
                    error_log("Prepare failed in admins: " . $conn->error);
                }
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $resultAdmin = $stmt->get_result();

                if ($resultAdmin && $resultAdmin->num_rows === 1) {
                    $admin = $resultAdmin->fetch_assoc();
                    if (password_verify($password, $admin['password'])) {
                        $_SESSION['email'] = $admin['email'];
                        $_SESSION['role']  = 'admin';
                        if ($remember) {
                            setcookie("user_email", $email, time() + (86400 * 30), "/");
                        }
                        header("Location: admin_dashboard.php");
                        exit();
                    } else {
                        $error = "Invalid email or password!";
                    }
                } else {
                    $error = "Invalid email or password!";
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="lib/animate/animate.min.css" rel="stylesheet">
        <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
    <style>
    /*** Spinner Start ***/
        #spinner {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease-out, visibility 0s linear 0.5s;
            z-index: 99999;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        #spinner.show {
            transition: opacity 0.5s ease-out, visibility 0s linear 0s;
            visibility: visible;
            opacity: 1;
        }
        /*** Spinner End ***/
    </style>
    <script src="./script.js"></script>
    <script>
        // Redirect to signup page based on dropdown selection
        function redirectToSignup(selectObj) {
            var url = selectObj.value;
            if (url !== "") {
                window.location.href = url;
            }
        }
    </script>
</head>
<body>

<div id="loader">
    <div class="spinner"></div>
    <!-- Alternatively, you could just have: <p>Loading...</p> -->
  </div>

  <!-- Main content -->
  <div id="main-content">
     
    <form class="box form-animate" id="loginForm" action="index.php" method="post" onsubmit="validateForm(event, 'loginForm')">
        <h1 class="welcome">Welcome!</h1>
        
        <!-- Display error message if it exists -->
        <?php
            if (!empty($error)) {
                echo "<p style='color:red; font-size:14px; margin-bottom:10px;'>$error</p>";
            }
        ?>
        
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="checkbox" name="remember"><label>Keep me logged in</label>
        <input type="submit" name="login" value="Login">
        <h5><a href="./reset_password.php" style="text-decoration: none;">Forgot password?</a></h5>
        <p style="color: rgb(228, 23, 23);">Not registered yet? 
            <select onchange="redirectToSignup(this)">
                <option value="">Create an account as...</option>
                <option value="./register_user.php">Motorcyclist</option>
                <option value="./register_mechanic.php">Mechanic</option>
            </select>
        </p>
    </form>
    <script>
        
        
        window.onload = function() {
      var loader = document.getElementById('loader');
      // Fade out the loader
      loader.style.opacity = 0;
      // After the transition completes (1s), hide the loader and show main content
      setTimeout(function() {
        loader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
      }, 1000); // Duration matches the CSS transition time
    };
    </script>
    <script src="../js/main.js"></script>
</body>
</html>

