<?php
session_start();
require 'db_connect.php';

// Only allow admin access
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginup/index.php");
    exit;
}

// Get user email from query string
if (!isset($_GET['user_email'])) {
    die("User email not provided.");
}
$user_email = $_GET['user_email'];

// Delete the user using a prepared statement
$stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
if ($stmt->execute()) {
    header("Location: admin_users.php?msg=User+deleted");
    exit;
} else {
    die("Error deleting user: " . $stmt->error);
}
$stmt->close();
$conn->close();
?>
