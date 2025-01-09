<?php
// fetch_trendline_data.php
session_start();
include "database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

$patient_id = $_SESSION['user_id'];
$metric = $_GET['metric'] ?? '';

// Map metric names to database column names
$metricColumns = [
    'bp' => 'blood_pressure',
    'bmi' => 'body_mass_index',
    'hba1c' => 'hemoglobin_a1c',
    'pr' => 'pulse_rate',
    'rbs' => 'random_blood_sugar'
];

if(!array_key_exists($metric, $metricColumns)){
    echo json_encode(["error" => "Invalid metric"]);
    exit;
}

$column = $metricColumns[$metric];

// Fetch trendline data from health_metrics table
$query = "SELECT created_at, $column FROM health_metrics WHERE patient_id = ? ORDER BY created_at";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$values = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['created_at']; // Dates
    $values[] = $row[$column]; // Metric values
}

$stmt->close();
$conn->close();

// Return data as JSON
echo json_encode([
    "labels" => $labels,
    "values" => $values
]);
?>