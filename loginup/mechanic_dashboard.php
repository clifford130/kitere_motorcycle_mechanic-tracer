<?php
session_start();
require 'db_connect.php';

// Ensure the mechanic is logged in.
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'mechanic') {
    session_destroy();
    header("Location: index.php");
    exit();
}

$mechanic_email = $_SESSION['email'];

// Fetch bookings for this mechanic.
$queryBookings = "SELECT * FROM bookings WHERE mechanic_email = ? ORDER BY booking_date, booking_time";
$stmt = $conn->prepare($queryBookings);
$stmt->bind_param("s", $mechanic_email);
$stmt->execute();
$resultBookings = $stmt->get_result();

// Fetch reviews (ratings) for this mechanic.
$queryReviews = "SELECT * FROM reviews WHERE mechanic_email = ? ORDER BY created_at DESC";
$stmtReviews = $conn->prepare($queryReviews);
$stmtReviews->bind_param("s", $mechanic_email);
$stmtReviews->execute();
$resultReviews = $stmtReviews->get_result();

// Calculate average rating and total reviews.
$queryAvg = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE mechanic_email = ?";
$stmtAvg = $conn->prepare($queryAvg);
$stmtAvg->bind_param("s", $mechanic_email);
$stmtAvg->execute();
$resultAvg = $stmtAvg->get_result();
$ratingData = $resultAvg->fetch_assoc();
$avg_rating = round($ratingData['avg_rating'], 1);
$total_reviews = $ratingData['total_reviews'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mechanic Dashboard - Kitere Mechanic System</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #34495e; }
    .navbar-custom { background: #191919; }
    .navbar-custom .navbar-brand, 
    .navbar-custom .nav-link { color: #2ecc71; }
    .navbar-custom .nav-link:hover { color: #fff; }
    .section-title { margin-top: 30px; }

    .profile-pic {
    width: 20px;
    height: 20px;
    border-radius: 50%; 
    overflow: hidden;
    border: 2px solid #4CAF50; 
    transition: transform 0.3s ease; 
    cursor: pointer; 
}

/* Hover effect */
.profile-pic:hover {
    transform: scale(1.05); /* Slight zoom-in effect */
    box-shadow: 0 0 10px rgba(0, 0, 0);
}
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
      <a class="navbar-brand" href="../index.html">KITERE MOTORCYCLE MECHANIC TRACER</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <!-- <li class="nav-item"><a class="nav-link" href="mechanic_dashboard.php">Dashboard</a></li> -->
          <!-- <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li> -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown">
              <img src="../img/profile.jpeg" alt=""class="profile-pic">
              <span style="color: white;">Profile</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
              <li><?php echo htmlspecialchars($mechanic_email); ?></li>
              <!-- <li><a class="dropdown-item" href="mechanic_dashboard.php">Dashboard</a></li> -->
              <!-- <li><a class="dropdown-item" href="profile.php">Profile</a></li> -->
              <li><a class="dropdown-item" href="logout.php"style="color:green;">Logout</a></li>
             
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container my-4">
    <!-- Bookings Section -->
    <h2 class="section-title text-light">Incoming Bookings</h2>
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>User Email</th>
            <th>Date</th>
            <th>Time</th>
            <th>Description</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $resultBookings->fetch_assoc()) { ?>
          <tr>
            <td><?php echo htmlspecialchars($row['user_email']); ?></td>
            <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
            <td><?php echo htmlspecialchars($row['booking_time']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['status'] ?? 'pending'); ?></td>
            <td>
              <a href="mark_complete.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Mark Completed</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- Ratings/Reviews Section -->
    <h2 class="section-title text-light">Your Reviews</h2>
    <div class="mb-3">
      <p class="text-light">
        <strong>Average Rating:</strong> <?php echo $avg_rating; ?> / 5 &nbsp;|&nbsp;
        <strong>Total Reviews:</strong> <?php echo $total_reviews; ?>
      </p>
    </div>
    <div class="row">
      <?php while ($review = $resultReviews->fetch_assoc()) { ?>
      <div class="col-md-6">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">From: <?php echo htmlspecialchars($review['user_email']); ?></h5>
            <p class="card-text">Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</p>
            <p class="card-text"><?php echo htmlspecialchars($review['review_message']); ?></p>
            <p class="card-text"><small class="text-muted">On <?php echo htmlspecialchars($review['created_at']); ?></small></p>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
