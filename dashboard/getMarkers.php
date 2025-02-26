
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../loginup/db_connect.php'; 

header("Content-Type: application/json");

// Query to retrieve mechanics with location data and their ratings.
$query = "SELECT 
            m.email, 
            m.full_name, 
            m.garage_name, 
            m.latitude,
            m.longitude,
            (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.mechanic_email = m.email) AS avg_rating,
            (SELECT COUNT(*) FROM reviews r WHERE r.mechanic_email = m.email) AS review_count
          FROM mechanics m
          WHERE m.latitude IS NOT NULL AND m.longitude IS NOT NULL";

$result = $conn->query($query);

if (!$result) {
    echo json_encode(array("error" => "Query failed: " . $conn->error));
    exit;
}

$markers = array();

while ($row = $result->fetch_assoc()) {
    $markers[] = $row;
}

echo json_encode($markers);
?>
