<?php
include 'database.php';

header("Content-Type: application/json");

// Get data from the request
$user_id = $_POST['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$phone_number = $_POST['phone_number'];
$gender = $_POST['gender'];
$date_of_birth = $_POST['date_of_birth'];
$address = $_POST['address'];

// Update user details
$sql = "UPDATE users SET name = ?, email = ?, password = ?,  phone_number = ?, gender = ?, date_of_birth = ?, address = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssi", $name, $email, $password, $phone_number, $gender, $date_of_birth, $address, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "User details updated successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update user details"
    ]);
}

$stmt->close();
$conn->close();
?>