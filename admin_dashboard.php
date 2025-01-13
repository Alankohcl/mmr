<?php
session_start();
include "database.php";

if (!isset($_SESSION['role'])) {
    die("Session role not set.");
}

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized access. Role: " . $_SESSION['role']);
}

$admin_id = $_SESSION['user_id'];
$query = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                display: flex;
                min-height: 100vh;
                margin: 0;
            }
            .sidebar {
                width: 250px;
                background-color: #f8f9fa;
                padding: 20px;
                border-right: 1px solid #ddd;
            }
            .sidebar a {
                display: block;
                margin: 10px 0;
                color: #000;
                text-decoration: none;
            }
            .content {
                flex: 1;
                padding: 20px;
                overflow-y: auto;
            }
        </style>
    </head>
    <body>
    <?php include 'header.php' ?>
    <?php include 'footer.php' ?>
        <div class="sidebar">
            <h4><?= htmlspecialchars($admin['name'])?></h4>
            <a href="logout.php">Logout</a>
        </div>

        <div class="content">
            <h1>Admin Dashboard</h1>
            <h2>Welcome Admin</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage User</h5>
                            <p class="card-text">Add, Edit, or Delete User in the system.</p>
                            <a href="manage_users.php" class="btn btn-primary">GO</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Hospital</h5>
                            <p class="card-text">Add, Edit, or Delete Hospital in the system.</p>
                            <a href="manage_hospital.php" class="btn btn-primary">GO</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">View Reports</h5>
                            <p class="card-text">View all submitted Medical reports</p>
                            <a href="view_reports.php" class="btn btn-primary">GO</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>