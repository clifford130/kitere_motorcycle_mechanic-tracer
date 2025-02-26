<?php
session_start();
require 'db_connect.php';

// Only allow admin access
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginup/index.php");
    exit;
}

// Get mechanic email from query string
if (!isset($_GET['mechanic_email'])) {
    die("Mechanic email not provided.");
}
$mechanic_email = $_GET['mechanic_email'];

// Delete the mechanic using a prepared statement
$stmt = $conn->prepare("DELETE FROM mechanics WHERE email = ?");
$stmt->bind_param("s", $mechanic_email);
if ($stmt->execute()) {
    header("Location: admin_mechanics.php?msg=Mechanic+deleted");
    exit;
} else {
    die("Error deleting mechanic: " . $stmt->error);
}
$stmt->close();
$conn->close();
?>
