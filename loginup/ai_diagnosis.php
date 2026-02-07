<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}
$user_email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Vehicle Diagnosis - Kenya Vehicle Mechanic System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; background: #34495e; color: #fff; }
        .container { margin-top: 50px; }
        .card { background: #191919; border: 1px solid #2ecc71; color: #fff; margin-bottom: 20px; }
        .btn-primary { background: #2ecc71; border: none; }
        .btn-primary:hover { background: #27ae60; }
        .mechanic-card { padding: 15px; }
        .score-badge { float: right; font-size: 1.2em; }
        #results { margin-top: 30px; }
        .available { color: #2ecc71; }
        .unavailable { color: #e74c3c; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="user_dashboard.php">Back to Dashboard</a>
  </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="text-center mb-4">AI Vehicle Diagnosis</h2>
                <p class="text-center">Enter your vehicle symptoms, and our AI will find the best mechanic for you.</p>
                <form id="diagnosisForm">
                    <div class="mb-3">
                        <label for="symptoms" class="form-label">Describe the symptoms (e.g., "my engine is making a knocking sound")</label>
                        <textarea class="form-control" id="symptoms" rows="3" placeholder="Describe symptoms here..." required></textarea>
                    </div>
                    <input type="hidden" id="latitude" value="">
                    <input type="hidden" id="longitude" value="">
                    <button type="submit" class="btn btn-primary w-100" id="diagnoseBtn">Analyze Symptoms & Find Mechanics</button>
                </form>
            </div>

            <div id="loader" class="text-center d-none">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>AI is analyzing symptoms and locating mechanics...</p>
            </div>

            <div id="results"></div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Get user location on load
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                $('#latitude').val(position.coords.latitude);
                $('#longitude').val(position.coords.longitude);
            }, function(error) {
                console.error("Error getting location:", error);
                // Fallback coordinates (Nairobi)
                $('#latitude').val(-1.2921);
                $('#longitude').val(36.8219);
            });
        }

        $('#diagnosisForm').on('submit', function(e) {
            e.preventDefault();

            const symptoms = $('#symptoms').val();
            const lat = $('#latitude').val() || -1.2921;
            const lon = $('#longitude').val() || 36.8219;

            $('#loader').removeClass('d-none');
            $('#results').empty();
            $('#diagnoseBtn').prop('disabled', true);

            $.ajax({
                url: 'http://localhost:8000/diagnose',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    symptoms: symptoms,
                    latitude: parseFloat(lat),
                    longitude: parseFloat(lon)
                }),
                success: function(response) {
                    $('#loader').addClass('d-none');
                    $('#diagnoseBtn').prop('disabled', false);

                    if (response.length === 0) {
                        $('#results').html('<div class="alert alert-warning">No specialized mechanics found for these symptoms. Try general repair or broaden your description.</div>');
                        return;
                    }

                    let html = '<h3>Recommended Mechanics</h3>';
                    response.forEach(function(mech) {
                        const availabilityClass = mech.available ? 'available' : 'unavailable';
                        const availabilityText = mech.available ? 'Available Now' : 'Busy Today';

                        html += `
                            <div class="card mechanic-card">
                                <div class="row">
                                    <div class="col-md-9">
                                        <span class="badge bg-success score-badge">AI Score: ${mech.score}%</span>
                                        <h4>${mech.garage_name}</h4>
                                        <p><strong>Mechanic:</strong> ${mech.full_name}<br>
                                        <strong>Experience:</strong> ${mech.experience} years<br>
                                        <strong>Distance:</strong> ${mech.distance} km<br>
                                        <strong>Rating:</strong> ${mech.rating}/5.0<br>
                                        <strong>Services:</strong> ${mech.services}<br>
                                        <strong class="${availabilityClass}">${availabilityText}</strong></p>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-center">
                                        <a href="booking.php?mechanic_email=${encodeURIComponent(mech.email)}&description=${encodeURIComponent('AI Diagnosed: ' + symptoms)}"
                                           class="btn btn-outline-success w-100 ${mech.available ? '' : 'disabled'}">
                                           Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#results').html(html);
                },
                error: function(xhr, status, error) {
                    $('#loader').addClass('d-none');
                    $('#diagnoseBtn').prop('disabled', false);
                    $('#results').html('<div class="alert alert-danger">Error communicating with Intelligence Module. Please ensure the AI service is running.</div>');
                    console.error("API Error:", error);
                }
            });
        });
    });
</script>

</body>
</html>
