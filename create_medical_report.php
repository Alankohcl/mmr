<?php
session_start();
include "database.php";

// Ensure the user is logged in as a lab assistant
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lab_assistant') {
    header("Location: login.php?error=unauthorized access");
    exit;
}

$lab_assistant_id = $_SESSION['user_id'];
$patient_id = $_GET['patient_id'] ?? null;

if(!$patient_id){
    echo "the patient id is not set";
    die;
}

$check_patient_query = "SELECT * FROM users WHERE user_id = ? AND role = 'patient'";
$stmt = $conn->prepare($check_patient_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows ===0){
    die("error: patient not found or not valid");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Insert into blood_test table
    $blood_test_query = "INSERT INTO blood_tests (
    haemoglobin_level, platelet_count, neutrophils_percent, 
    lymphocytes_percent, monocytes_percent, eosinophils_percent, basophils_percent) 
    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($blood_test_query);

    $stmt->bind_param("ddddddd", 
    $_POST['haemoglobin_level'], $_POST['platelet_count'], 
    $_POST['neutrophils_percent'], $_POST['lymphocytes_percent'], 
    $_POST['monocytes_percent'], $_POST['eosinophils_percent'], 
    $_POST['basophils_percent']);

    $stmt->execute();
    $blood_test_id = $stmt->insert_id;

    // Insert into health_metric table
    $health_metric_query = "INSERT INTO health_metrics (
    patient_id, blood_pressure, body_mass_index, 
    hemoglobin_a1c, pulse_rate, random_blood_sugar, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($health_metric_query);
    $stmt->bind_param("iddddd", 
    $patient_id, $_POST['blood_pressure'], $_POST['body_mass_index'], 
    $_POST['hemoglobin_a1c'], $_POST['pulse_rate'], 
    $_POST['random_blood_sugar']);

    $stmt->execute();
    $health_metric_id = $stmt->insert_id;

    // Insert into medical_report table
    $medical_report_query = "INSERT INTO medical_reports (
    patient_id, lab_assistant_id, doctor_id, remarks, 
    created_at, blood_test_id, health_metric_id) VALUES (?, ?, ?, ?, NOW(), ?, ?)";
    $stmt = $conn->prepare($medical_report_query);
    $stmt->bind_param("iiisii", 
    $patient_id, $lab_assistant_id, 
    $_POST['doctor_id'], $_POST['remarks'], $blood_test_id, $health_metric_id);
    $stmt->execute();

    echo "<script>alert('Medical Report created successfully!'); window.location.href='lab_assistant_dashboard.php';</script>";
}

$conn->close();
?>
<!-- HTML Form for Medical Report -->
<!DOCTYPE html>
<html>
<head>
    <title>Create Medical Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Create Medical Report</h1>
    <form method="POST" action="">
        <h3>Patient Details</h3>
        <div class="mb-3">
            <label for="doctor_id" class="form-label">Select Doctor</label>
            <select id="doctor_id" name="doctor_id" class="form-select" required>
                <option value="" disabled selected>Select a doctor</option>
                <?php
                include "database.php";
                $query = "SELECT user_id, name FROM users WHERE role = 'doctor'";
                $result = $conn->query($query);
                while ($doctor = $result->fetch_assoc()) {
                    echo "<option value='{$doctor['user_id']}'>{$doctor['name']}</option>";
                }
                ?>
            </select>
        </div>

        <h3>Blood Test Readings</h3>
        <div class="mb-3">
            <label for="haemoglobin_level" class="form-label">Haemoglobin Level</label>
            <input type="number" step="0.1" class="form-control" id="haemoglobin_level" name="haemoglobin_level" required>
        </div>
        <div class="mb-3">
            <label for="platelet_count" class="form-label">Platelet Count</label>
            <input type="number" class="form-control" id="platelet_count" name="platelet_count" required>
        </div>
        <div class="mb-3">
            <label for="neutrophils_percent" class="form-label">Neutrophils (%)</label>
            <input type="number" step="0.1" class="form-control" id="neutrophils_percent" name="neutrophils_percent" required>
        </div>
        <div class="mb-3">
            <label for="lymphocytes_percent" class="form-label">Lymphocytes (%)</label>
            <input type="number" step="0.1" class="form-control" id="lymphocytes_percent" name="lymphocytes_percent" required>
        </div>
        <div class="mb-3">
            <label for="monocytes_percent" class="form-label">Monocytes (%)</label>
            <input type="number" step="0.1" class="form-control" id="monocytes_percent" name="monocytes_percent" required>
        </div>
        <div class="mb-3">
            <label for="eosinophils_percent" class="form-label">Eosinophils (%)</label>
            <input type="number" step="0.1" class="form-control" id="eosinophils_percent" name="eosinophils_percent" required>
        </div>
        <div class="mb-3">
            <label for="basophils_percent" class="form-label">Basophils (%)</label>
            <input type="number" step="0.1" class="form-control" id="basophils_percent" name="basophils_percent" required>
        </div>

        <h3>Health Metrics</h3>
        <div class="mb-3">
            <label for="blood_pressure" class="form-label">Blood Pressure</label>
            <input type="text" class="form-control" id="blood_pressure" name="blood_pressure" required>
        </div>
        <div class="mb-3">
            <label for="body_mass_index" class="form-label">Body Mass Index</label>
            <input type="number" step="0.1" class="form-control" id="body_mass_index" name="body_mass_index" required>
        </div>
        <div class="mb-3">
            <label for="hemoglobin_a1c" class="form-label">HbA1c</label>
            <input type="number" step="0.1" class="form-control" id="hemoglobin_a1c" name="hemoglobin_a1c" required>
        </div>
        <div class="mb-3">
            <label for="pulse_rate" class="form-label">Pulse Rate</label>
            <input type="number" step="0.1" class="form-control" id="pulse_rate" name="pulse_rate" required>
        </div>
        <div class="mb-3">
            <label for="random_blood_sugar" class="form-label">Random Blood Sugar</label>
            <input type="number" step="0.1" class="form-control" id="random_blood_sugar" name="random_blood_sugar" required>
        </div>

        <h3>Remarks</h3>
        <div class="mb-3">
            <label for="remarks" class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks" name="remarks" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Submit</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
