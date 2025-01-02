<?php
session_start();
include "database.php";

// Ensure the user is logged in as a doctor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php?error=unauthorized access");
    exit;
}

$doctor_id = $_SESSION['user_id'];

// Fetch all medical reports assigned to the doctor
$query = "SELECT m.report_id, u.name as patient_name, l.name as lab_assistant_name, m.created_at, m.remarks 
          FROM medical_report m 
          JOIN users u ON m.patient_id = u.user_id 
          JOIN users l ON m.lab_assistant_id = l.user_id
          WHERE m.doctor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$reports = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Doctor Dashboard</h1>
    <h2>Assigned Medical Reports</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Report ID</th>
            <th>Patient Name</th>
            <th>Lab Assistant</th>
            <th>Created At</th>
            <th>Remarks</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reports as $report): ?>
            <tr>
                <td><?= $report['report_id'] ?></td>
                <td><?= $report['patient_name'] ?></td>
                <td><?= $report['lab_assistant_name'] ?></td>
                <td><?= $report['created_at'] ?></td>
                <td><?= $report['remarks'] ?></td>
                <td>
                    <a href="add_remarks.php?report_id=<?= $report['report_id'] ?>" class="btn btn-primary">Add Remarks</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
