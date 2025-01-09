<?php
include 'database.php';

header("Content-Type: application/json");

// Get patient_id from the request
$patient_id = $_GET['patient_id'];

// Fetch medical reports for the patient
$sql = "SELECT * FROM medical_reports WHERE patient_id = $patient_id";
$result = $conn->query($sql);

$reports = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reports[] = $row;
    }
}

// Return JSON response
echo json_encode($reports);

$conn->close();
?>