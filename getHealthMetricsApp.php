<?php
include 'database.php';

header("Content-Type: application/json");

// Get patient_id from the request
$patient_id = $_GET['patient_id'];

// Fetch health metrics
$sql = "SELECT * FROM health_metrics WHERE patient_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$healthMetrics = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $healthMetrics[] = $row;
    }
    echo json_encode($healthMetrics);
} else {
    echo json_encode([]);
}

$stmt->close();
$conn->close();
?>