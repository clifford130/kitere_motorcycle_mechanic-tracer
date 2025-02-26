<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginup/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
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
  
  /* Initially hide main content */
  #main-content {
    display: none;
  }
    body { background: #34495e;
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
        width: 35px; height: 35px; border-radius: 50%; 
        margin-right: 5px; 
    }
    .container { margin-top: 30px; 
        background: #968d8d; 
        padding: 20px; 
        border-radius: 10px; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    h2 { text-align: center; 
        margin-bottom: 20px; 
        color:rgb(27, 27, 27); 
    }
    .card { 
        margin: 15px 0; 
        text-align: center; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
    }
    .card-title { font-size: 20px; 
        argin-bottom: 10px; 
        color: #191919; 
    }
    .card-text { font-size: 16px; 
        margin-bottom: 15px; 
        color: #555; 
    }
    .btn-primary { background-color: #2ecc71; 
        border: none; 
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
      <a class="navbar-brand" href="admin_dashboard.php"> Admin</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="adminNavbar">
        <ul class="navbar-nav ms-auto">
          <!-- <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_users.php">Manage Users</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_ratings.php">Manage Ratings</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_bookings.php">Manage Bookings</a></li>
          <li class="nav-item"><a class="nav-link" href="admin_mechanics.php">Manage Mechanics</a></li> -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
              <img src="../img/profile.jpeg" alt="Profile Picture" class="profile-pic">
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
    <h2>Admin Dashboard</h2>
    <div class="row">
      <!-- Manage Users Card -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Manage Users</h4>
            <p class="card-text">View, update, or delete users.</p>
            <a href="admin_users.php" class="btn btn-primary">Manage Users</a>
          </div>
        </div>
      </div>
      <!-- Manage Ratings Card -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Manage Ratings</h4>
            <p class="card-text">Oversee reviews and ratings.</p>
            <a href="admin_ratings.php" class="btn btn-primary">Manage Ratings</a>
          </div>
        </div>
      </div>
      <!-- Manage Bookings Card -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Manage Bookings</h4>
            <p class="card-text">Review and manage service bookings.</p>
            <a href="admin_bookings.php" class="btn btn-primary">Manage Bookings</a>
          </div>
        </div>
      </div>
      <!-- Manage Mechanics Card -->
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Manage Mechanics</h4>
            <p class="card-text">Update or remove mechanic details.</p>
            <a href="admin_mechanics.php" class="btn btn-primary">Manage Mechanics</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
