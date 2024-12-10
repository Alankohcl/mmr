<?php
include_once 'C:\xampp\htdocs\Final Year Project\database.php';

$data = json_decode(file_get_contents("php://input"));

$patient_id = $data['patient_id'];
$lab_assistant_id = $data['patient_id'];
$patient_id = $data['patient_id'];
$doctor_id = $data['patient_id'];
$blood_test_id = $data['patient_id'];
$health_metric_id = $data['patient_id'];

$sql = "INSERT INTO medical_reports
(patient_id, lab_assistant_id, doctor_id, remarks, blood_test_id, health_metric_id)
VALUES ($patient_id, $lab_assistant_id, $doctor_id, $blood_test_id, $health_metric_id)";

$stmt = $conn->prepare($sql);



