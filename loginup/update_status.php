<!-- for admins to update status -->
<?php
session_start();
require 'db_connect.php';

// Ensure admin is logged in.
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Booking ID not provided.");
}

$booking_id = intval($_GET['id']);
$error = "";
$success = "";

// Process form submission.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_status = trim($_POST['status']);
    if (empty($new_status)) {
        $error = "Please select a status.";
    } else {
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $booking_id);
        if ($stmt->execute()) {
            $success = "Status updated successfully!";
            header("location:admin_dashboard.php");
        } else {
            $error = "Error updating status: " . $stmt->error;
        }
    }
}

// Fetch current booking details.
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);
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
    <title>Update Booking Status</title>
    <style>

        /* Loader Styles */
#loader {
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    transition: opacity 1s ease; /* Smooth fade out */
  }
  .spinner {
    border: 8px solid #f3f3f3;
    border-top: 8px solid #2ecc71;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
        body { font-family: sans-serif; background: #34495e; color: #fff; }
        .container { width: 400px; margin: 50px auto; background: #191919; padding: 20px; border-radius: 8px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; }
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
        <h1>Update Booking  id <?php echo $booking['id']; ?> Status</h1>
        <?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>
        <?php if ($success) { echo "<p style='color: #2ecc71;'>$success</p>"; } ?>
        <form method="post" action="">
            <label for="status">New Status:</label>
            <select name="status" id="status" required>
                <option value="">Select status</option>
                <option value="pending" <?php if($booking['status'] == "pending") echo "selected"; ?>>Pending</option>
                <option value="confirmed" <?php if($booking['status'] == "confirmed") echo "selected"; ?>>Confirmed</option>
                <option value="completed" <?php if($booking['status'] == "completed") echo "selected"; ?>>Completed</option>
                <option value="cancelled" <?php if($booking['status'] == "cancelled") echo "selected"; ?>>Cancelled</option>
            </select>
            <input type="submit" value="Update Status">
        </form>
        <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
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
    </script></script>
</body>
</html>
