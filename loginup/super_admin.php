<?php
require 'db_connect.php';

$email = 'sean@gmail.com';
$password = password_hash('Sean.9467', PASSWORD_DEFAULT);
$role = 'super_admin';

$stmt = $conn->prepare("INSERT INTO admins (email, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $password, $role);

if ($stmt->execute()) {
    echo "Super admin created successfully!";
} else {
    echo "Error creating super admin: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
