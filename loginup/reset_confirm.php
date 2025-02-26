<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    if (empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
        die("All fields are required!");
    }

    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        die("Passwords do not match!");
    }

    // Get email from token
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $email = $row['email'] ?? null;

    if ($email) {
        // Update password in users table
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);
        $stmt->execute();

        // Update password in mechanics table
        $stmt = $conn->prepare("UPDATE mechanics SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);
        $stmt->execute();

        echo "Password updated successfully!";
    } else {
        die("Invalid or expired token.");
    }
} else {
    // Display form for password reset
    $token = $_GET['token'] ?? '';
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
        <form class="box form-animate" id="confirmForm" action="reset_confirm.php" method="post">
            <h1 class="welcome">Enter New Password</h1>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="submit" name="reset_confirm" value="Update Password">
        </form>
        <script>window.onload = function() {
      var loader = document.getElementById('loader');
      // Fade out the loader
      loader.style.opacity = 0;
      // 
      setTimeout(function() {
        loader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
      }, 1000); // Duration matches the CSS transition time
    };</script>
    </body>
    </html>
    <?php
}
?>
