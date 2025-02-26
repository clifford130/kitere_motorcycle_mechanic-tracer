<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mechanic_email = trim($_POST['mechanic_email']);
    $rating = intval($_POST['rating']);
    $review_message = trim($_POST['review_message']);
    
    // Using the logged-in user's email from the session
    $user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'anonymous@example.com';
    
    if (empty($mechanic_email) || empty($rating)) {
        die("Mechanic email and rating are required.");
    }
    
    // Prevent duplicate review submissions
    $check_stmt = $conn->prepare("SELECT review_id FROM reviews WHERE mechanic_email = ? AND user_email = ?");
    $check_stmt->bind_param("ss", $mechanic_email, $user_email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        header("location:user_dashboard.php");
        die("You have already submitted a review for this mechanic.");
        
    }
    $check_stmt->close();
    
    // Insert the review into the reviews table
    $stmt = $conn->prepare("INSERT INTO reviews (mechanic_email, user_email, rating, review_message) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssis", $mechanic_email, $user_email, $rating, $review_message);
    
    if ($stmt->execute()) {
        header("location:user_dashboard.php");
        echo "Thank you for your review!";
       
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
