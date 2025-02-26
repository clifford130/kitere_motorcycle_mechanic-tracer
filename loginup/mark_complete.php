<!-- to mechanic_dashboard  -->
<?php
session_start();
require 'db_connect.php';

// Ensure the mechanic is logged in.
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'mechanic') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Booking ID not provided.");
}

$booking_id = intval($_GET['id']);
$mechanic_email = $_SESSION['email'];

// Update booking status to 'completed' for this booking.
$stmt = $conn->prepare("UPDATE bookings SET status = 'completed' WHERE id = ? AND mechanic_email = ?");
$stmt->bind_param("is", $booking_id, $mechanic_email);
if ($stmt->execute()) {
    header("Location: mechanic_dashboard.php?msg=completed");
    exit();
} else {
    die("Error marking booking as completed: " . $stmt->error);
}
?>
