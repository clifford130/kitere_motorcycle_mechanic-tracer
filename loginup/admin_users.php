<?php
session_start();
require 'db_connect.php';

// Only allow admin access
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginup/index.php");
    exit;
}

// Retrieve all users (using email as primary key)
$query = "SELECT email, full_name FROM users";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Users</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
      color: #191919;
      margin-bottom: 20px;
      text-align: center;
    }
    table {
      margin-top: 20px;
    }
    .btn {
      margin-right: 5px;
    }
    .navbar-custom .nav-link:hover {
  color: #2ecc71 !important;
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
      <a class="navbar-brand" href="#">Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="adminNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">

          <li class="nav-item"><a class="nav-link" href="admin_ratings.php">Manage Ratings</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_bookings.php">Manage Bookings</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_users.php">Manage Users</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_mechanics.php">Manage Mechanics</a></li>

          </li>
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
    <h2>Manage Users</h2>
    <table class="table table-striped table-bordered">
      <thead class="table-dark">
        <tr>
          <th>Email</th>
          <th>Full Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['email']); ?></td>
          <td><?php echo htmlspecialchars($row['full_name']); ?></td>
          <td>
            <a href="admin_update_password.php?user_email=<?php echo urlencode($row['email']); ?>" class="btn btn-warning btn-sm">Update Password</a>
            <a href="admin_delete_user.php?user_email=<?php echo urlencode($row['email']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
  </div>

  <!-- Bootstrap  -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
