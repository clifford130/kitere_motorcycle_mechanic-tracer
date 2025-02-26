<?php
session_start();
require 'db_connect.php';

// Redirect if the user is not logged in.
if (!isset($_SESSION['email'])) {
    header("location: login.php");
    exit;
}

$error = "";
$success = "";

// Process booking submission.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mechanic_email = $_POST['email'];
    $booking_date   = $_POST['booking_date'];
    $booking_time   = $_POST['booking_time'];
    $description    = trim($_POST['description']);

    if (empty($mechanic_email) || empty($booking_date) || empty($booking_time)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO bookings (user_email, mechanic_email, booking_date, booking_time, description) VALUES (?, ?, ?, ?, ?)");
        $user_email = $_SESSION['email'];
        $stmt->bind_param("sssss", $user_email, $mechanic_email, $booking_date, $booking_time, $description);

        if ($stmt->execute()) {
            $success = "Booking successfully made!";
            header("Location: user_dashboard.php");
        } else {
            $error = "Error making booking: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Book a Mechanic</title>
  <style>
    /* Global Styles */
    body {
      margin: 0;
      padding: 0;
      font-family: sans-serif;
      background: #34495e;
    }
    .box {
      width: 300px;
      padding: 40px;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #191919;
      text-align: center;
      color: #fff;
      border-radius: 10px;
    }
    .box p {
      font-size: 15px;
    }
    .box a {
      color: #2ecc71;
      font-size: 15px;
    }
    .welcome {
      font-weight: 500;
    }
    .box input[type="text"],
    .box input[type="email"],
    .box input[type="password"],
    .box input[type="date"],
    .box input[type="time"],
    .box select,
    .box textarea {
      border: 2px solid #3498db;
      background:rgb(247, 150, 150);
      display: block;
      margin: 20px auto;
      text-align: center;
      padding: 14px 10px;
      width: 200px;
      outline: none;
      color:rgb(223, 231, 236);
      border-radius: 24px;
      transition: 0.25s;
    }
    .box input[type="text"]:focus,
    .box input[type="email"]:focus,
    .box input[type="password"]:focus,
    .box input[type="date"]:focus,
    .box input[type="time"]:focus,
    .box select:focus,
    .box textarea:focus {
      width: 280px;
      border-color: #2ecc71;
    }
    .box select {
      text-align: center;
      background: #191919;
      color: #fff;
    }
    .box input[type="submit"] {
      border: 2px solid #2ecc71;
      background: none;
      display: block;
      margin: 20px auto;
      text-align: center;
      padding: 14px 40px;
      width: 200px;
      outline: none;
      color: #fff;
      border-radius: 24px;
      transition: 0.25s;
      cursor: pointer;
    }
    .box input[type="submit"]:hover {
      background: #2ecc71;
    }
    /* Booking success/error message */
    .success, .error {
      color: red;
    }
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
    /* Mechanic details styling */
    #mechanic-details {
      background: rgb(16, 199, 245);
      color: #191919;
      padding: 10px;
      border-radius: 8px;
      margin-top: 10px;
      text-align: left;
      width: 280px;
      margin-left: auto;
      margin-right: auto;
      font-size: 14px;
    }
  </style>
  
  <!-- jQuery for AJAX and fadeOut -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  
  <script>
    // 
    window.onload = function(){
      var loader = document.getElementById('loader');
      loader.style.opacity = 0;
      setTimeout(function(){
        loader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
      }, 1000);
    };

    $(document).ready(function(){
      // Fade out success/error message after 3 seconds.
      setTimeout(function(){
         $('.success, .error').fadeOut('slow');
      }, 3000);

      // Load mechanics via AJAX.
      function loadMechanics(){
        $.ajax({
          url: 'getMechanics.php',
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            var $select = $('#mechanic_email');
            $select.empty();
            $select.append($('<option>', { value: '', text: '-- Choose Mechanic --' }));
            $.each(data, function(i, mechanic) {
              $select.append($('<option>', {
                value: mechanic.email,
                text: mechanic.garage_name + " - " + mechanic.full_name
              }));
            });
            // Checking if a mechanic_email was passed in the URL and pre-select it.
            var preselected = "<?php echo isset($_GET['mechanic_email']) ? $_GET['mechanic_email'] : ''; ?>";
            if(preselected !== ""){
              $select.val(preselected).change();
            }
          },
          error: function(){
            console.log('Error fetching mechanics');
          }
        });
      }
      
      loadMechanics(); // Load mechanics on page load

      // When a mechanic is selected, display details (including ratings).
      $('#mechanic_email').change(function(){
        var selectedEmail = $(this).val();
        $.ajax({
          url: 'getMechanics.php',
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            var mechanicInfo = "<p>Select a mechanic to view details.</p>";
            $.each(data, function(i, mechanic) {
              if(mechanic.email === selectedEmail){
                mechanicInfo = "<p><strong>Garage:</strong> " + mechanic.garage_name + "</p>";
                mechanicInfo += "<p><strong>Mechanic:</strong> " + mechanic.full_name + "</p>";
                mechanicInfo += "<p><strong>Services:</strong> " + mechanic.services_offered + "</p>";
                var avgRating = (typeof mechanic.avg_rating !== 'undefined' && mechanic.avg_rating !== null) ? mechanic.avg_rating : "No ratings yet";
                var reviewCount = (typeof mechanic.review_count !== 'undefined' && mechanic.review_count !== null) ? mechanic.review_count : 0;
                mechanicInfo += "<p><strong>Rating:</strong> " + avgRating;
                if(typeof mechanic.avg_rating !== 'undefined' && mechanic.avg_rating !== null){
                    mechanicInfo += " (" + reviewCount + " reviews)";
                }
                mechanicInfo += "</p>";
              }
            });
            $("#mechanic-details").html(mechanicInfo);
          }
        });
      });
    });
  </script>
</head>
<body>
  <!-- Loader -->
  <div id="loader">
    <div class="spinner"></div>
  </div>

  <!-- Main content: Booking Form -->
  <div id="main-content">
    <form class="box" method="post" action="booking.php">
      <h1 class="welcome">Book a Service</h1>
      <?php if ($error != "") { echo "<p class='error'>{$error}</p>"; } ?>
      <?php if ($success != "") { echo "<p class='success'>{$success}</p>"; } ?>
      
      <!-- Mechanic dropdown -->
      <select name="email" id="mechanic_email" required>
        <option value="">Choose Mechanic</option>
        <?php
          // Fallback: Initially populate dropdown from database.
          $query = "SELECT m.email, m.full_name, m.garage_name, m.services_offered,
                       (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.mechanic_email = m.email) AS avg_rating,
                       (SELECT COUNT(*) FROM reviews r WHERE r.mechanic_email = m.email) AS review_count
                    FROM mechanics m";
          $result = $conn->query($query);
          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<option value='" . htmlspecialchars($row['email']) . "'>" .
                       htmlspecialchars($row['garage_name']) . " - " .
                       htmlspecialchars($row['full_name']) .
                       "</option>";
              }
          } else {
              echo "<option value=''>No mechanics available</option>";
          }
        ?>
      </select>

      <!-- Mechanic details display -->
      <div id="mechanic-details">Select a mechanic to view details</div>

      <!-- Booking date, time, and description -->
      <input type="date" name="booking_date" required>
      <input type="time" name="booking_time" required>
      <textarea name="description" rows="4" placeholder="Describe the issue or service required"></textarea>
      <input type="submit" value="Book Service">
      <p>Go to <a href="../dashboard/main.php" style="text-decoration: none;">Homepage</a></p>
    </form>
  </div>
</body>
</html>
