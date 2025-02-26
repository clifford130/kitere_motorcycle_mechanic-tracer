<?php  
session_start();
require 'db_connect.php';
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    session_destroy();
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['email'];
$query = "SELECT * FROM bookings WHERE user_email = ? ORDER BY booking_date DESC, created_at asc";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>My Bookings - User Dashboard</title>
   <link rel="stylesheet" href="style.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <style>
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
      body { font-family: sans-serif; background: #34495e; color: #fff; }
      .container { width: 90%; margin: 20px auto; }
      table { width: 100%; border-collapse: collapse; }
      th, td { border: 1px solid #2ecc71; padding: 10px; text-align: center; }
      th { background: #191919; }
      a { color: #2ecc71; text-decoration: none; }

      .profile-pic {
    width: 20px;
    height: 20px;
    border-radius: 50%; 
    overflow: hidden;
    border: 2px solid #4CAF50; 
    transition: transform 0.3s ease; /
    cursor: pointer; 
}

/* Hover effect */
.profile-pic:hover {
    transform: scale(1.05); 
    box-shadow: 0 0 10px rgba(0, 0, 0);
}
   </style>
</head>
<body>
<div id="loader">
    <div class="spinner"></div>
    
  </div>

  <!-- Main content -->
  <div id="main-content">
<nav>
    <a href="../index.html" class="logo">KITERE MOTORCYCLE MECHANIC TRACER</a>
    <ul>
       <li><a href="../dashboard/main.php">Homepage</a></li>
       <li><a href="booking.php">Book mechanic</a></li>
       
       <!-- <li><a href="logout.php">Logout</a></li> -->
    </ul>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
     
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <!-- <li class="nav-item"><a class="nav-link" href="mechanic_dashboard.php">Dashboard</a></li> -->
          <!-- <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li> -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown">
              <img src="../img/profile.jpeg" alt=""class="profile-pic" style="color:white;">
              <span style="color: white;">Profile</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
              <li><?php echo htmlspecialchars($user_email); ?></li>
              <!-- <li><a class="dropdown-item" href="booking.php">Book a mechanic</a></li> -->
              <!-- <li><a class="dropdown-item" href="profile.php">Profile</a></li> -->
              <li><a class="dropdown-item" href="logout.php"style="color:green;">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</nav>
<div class="container">
    <h1>My Bookings</h1>
    <table>
      <tr>
         <th>Mechanic Email</th>
         <th>Date</th>
         <th>Time</th>
         <th>Description</th>
         <th>Status</th>
         <th>booked  date</th>
         <th>Actions</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()) { ?>
      <tr>
         <td><?php echo htmlspecialchars($row['mechanic_email']); ?></td>
         <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
         <td><?php echo htmlspecialchars($row['booking_time']); ?></td>
         <td><?php echo htmlspecialchars($row['description']); ?></td>
         <td><?php echo htmlspecialchars($row['status'] ?? 'pending'); ?></td>
         <td><?php echo htmlspecialchars($row['created_at']); ?></td>
         <td>
            <!-- Existing actions -->
            <a href="reschedule.php?id=<?php echo $row['id']; ?>">Reschedule</a> |
            <a href="cancel.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">Cancel</a> |
            <?php 
                $mech_email = $row['mechanic_email'];
                $review_stmt = $conn->prepare("SELECT review_id FROM reviews WHERE mechanic_email = ? AND user_email = ?");
                if (!$review_stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $review_stmt->bind_param("ss", $mech_email, $user_email);
                $review_stmt->execute();
                $review_result = $review_stmt->get_result();
                if ($review_result->num_rows > 0) {
                    echo "Already rated";
                } else {
                    echo "<a href='rate_mechanic.php?mechanic_email=" . urlencode($mech_email) . "'>Rate Now</a>";
                }
                $review_stmt->close();
            ?>
         </td>
      </tr>
      <?php } ?>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script> 
  window.onload = function() {
      var loader = document.getElementById('loader');
      // Fade out the loader
      loader.style.opacity = 0;
     
      setTimeout(function() {
        loader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
      }, 1000); // Duration matches the CSS transition time
    };
    </script>
</body>
</html>
