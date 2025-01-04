<?php
session_start();
include "database.php";

// Ensure the user is logged in as a lab assistant
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lab_assistant') {
    header("Location: login.php?error=unauthorized access");
    exit;
}

// Get the lab assistant's ID
$lab_assistant_id = $_SESSION['user_id'];

// Handle patient search
$patients = [];
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
    $search = "%" . $_POST['search'] . "%";
    $query = "SELECT user_id, name, email, phone_number, gender, date_of_birth, address
            FROM users 
            WHERE role = 'patient' AND (name LIKE ? OR user_id LIKE ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    $patients = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lab Assistant Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Side Panel -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky">
                <div class="pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">Search Patient</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Create Medical Report</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1>Lab Assistant Dashboard</h1>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="search" class="form-label">Search Patient</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Enter patient name or ID">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>

            <?php if (!empty($patients)): ?>
                <h2>Search Results</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?= $patient['user_id'] ?></td>
                            <td><?= $patient['name'] ?></td>
                            <td><?= $patient['email'] ?></td>
                            <td>
                                <a href="create_medical_report.php?patient_id=<?= $patient['user_id'] ?>" class="btn btn-success">Create Medical Report</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>