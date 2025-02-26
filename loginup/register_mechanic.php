<?php  
session_start();
require '../loginup/db_connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $garage_name = trim($_POST['garage_name']);
    $experience = intval($_POST['experience']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $services = isset($_POST['services']) ? implode(", ", $_POST['services']) : '';
    $password_raw = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($full_name) || empty($email) || empty($phone) || empty($garage_name) || empty($latitude) || empty($longitude) || empty($password_raw)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password_raw) < 8) {
        $error = "Password must be at least 8 characters long!";
    } elseif ($password_raw !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists in mechanics table
        $check_stmt = $conn->prepare("SELECT email FROM mechanics WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Email already registered! please login!";
        } else {
            // Hash the password
            $password = password_hash($password_raw, PASSWORD_BCRYPT);

            // Insert into mechanics table
            $stmt = $conn->prepare("INSERT INTO mechanics (email, full_name, phone_number, garage_name, experience, latitude, longitude, services_offered, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssddsss", $email, $full_name, $phone, $garage_name, $experience, $latitude, $longitude, $services, $password);

            if ($stmt->execute()) {
                $success = "Mechanic registration successful!";
                header("Location: mechanic_dashboard.php");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Register as Mechanic</title>
  <link rel="stylesheet" href="style2.css">
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
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
      transition: opacity 1s ease;
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
    /* Style for services grid */
    .services-list {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 10px;
      margin-top: 10px;
      align-items: center;
    }
    .services-list label {
      display: flex;
      align-items: center;
      font-size: 14px;
      white-space: nowrap;
    }
    .services-list input[type="checkbox"] {
      margin-right: 5px;
      width: 16px;
      height: 16px;
      flex-shrink: 0;
    }
    /* Message styling for error/success */
    .message {
      font-size: 14px;
      margin-bottom: 10px;
      text-align: center;
    }
    .error { color: red; }
    .success { color: green; }
    /* Map container style */
    #map {
      height: 200px;
      width: 100%;
      margin-top: 10px;
    }
  </style>
  <script>
    // Get user's location and update map and hidden form fields
    function getLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function(position) {
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lon;
            document.getElementById('locationMessage').innerHTML = "";
            if (window.marker) {
              window.map.removeLayer(window.marker);
            }
            window.marker = L.marker([lat, lon]).addTo(window.map)
              .bindPopup("Your Location").openPopup();
            window.map.setView([lat, lon], 13);
          },
          function(error) {
            if (error.code === error.PERMISSION_DENIED) {
              document.getElementById('locationMessage').innerHTML = 
                "<p class='message error'>Location access is blocked. Please click the button below to allow location access.</p>" +
                "<button type='button' onclick='getLocation()'>Allow Location Access</button>";
            } else {
              document.getElementById('locationMessage').innerHTML = 
                "<p class='message error'>Error obtaining location. Please try again.</p>" +
                "<button type='button' onclick='getLocation()'>Retry</button>";
            }
          }
        );
      } else {
        alert("Geolocation is not supported by your browser.");
      }
    }

    // Combined onload event: fade out loader, show main content, and initialize the map.
    window.addEventListener('load', function() {
      var loader = document.getElementById('loader');
      loader.style.opacity = 0;
      setTimeout(function() {
        loader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
        // Initializing the map with a default view
        window.map = L.map('map').setView([-1.2921, 36.8219], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(window.map);
        // Retrieve and set user location
        getLocation();
      }, 1000);
    });

    // Full form validation before submission
    function validateMechanicForm() {
      var fullName = document.getElementById("full_name").value.trim();
      var email = document.getElementById("email").value.trim();
      var phone = document.getElementById("phone").value.trim();
      var garageName = document.getElementById("garage_name").value.trim();
      var experience = document.getElementById("experience").value.trim();
      var password = document.getElementById("password").value;
      var confirmPassword = document.getElementById("confirm_password").value;
      var latitude = document.getElementById("latitude").value;
      var longitude = document.getElementById("longitude").value;
      var serviceCheckboxes = document.querySelectorAll("input[name='services[]']:checked");

      if (fullName === "" || email === "" || phone === "" || garageName === "" || experience === "") {
          alert("All fields are required!");
          return false;
      }

      // Email format validation
      var emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
      if (!email.match(emailPattern)) {
          alert("Invalid email format!");
          return false;
      }

      // Password validation
      if (password.length < 8) {
          alert("Password must be at least 8 characters long.");
          return false;
      }
      if (password !== confirmPassword) {
          alert("Passwords do not match!");
          return false;
      }

      // Ensure location is selected
      if (latitude === "" || longitude === "") {
          alert("Please allow location access to register.");
          return false;
      }

      // Ensure at least one service is selected
      if (serviceCheckboxes.length === 0) {
          alert("Please select at least one service offered.");
          return false;
      }

      return true;
    }
  </script>
</head>
<body>
  <div id="loader">
    <div class="spinner"></div>
  </div>

  <!-- Main content -->
  <div id="main-content">
    <form class="box form-animate" id="mechanicForm" action="register_mechanic.php" method="post" onsubmit="return validateMechanicForm();">
      <h1 class="started">Join as a Mechanic or</h1>
      <p><a href="./register_user.php">Register as Motorcyclist</a> <span style="color: white;">or</span> <a href="./index.php">Login</a></p>

      <!-- Display error or success message -->
      <?php if (!empty($error)): ?>
        <div class="message error"><?php echo $error; ?></div>
      <?php elseif (!empty($success)): ?>
        <div class="message success"><?php echo $success; ?></div>
      <?php endif; ?>

      <input type="text" id="full_name" name="full_name" placeholder="Full Name" required>
      <input type="email" id="email" name="email" placeholder="Email" required>
      <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
      <input type="text" id="garage_name" name="garage_name" placeholder="Garage Name" required>
      <input type="number" id="experience" name="experience" placeholder="Years of Experience" required>

      <!-- Password Fields -->
      <input type="password" id="password" name="password" placeholder="Password" required>
      <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>

      <!-- GPS Location Collection -->
      <label style="color: white;">Garage Location</label>
      <div id="map"></div>
      <!-- Div for location messages (e.g., re-allow location access) -->
      <div id="locationMessage"></div>
      <input type="text" id="latitude" name="latitude" placeholder="Latitude" readonly required>
      <input type="text" id="longitude" name="longitude" placeholder="Longitude" readonly required>

      <!-- Services Offered  -->
      <label style="color: white;">Services Offered</label>
      <div class="services-list">
        <label><input type="checkbox" name="services[]" value="Oil Change"> Oil Change</label>
        <label><input type="checkbox" name="services[]" value="Engine Repair"> Engine Repair</label>
        <label><input type="checkbox" name="services[]" value="Tire Replacement"> Tire Replacement</label>
        <label><input type="checkbox" name="services[]" value="Brake Adjustment"> Brake Adjustment</label>
        <label><input type="checkbox" name="services[]" value="Chain Lubrication"> Chain Lubrication</label>
        <label><input type="checkbox" name="services[]" value="Battery Replacement"> Battery Replacement</label>
      </div>

      <input type="submit" name="signup" value="Sign Up">
    </form>
  </div>
</body>
</html>
