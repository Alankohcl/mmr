<?php
include 'database.php';

header("Content-Type: application/json");

// Get user_id from the request
$user_id = $_GET['user_id'];

// Fetch user details
$sql = "SELECT user_id, name, email, phone_number, gender, date_of_birth, address FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "message" => "User details fetched successfully",
        "user" => $row
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "User not found"
    ]);
}

$stmt->close();
$conn->close();
?>