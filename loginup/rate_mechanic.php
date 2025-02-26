<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}
$mechanic_email = isset($_GET['mechanic_email']) ? $_GET['mechanic_email'] : '';
if(empty($mechanic_email)){
    die("Mechanic email not provided.");
}

// Verify that the mechanic exists
$stmt = $conn->prepare("SELECT email FROM mechanics WHERE email = ?");
$stmt->bind_param("s", $mechanic_email);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows == 0){
    die("Mechanic not found.");
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Rate Mechanic</title>
  <!-- Include Font Awesome for star icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
  <style>
    body {
    margin: 0;
    padding: 0;
    font-family: sans-serif;
    background: #34495e;
}
    .review-container {
      max-width: 600px;
      margin: 50px auto;
      padding: 20px;
      background: #7a7979;
      border: 1px solid #ccc;
      border-radius: 10px;
      text-align: center;
      color: #333;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .star-rating {
      font-size: 2rem;
      direction: rtl;
      display: inline-block;
    }
    .star-rating input[type="radio"] {
      display: none;
    }
    .star-rating label {
      color: #ccc;
      cursor: pointer;
      margin: 0 5px;
    }
    .star-rating input[type="radio"]:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
      color: #f7d106;
    }
    textarea {
      width: 100%;
      padding: 10px;
      margin-top: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
      resize: vertical;
    }
    button {
      margin-top: 15px;
      padding: 10px 20px;
      background-color: #2ecc71;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #27ae60;
    }
  </style>
</head>
<body>
<div id="loader">
    <div class="spinner"></div>
   
  </div>

  <!-- Main content -->
  <div id="main-content">
  <div class="review-container">
    <h2>Rate This Mechanic</h2>
    <form action="submit_rating.php" method="post">
      <!-- Hidden input for mechanic's email -->
      <input type="hidden" name="mechanic_email" value="<?php echo htmlspecialchars($mechanic_email); ?>">
      
      <div class="star-rating">
        <input type="radio" id="star5" name="rating" value="5" required>
        <label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
        <input type="radio" id="star4" name="rating" value="4">
        <label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
        <input type="radio" id="star3" name="rating" value="3">
        <label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
        <input type="radio" id="star2" name="rating" value="2">
        <label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
        <input type="radio" id="star1" name="rating" value="1">
        <label for="star1" title="1 star"><i class="fas fa-star"></i></label>
      </div>
      
      <div>
        <textarea name="review_message" rows="4" placeholder="Leave an optional comment..."></textarea>
      </div>
      
      <button type="submit">Submit Rating</button>
    </form>
  </div>
  <script>window.onload = function() {
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
