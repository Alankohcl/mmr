<?php
session_start();
include "database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php?error=unauthorized access");
    exit;
}

$report_id = $_GET['report_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $remarks = $_POST['remarks'];
    $query = "UPDATE medical_report SET remarks = ? WHERE report_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $remarks, $report_id);
    $stmt->execute();
    header("Location: doctor_dashboard.php?success=Remarks updated");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Remarks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Add Remarks</h1>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="4"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
