<?php
session_start();
require 'db_connect.php';

// Ensure the user is logged in as a regular user.
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Booking ID not provided.");
}

$booking_id = intval($_GET['id']);
$error = "";
$success = "";

// Process reschedule form submission.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_date = $_POST['booking_date'];
    $new_time = $_POST['booking_time'];
    if (strlen($new_time) == 5) {
        $new_time .= ":00";
    }
    if (empty($new_date) || empty($new_time)) {
        $error = "Both date and time are required.";
    } else {
        $stmt = $conn->prepare("UPDATE bookings SET booking_date = ?, booking_time = ? WHERE id = ? AND user_email = ?");
        $user_email = $_SESSION['email'];
        $stmt->bind_param("ssis", $new_date, $new_time, $booking_id, $user_email);
        if ($stmt->execute()) {
            $success = "Booking rescheduled successfully!";
            header("location:user_dashboard.php");
        } else {
            $error = "Error rescheduling booking: " . $stmt->error;
        }
    }
}

// Fetch current booking details.
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_email = ?");
$user_email = $_SESSION['email'];
$stmt->bind_param("is", $booking_id, $user_email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    die("Booking not found.");
}
$booking = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reschedule Booking</title>
    <link rel="stylesheet" href="style.css">
    <style>
    
      body { font-family: sans-serif; background: #34495e; color: #fff; }
      .container { width: 400px; margin: 50px auto; background: #191919; padding: 20px; border-radius: 8px; }
      input { width: 100%; padding: 10px; margin: 10px 0; }
      input[type="submit"] { background: #2ecc71; border: none; cursor: pointer; }
      a { color: #2ecc71; text-decoration: none; }
    </style>
</head>
<body>

<div id="loader">
    <div class="spinner"></div>
   
  </div>

  <!-- Main content -->
  <div id="main-content">
    <div class="container">
        <h1>Reschedule Booking id <?php echo $booking['id']; ?></h1>
        <?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>
        <?php if ($success) { echo "<p style='color: #2ecc71;'>$success</p>"; } ?>
        <form method="post" action="">
            <label for="booking_date">New Date:</label>
            <input type="date" name="booking_date" id="booking_date" value="<?php echo htmlspecialchars($booking['booking_date']); ?>" required>
            <label for="booking_time">New Time:</label>
            <!-- Show HH:MM format -->
            <input type="time" name="booking_time" id="booking_time" value="<?php echo substr(htmlspecialchars($booking['booking_time']), 0, 5); ?>" required>
            <input type="submit" value="Reschedule Booking">
        </form>
        <p><a href="user_dashboard.php">Back to Dashboard</a></p>
    </div>
    <script> 
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
