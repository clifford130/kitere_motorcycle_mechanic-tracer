<?php
session_start();
require 'db_connect.php';

// Only allow admin access
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginup/index.php");
    exit;
}

$query = "SELECT email, full_name, phone_number, garage_name, experience FROM mechanics";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Mechanics</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background: #34495e;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .navbar-custom {
      background-color: #191919;
      padding: 10px 20px;
    }
    .navbar-custom .navbar-brand {
      color: #2ecc71;
      font-weight: bold;
      font-size: 24px;
    }
    .navbar-custom .nav-link {
      color: white !important;
    }
    .navbar-custom .nav-link:hover {
      color: #2ecc71 !important;
    }
    .profile-pic {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      margin-right: 5px;
    }
    .container {
      margin-top: 30px;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #191919;
    }
    table {
      width: 100%;
      margin-top: 20px;
    }
    .btn {
      margin-right: 5px;
    }
  </style>
</head>
<body>
<div id="loader">
    <div class="spinner"></div>
   
  </div>

  <!-- Main content -->
  <div id="main-content">
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
      <a class="navbar-brand" href="admin_dashboard.php">Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="adminNavbar">
        <ul class="navbar-nav ms-auto">
          <!-- <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li> -->
          <li class="nav-item"><a class="nav-link" href="admin_ratings.php">Manage Ratings</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_bookings.php">Manage Bookings</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_users.php">Manage Users</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_mechanics.php">Manage Mechanics</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
              <img src="../img/profile.jpeg" alt="Profile" class="profile-pic">
              <span>Profile</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
              <li class="dropdown-item-text"><?php echo htmlspecialchars($_SESSION['email']); ?></li>
              <li><a class="dropdown-item" href="../loginup/logout.php" style="color:green;">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Container -->
  <div class="container">
    <h2>Manage Mechanics</h2>
    <table class="table table-striped table-bordered">
      <thead class="table-dark">
        <tr>
          <th>Email</th>
          <th>Full Name</th>
          <th>Phone</th>
          <th>Garage Name</th>
          <th>Experience</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['email']); ?></td>
          <td><?php echo htmlspecialchars($row['full_name']); ?></td>
          <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
          <td><?php echo htmlspecialchars($row['garage_name']); ?></td>
          <td><?php echo htmlspecialchars($row['experience']); ?> yrs</td>
          <td>
            <a href="admin_update_mechanic.php?mechanic_email=<?php echo urlencode($row['email']); ?>" class="btn btn-warning btn-sm">Update</a>
            <a href="admin_update_mechanic_password.php?mechanic_email=<?php echo urlencode($row['email']); ?>" class="btn btn-info btn-sm">Change Password</a>
            <a href="admin_delete_mechanic.php?mechanic_email=<?php echo urlencode($row['email']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this mechanic?');">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <a href="admin_dashboard.php" class="btn btn-secondary" style="display: block; text-align: center; max-width: 200px; margin: 30px auto;">Back to Dashboard</a>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>window.onload = function() {
      var loader = document.getElementById('loader');
      // Fade out the loader
      loader.style.opacity = 0;
      // A
      setTimeout(function() {
        loader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
      }, 1000); // Duration matches the CSS transition time
    };
    </script>
</body>
</html>
