<?php
// Start the session
session_start();

// Include the database connection file
require_once 'database.php'; // Replace with your database connection file

// Check if report_id is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid report ID.");
}

$report_id = $_GET['id'];

// Fetch the report details
$query = "
    SELECT 
        mr.report_id, 
        mr.patient_id, 
        mr.lab_assistant_id, 
        mr.doctor_id, 
        mr.remarks, 
        mr.created_at AS report_created_at,
        bt.haemoglobin_level, 
        bt.platelet_count, 
        bt.neutrophils_percent, 
        bt.lymphocytes_percent, 
        bt.monocytes_percent, 
        bt.eosinophils_percent, 
        bt.basophils_percent,
        hm.blood_pressure, 
        hm.body_mass_index, 
        hm.hemoglobin_a1c, 
        hm.pulse_rate, 
        hm.random_blood_sugar,
        hm.created_at AS health_metric_created_at,
        patient.name AS patient_name,
        lab_assistant.name AS lab_assistant_name,
        doctor.name AS doctor_name
    FROM 
        medical_reports mr
    LEFT JOIN 
        blood_tests bt ON mr.blood_test_id = bt.blood_test_id
    LEFT JOIN 
        health_metrics hm ON mr.health_metric_id = hm.health_metric_id
    LEFT JOIN 
        users AS patient ON mr.patient_id = patient.user_id
    LEFT JOIN 
        users AS lab_assistant ON mr.lab_assistant_id = lab_assistant.user_id
    LEFT JOIN 
        users AS doctor ON mr.doctor_id = doctor.user_id
    WHERE 
        mr.report_id = ?
";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $report_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No report found with the given ID.");
}

$report = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Medical Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php' ?>
    <div class="container mt-5">
        <h1>Medical Report Details</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Report ID: <?= htmlspecialchars($report['report_id']) ?></h5>
                <p class="card-text"><strong>Patient ID:</strong> <?= htmlspecialchars($report['patient_name']) ?></p>
                <p class="card-text"><strong>Lab Assistant ID:</strong> <?= htmlspecialchars($report['lab_assistant_name']) ?></p>
                <p class="card-text"><strong>Doctor ID:</strong> <?= htmlspecialchars($report['doctor_name']) ?></p>
                <p class="card-text"><strong>Remarks:</strong> <?= htmlspecialchars($report['remarks']) ?></p>
                <p class="card-text"><strong>Report Created At:</strong> <?= htmlspecialchars($report['report_created_at']) ?></p>
            </div>
        </div>

        <h2 class="mt-4">Blood Test Details</h2>
        <div class="card">
            <div class="card-body">
                <p class="card-text"><strong>Haemoglobin Level:</strong> <?= htmlspecialchars($report['haemoglobin_level']) ?></p>
                <p class="card-text"><strong>Platelet Count:</strong> <?= htmlspecialchars($report['platelet_count']) ?></p>
                <p class="card-text"><strong>Neutrophils Percent:</strong> <?= htmlspecialchars($report['neutrophils_percent']) ?></p>
                <p class="card-text"><strong>Lymphocytes Percent:</strong> <?= htmlspecialchars($report['lymphocytes_percent']) ?></p>
                <p class="card-text"><strong>Monocytes Percent:</strong> <?= htmlspecialchars($report['monocytes_percent']) ?></p>
                <p class="card-text"><strong>Eosinophils Percent:</strong> <?= htmlspecialchars($report['eosinophils_percent']) ?></p>
                <p class="card-text"><strong>Basophils Percent:</strong> <?= htmlspecialchars($report['basophils_percent']) ?></p>
            </div>
        </div>

        <h2 class="mt-4">Health Metrics</h2>
        <div class="card">
            <div class="card-body">
                <p class="card-text"><strong>Blood Pressure:</strong> <?= htmlspecialchars($report['blood_pressure']) ?></p>
                <p class="card-text"><strong>Body Mass Index:</strong> <?= htmlspecialchars($report['body_mass_index']) ?></p>
                <p class="card-text"><strong>Hemoglobin A1c:</strong> <?= htmlspecialchars($report['hemoglobin_a1c']) ?></p>
                <p class="card-text"><strong>Pulse Rate:</strong> <?= htmlspecialchars($report['pulse_rate']) ?></p>
                <p class="card-text"><strong>Random Blood Sugar:</strong> <?= htmlspecialchars($report['random_blood_sugar']) ?></p>
                <p class="card-text"><strong>Health Metric Created At:</strong> <?= htmlspecialchars($report['health_metric_created_at']) ?></p>
            </div>
        </div>

        <a href="javascript:history.back()" class="btn btn-secondary mt-4">Back</a>
        <a href="javascript:window.print()" class="btn btn-secondary mt-4">Print</a>
    </div>
</body>
</html>