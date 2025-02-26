<?php
// getMechanics links to booking.php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db_connect.php';

header("Content-Type: application/json");

// Check connection
if ($conn->connect_error) {
    echo json_encode(array("error" => "Database connection failed: " . $conn->connect_error));
    exit;
}

// Updated query to include rating information.
$query = "SELECT 
            m.email, 
            m.full_name, 
            m.garage_name, 
            m.services_offered,
            (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.mechanic_email = m.email) AS avg_rating,
            (SELECT COUNT(*) FROM reviews r WHERE r.mechanic_email = m.email) AS review_count
          FROM mechanics m";

$result = $conn->query($query);

if (!$result) {
    echo json_encode(array("error" => "Query failed: " . $conn->error));
    exit;
}

$mechanics = array();

while ($row = $result->fetch_assoc()) {
    $mechanics[] = $row;
}

echo json_encode($mechanics);
?>
