<?php
session_start();
require 'db_connect.php';

// admin access only
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../loginup/index.php");
    exit;
}

// Get mechanic email from query string
if (!isset($_GET['mechanic_email'])) {
    die("Mechanic email not provided.");
}
$mechanic_email = $_GET['mechanic_email'];

// Fetch mechanic details
$stmt = $conn->prepare("SELECT email, full_name, phone_number, garage_name, experience FROM mechanics WHERE email = ?");
$stmt->bind_param("s", $mechanic_email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Mechanic not found.");
}
$mechanic = $result->fetch_assoc();
$stmt->close();

$error = "";
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $phone_number = trim($_POST['phone_number']);
    $garage_name = trim($_POST['garage_name']);
    $experience = intval($_POST['experience']);
    
    if (empty($full_name) || empty($phone_number) || empty($garage_name)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE mechanics SET full_name = ?, phone_number = ?, garage_name = ?, experience = ? WHERE email = ?");
        $stmt->bind_param("sssis", $full_name, $phone_number, $garage_name, $experience, $mechanic_email);
        if ($stmt->execute()) {
            // header("location:admin_mechanics.php")
            $success = "Mechanic details updated successfully.";
        } else {
            $error = "Error updating details: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Update Mechanic</title>
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
    body { background: #34495e;}
    .navbar-custom {
      background-color: #191919;
      padding: 10px 20px;
    }
    .navbar-custom .navbar-brand { color: #2ecc71; font-weight: bold; font-size: 24px; }
    .navbar-custom .nav-link { color: white !important; }
    .profile-pic {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      margin-right: 5px;
    }
    .container {
      margin-top: 30px;
      max-width: 500px;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; margin-bottom: 20px; color: #191919; }
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
    <h2>Update Mechanic Details</h2>
    <p class="text-center">For <?php echo htmlspecialchars($mechanic['full_name']); ?> (<?php echo htmlspecialchars($mechanic['email']); ?>)</p>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form action="admin_update_mechanic.php?mechanic_email=<?php echo urlencode($mechanic_email); ?>" method="post">
      <div class="mb-3">
        <label for="full_name" class="form-label">Full Name</label>
        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($mechanic['full_name']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="phone_number" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($mechanic['phone_number']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="garage_name" class="form-label">Garage Name</label>
        <input type="text" class="form-control" id="garage_name" name="garage_name" value="<?php echo htmlspecialchars($mechanic['garage_name']); ?>" required>
      </div>
      <div class="mb-3">
        <label for="experience" class="form-label">Years of Experience</label>
        <input type="number" class="form-control" id="experience" name="experience" value="<?php echo htmlspecialchars($mechanic['experience']); ?>" required>
      </div>
      <button type="submit" class="btn btn-primary">Update Details</button>
      <a href="admin_mechanics.php" class="btn btn-secondary">Back to Mechanics</a>
    </form>
  </div>
  <!-- Bootstrap Bundle with Popper -->
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
