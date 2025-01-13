<?php

session_start();
include "database.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];

// Fetch the user's current profile details
$query = "SELECT name, email, password, phone_number, address FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No user found with the given ID.");
}

$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    // Update the user's profile
    $update_query = "UPDATE users SET name = ?, email = ?, password = ?, phone_number = ?, address = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_query);

    if ($update_stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $update_stmt->bind_param("sssssi", $name, $email, $password, $phone_number, $address, $user_id);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {
        echo "<script>alert('Profile updated successfully!'); window.location.href = 'view_user_profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update profile.');</script>";
    }

    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php' ?>
<?php include 'footer.php' ?>
    <div class="container mt-5">
        <h1>Edit User Profile</h1>
        <form method="POST" action="edit_user_profile.php">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="<?= htmlspecialchars($user['password']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
        <a href="javascript:history.back()" class="btn btn-secondary mt-4">Back</a>
    </div>
</body>
</html>

?>