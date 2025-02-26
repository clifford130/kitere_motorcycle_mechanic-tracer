<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        die("Email is required!");
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? UNION SELECT email FROM mechanics WHERE email = ?");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Email not found!");
    }

    // Generate password reset token
    $token = bin2hex(random_bytes(50)); // Generate a secure token
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?) ON DUPLICATE KEY UPDATE token = ?");
    $stmt->bind_param("sss", $email, $token, $token);
    $stmt->execute();

    // Send email (simulation)
    $reset_link = "http://localhost/reset_confirm.php?token=" . $token;
    echo "A password reset link has been sent: <a href='$reset_link'>$reset_link</a>";

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div id="loader">
    <div class="spinner"></div>
  </div>

  <!-- Main Content -->
  <div id="main-content">
    <form class="box form-animate" id="resetForm" action="reset_password.php" method="post">
        <h1 class="welcome">Reset Password</h1>
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="submit" name="reset" value="Send Reset Link">
        
        <p><a href="./index.php">Back to Login</a></p>
    </form>
    <script>window.onload = function() {
      var loader = document.getElementById('loader');
      // Fade out the loader
      loader.style.opacity = 0;
      // After the transition completes (1s), hide the loader and show main content
      setTimeout(function() {
        loader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
      }, 1000); // Duration matches the CSS transition time
    };</script>
</body>
</html>
