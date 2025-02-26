<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:../loginup/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Main - Book Mechanic & Map</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
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
    #main-content { display: none; }
    /* Navigation Bar Styling */
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
      font-size: 16px;
      transition: color 0.3s;
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
    /* Map Container Styling */
    #map { height: 600px; width: 100%; }
    .map-header {
      text-align: center;
      padding: 10px;
      background: #f8f9fa;
      color: #191919;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <!-- Loader -->
  <div id="loader">
    <div class="spinner"></div>
  </div>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
      <a class="navbar-brand" href="../index.html">KITERE MOTORCYCLE MECHANIC TRACER</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="ajax.php">Search a Mechanic</a></li>
          <li class="nav-item"><a class="nav-link" href="../loginup/booking.php">Book Mechanic</a></li>
          <li class="nav-item"><a class="nav-link" href="../loginup/user_dashboard.php">View Bookings</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown">
              <img src="../img/profile.jpeg" alt="Profile Picture" class="profile-pic">
              <span>Profile</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
              <p style="color: black;"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
              <li><a class="dropdown-item" href="../loginup/logout.php" style="color:green;">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div id="main-content">
    
      <p style="text-align: center; color: rgb(83, 252, 83); font-style: italic; font-weight: 300; margin:0; background-color: #191919;"> Choose a blue marker to find nearby mechanics and book instantly.</p>
    
    <!-- Map Container -->
    <div id="map"></div>
  </div>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script>
    // Initialize the map
    var map = L.map('map').setView([-0.755324, 34.5999], 15);
    var baseLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    });
    baseLayer.addTo(map);

    // Load markers dynamically from getMarkers.php
    fetch('getMarkers.php')
      .then(response => response.json())
      .then(data => {
        if(Array.isArray(data) && data.length > 0){
          var bounds = [];
          data.forEach(function(mech) {
            var lat = parseFloat(mech.latitude);
            var lng = parseFloat(mech.longitude);
            if (!isNaN(lat) && !isNaN(lng)) {
              var popupContent = "<strong>" + mech.garage_name + "</strong><br>" +
                                 mech.full_name + "<br>";
              // Display rating if available
              if (mech.avg_rating !== undefined && mech.avg_rating !== null) {
                popupContent += "Rating: " + mech.avg_rating;
                if (mech.review_count !== undefined && mech.review_count !== null) {
                  popupContent += " (" + mech.review_count + " reviews)<br>";
                }
              } else {
                popupContent += "Rating: Not rated yet<br>";
              }
              // Add booking link (passing mechanic_email via GET)
              popupContent += "<a href='../loginup/booking.php?mechanic_email=" + encodeURIComponent(mech.email) + "' style='color:blue;'>Book Mechanic</a>";
              var marker = L.marker([lat, lng]).addTo(map);
              marker.bindPopup(popupContent);
              bounds.push([lat, lng]);
            }
          });
          if(bounds.length > 0){
            map.fitBounds(bounds);
          }
        }
      })
      .catch(error => console.error('Error loading markers:', error));

    // Optional: Get user's location and add a marker
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(position) {
          var user_lat = position.coords.latitude;
          var user_lng = position.coords.longitude;
          var userMarker = L.marker([user_lat, user_lng]).addTo(map);
          userMarker.bindPopup("You are here!").openPopup();
          map.setView([user_lat, user_lng], 15);
        },
        function(error) {
          console.error("Error retrieving location: " + error.message);
        }
      );
    }
    
    // Hide loader and show main content after page load
    window.onload = function() {
      var loader = document.getElementById('loader');
      loader.style.opacity = 0;
      setTimeout(function() {
        loader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
        if (typeof map !== 'undefined' && map !== null) {
          map.invalidateSize();
        }
      }, 1000);
    };
  </script>
</body>
</html>
