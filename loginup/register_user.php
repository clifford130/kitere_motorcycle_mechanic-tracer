<?php 
session_start();
require 'db_connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password_raw = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($full_name) || empty($email) || empty($phone) || empty($password_raw) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password_raw) < 8) {
        $error = "Password must be at least 8 characters long!";
    } elseif ($password_raw !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists in users table
        $check_stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // Hash the password
            $password = password_hash($password_raw, PASSWORD_BCRYPT);

            // Insert user into users table 
            $stmt = $conn->prepare("INSERT INTO users (email, full_name, phone_number, password, role) VALUES (?, ?, ?, ?, 'motorcyclist')");
            $stmt->bind_param("ssss", $email, $full_name, $phone, $password);

            if ($stmt->execute()) {
                $success = "Motorcyclist registration successful!";
                header("location:mechanic_dashboard.php");
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Register as Motorcycle Owner</title>
    <link rel="stylesheet" href="style.css">
    <script src="validation.js"></script>
    <style>
        .message {
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
<div id="loader">
    <div class="spinner"></div>
    
  </div>

  <!-- Main content -->
  <div id="main-content">
    <form class="box form-animate" id="userForm" action="register_user.php" method="post" onsubmit="return validateUserForm();">
        <h2 class="started">Join as Motorcycle Owner</h2>
        
        <!-- Display error or success message -->
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <input type="text" id="full_name" name="full_name" placeholder="Full Name" required>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
        
        <input type="submit" name="signup" value="Sign Up">
        <p>By creating an account, you agree to our <a href="#">Terms & Privacy</a>.</p>
        
        <p>
            <a href="./register_mechanic.php">Register as Mechanic</a> or 
            <a href="./index.php">Login</a>
        </p>
    </form>

    <script>
        function validateUserForm() {
            var fullName = document.getElementById("full_name").value.trim();
            var email = document.getElementById("email").value.trim();
            var phone = document.getElementById("phone").value.trim();
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;

            if (fullName === "" || email === "" || phone === "" || password === "" || confirmPassword === "") {
                alert("All fields are required!");
                return false;
            }

            // Email format validation
            var emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
            if (!email.match(emailPattern)) {
                alert("Invalid email format!");
                return false;
            }

            // Password validation
            if (password.length < 8) {
                alert("Password must be at least 8 characters long.");
                return false;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }

            return true;
        }

        window.onload = function() {
      var loader = document.getElementById('loader');
      // Fade out the loader
      loader.style.opacity = 0;
      // 
      setTimeout(function() {
        loader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
      }, 1000); // Duration matches the CSS transition time
    };
    </script>
</body>
</html>
