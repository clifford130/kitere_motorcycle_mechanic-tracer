<?php 
session_start();
require 'db_connect.php';

// Checking if user logged in is  admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
  session_destroy();
  header("Location: index.php");
  exit();
}

$admin_email = $_SESSION['email']; 
$query = "SELECT * FROM bookings ORDER BY booking_date DESC, booking_time DESC";
$result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Admin Dashboard - All Bookings</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
      body { font-family: sans-serif; background: #34495e; color: #fff; }
      .container { width: 90%; margin: 20px auto; }
      table { width: 100%; border-collapse: collapse; }
      th, td { border: 1px solid #2ecc71; padding: 10px; text-align: center; }
      th { background: #191919; }
      a { color: #2ecc71; text-decoration: none; }

      .profile-pic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    overflow: hidden;
    
    transition: transform 0.3s ease; 
    cursor: pointer; 
}

/* Hover effect */
.profile-pic:hover {
    transform: scale(1.05);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); 
}
       /* Navigation Bar Styles */
    nav {
      background: #191919;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    nav .logo {
      color: #2ecc71;
      font-size: 24px;
      font-weight: bold;
      text-decoration: none;
    }
    nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
    }
    nav ul li {
      margin-left: 20px;
      position: relative;
    }
    nav ul li a {
      color: #fff;
      text-decoration: none;
      font-size: 16px;
    }
    nav ul li a:hover {
      color: #2ecc71;
    }
    /* Dropdown Styles */
    nav ul li .dropdown {
      display: none;
      position: absolute;
      top: 30px;
      right: 0;
      background: #191919;
      border: 1px solid #2ecc71;
      border-radius: 4px;
      min-width: 120px;
      z-index: 1000;
    }
    nav ul li:hover .dropdown {
      display: block;
    }
    nav ul li .dropdown a {
      display: block;
      padding: 10px;
      color: #fff;
      text-decoration: none;
    }
    nav ul li .dropdown a:hover {
      background: #2ecc71;
    }
    .container {
      width: 90%;
      margin: 20px auto;
      background: #34495e;
      padding: 20px;
      border-radius: 8px;
      color: #fff;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td {
      border: 1px solid #2ecc71;
    }
    th, td {
      padding: 10px;
      text-align: center;
    }
    th {
      background: #191919;
    }
    a { color: #2ecc71; text-decoration: none; }
   
    nav ul li a:hover {
  color: #2ecc71 !important;
  
}
.navbar-custom .nav-link:hover {
  color: #2ecc71 !important;}
   </style>

</head>
<body>
<div id="loader">
    <div class="spinner"></div>
   
  </div>

  <!-- Main content -->
  <div id="main-content">
      
  <!-- Navigation Bar -->
  <nav>
    <a href="#" class="logo">Admin</a>
    <ul>
       
          <li class="nav-item"><a class="nav-link" href="admin_ratings.php">Manage Ratings</a></li>
          <!-- <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Manage Bookings</a></li> -->
          <li class="nav-item"><a class="nav-link" href="admin_users.php">Manage Users</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_mechanics.php">Manage Mechanics</a></li>

       
       
    </ul>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
     
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <ul class="navbar-nav">

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown">
              <img src="../img/profile.jpeg" alt=""class="profile-pic" style="color:white;">
              <span style="color: white;">Profile</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
              <li><?php echo htmlspecialchars($admin_email); ?></li>
              
              <li><a class="dropdown-item" href="logout.php"style="color:green;">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</nav>

  <div class="container">
    <h1>All Bookings</h1>
    <table>
      <tr>
         <th>User Email</th>
         <th>Mechanic Email</th>
         <th>Date</th>
         <th>Time</th>
         <th>Description</th>
         <th>Status</th>
         <th>Actions</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()) { ?>
      <tr>
         <td><?php echo htmlspecialchars($row['user_email']); ?></td>
         <td><?php echo htmlspecialchars($row['mechanic_email']); ?></td>
         <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
         <td><?php echo htmlspecialchars($row['booking_time']); ?></td>
         <td><?php echo htmlspecialchars($row['description']); ?></td>
         <td><?php echo htmlspecialchars($row['status'] ?? 'pending'); ?></td>
         <td>
            <!-- action to update status  -->
            <a href="update_status.php?id=<?php echo $row['id']; ?>">Update Status</a>
         </td>
      </tr>
      <?php } ?>
      
    </table>
    <div style="max-width: 200px; margin: 30px auto; text-align: center;">
  <a href="admin_dashboard.php" class="btn btn-secondary" style="display: block;">Back to Dashboard</a>
</div>


  </div>
      
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
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
