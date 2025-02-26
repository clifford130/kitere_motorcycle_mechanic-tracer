<?php
session_start();
require 'db_connect.php';

// Ensure only admin users can delete reviews.
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginup/index.php");
    exit();
}

if (isset($_GET['review_id'])) {
    $review_id = intval($_GET['review_id']);
    
    // Prepare and execute deletion.
    $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
    $stmt->bind_param("i", $review_id);
    if ($stmt->execute()) {
        header("Location: admin_ratings.php?msg=Review+deleted");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error deleting review: " . htmlspecialchars($stmt->error) . "</div>";
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-warning'>Invalid request. No review ID provided.</div>";
}

$conn->close();
?>
