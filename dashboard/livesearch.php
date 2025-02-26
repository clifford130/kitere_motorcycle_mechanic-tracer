<?php
include '../loginup/db_connect.php'; 

if(isset($_POST['input'])){
    $input = '%' . $_POST['input'] . '%'; 

    $query = "SELECT m.*, 
                (SELECT ROUND(AVG(r.rating), 1) FROM reviews r WHERE r.mechanic_email = m.email) AS avg_rating,
                (SELECT COUNT(*) FROM reviews r WHERE r.mechanic_email = m.email) AS review_count
              FROM mechanics m
              WHERE m.full_name LIKE ? OR m.garage_name LIKE ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $input, $input); 
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if($result){ 
            if(mysqli_num_rows($result) > 0){
                echo "<style>
                    .mechanic-card {
                        border: 1px solid #ccc;
                        border-radius: 10px;
                        padding: 20px;
                        margin-top: 20px;
                        background-color: rgb(248, 191, 191);
                        width: 500px;
                        font-size: larger;
                    }
                    h6 {
                        font-size: larger;
                    }
                    .mechanic-card a.book-link {
                        color: #2ecc71;
                        text-decoration: none;
                        font-weight: bold;
                    }
                    .mechanic-card a.book-link:hover {
                        color: rgb(52, 80, 240);
                    }
                </style>";
                echo "<div class='cards-container'>";
                while($row = mysqli_fetch_assoc($result)){
                    $email = htmlspecialchars($row['email']); 
                    $full_name = htmlspecialchars($row['full_name']);
                    $phone_number = htmlspecialchars($row['phone_number']);
                    $garage_name = htmlspecialchars($row['garage_name']);
                    $services = htmlspecialchars($row['services_offered']);
                    $avgRating = ($row['avg_rating'] !== null) ? $row['avg_rating'] : "No ratings yet";
                    $reviewCount = ($row['review_count'] !== null) ? $row['review_count'] : 0;
                    
                    echo "<div class='mechanic-card'>
                            <h3>$full_name</h3>
                            <p>Email: $email</p>
                            <p>Business Name: $garage_name</p>
                            <p>Services: $services</p>
                            <p>Rating: $avgRating";
                    if($row['avg_rating'] !== null){
                        echo " ($reviewCount reviews)";
                    }
                    echo "</p>
                            <p>Phone Number: <a href='tel:$phone_number' style='text-decoration: none'>$phone_number</a></p>
                            <p><a class='book-link' href='../loginup/booking.php?mechanic_email=" . urlencode($email) . "'>Book Mechanic</a></p>
                          </div>";
                }
                echo "</div>";
            } else {
                echo "<h6>No record found</h6>";
            }
        } else {
            echo "<h6>Error executing query</h6>";
        }
    } else {
        echo "<h6>Error preparing statement</h6>";
    }
} else {
    echo "<h6>No input received</h6>";
}
?>
