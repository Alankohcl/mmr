<?php
session_start();

// Include the database connection file
require_once 'database.php'; // Replace with your database connection file

// Check if report_id is provided in the URL
if (!isset($_SESSION['role'])) {
    die("Invalid role.");
}

$user_id = $_SESSION['user_id'];

$query = "SELECT name, email, password, phone_number, gender, date_of_birth, address, role, specialization, created_at
        FROM users
        WHERE user_id = ?";
$stmt = $conn->prepare($query);

if($stmt === false){
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0){
    die("No user found with the given id");
}

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php' ?>
<?php include 'footer.php' ?>
    <div class="container mt-5">
        <h1>User Profile</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Profile Details</h5>
                <p class="card-text"><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p class="card-text"><strong>Password:</strong> <?= htmlspecialchars($user['password']) ?></p>
                <p class="card-text"><strong>Phone Number:</strong> <?= htmlspecialchars($user['phone_number']) ?></p>
                <p class="card-text"><strong>Gender:</strong> <?= htmlspecialchars($user['gender']) ?></p>
                <p class="card-text"><strong>Date of Birth:</strong> <?= htmlspecialchars($user['date_of_birth']) ?></p>
                <p class="card-text"><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
                <p class="card-text"><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
                <p class="card-text"><strong>Specialization:</strong> <?= htmlspecialchars($user['specialization']) ?></p>
                <p class="card-text"><strong>Creation Date:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
                <a href="edit_user_profile.php" class="btn btn-primary">Edit Profile</a>
            </div>
            <a href="javascript:history.back()" class="btn btn-secondary mt-4">Back</a>
        </div>
    </div>
</body>
</html>