<!-- to user_dashboard  -->
<?php
session_start();
require 'db_connect.php';

//  regular user logged in.
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Booking ID not provided.");
}

$booking_id = intval($_GET['id']);
$user_email = $_SESSION['email'];

// Update the status to "cancelled" rather than deleting the record.
$stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_email = ?");
$stmt->bind_param("is", $booking_id, $user_email);
if ($stmt->execute()) {
    header("Location: user_dashboard.php?msg=cancelled");
    exit();
} else {
    die("Error cancelling booking: " . $stmt->error);
}
?>
