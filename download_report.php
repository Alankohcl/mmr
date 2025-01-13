<?php
// Start the session
session_start();

// Include the database connection file
require_once 'database.php'; // Replace with your database connection file

define('FPDF_FONTPATH', 'font');

// Include the FPDF library
require('fpdf.php'); // Replace with the correct path to FPDF library

// Check if report_id is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid report ID.");
}

$report_id = $_GET['id'];

// Fetch the report details with user names
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

// Create a new PDF instance
$pdf = new FPDF();
$pdf->AddPage();

// Set font for the title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Medical Report', 0, 1, 'C');
$pdf->Ln(10);

// Set font for the content
$pdf->SetFont('Arial', '', 12);

// Add Report Details
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Report Details', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Report ID: ' . $report['report_id'], 0, 1);
$pdf->Cell(0, 10, 'Patient Name: ' . $report['patient_name'], 0, 1);
$pdf->Cell(0, 10, 'Lab Assistant: ' . $report['lab_assistant_name'], 0, 1);
$pdf->Cell(0, 10, 'Doctor: ' . $report['doctor_name'], 0, 1);
$pdf->Cell(0, 10, 'Remarks: ' . $report['remarks'], 0, 1);
$pdf->Cell(0, 10, 'Report Date: ' . $report['report_created_at'], 0, 1);
$pdf->Ln(10);

// Add Blood Test Details
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Blood Test Details', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Haemoglobin Level: ' . $report['haemoglobin_level'], 0, 1);
$pdf->Cell(0, 10, 'Platelet Count: ' . $report['platelet_count'], 0, 1);
$pdf->Cell(0, 10, 'Neutrophils Percent: ' . $report['neutrophils_percent'], 0, 1);
$pdf->Cell(0, 10, 'Lymphocytes Percent: ' . $report['lymphocytes_percent'], 0, 1);
$pdf->Cell(0, 10, 'Monocytes Percent: ' . $report['monocytes_percent'], 0, 1);
$pdf->Cell(0, 10, 'Eosinophils Percent: ' . $report['eosinophils_percent'], 0, 1);
$pdf->Cell(0, 10, 'Basophils Percent: ' . $report['basophils_percent'], 0, 1);
$pdf->Ln(10);

// Add Health Metrics
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Health Metrics', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Blood Pressure: ' . $report['blood_pressure'], 0, 1);
$pdf->Cell(0, 10, 'Body Mass Index: ' . $report['body_mass_index'], 0, 1);
$pdf->Cell(0, 10, 'Hemoglobin A1c: ' . $report['hemoglobin_a1c'], 0, 1);
$pdf->Cell(0, 10, 'Pulse Rate: ' . $report['pulse_rate'], 0, 1);
$pdf->Cell(0, 10, 'Random Blood Sugar: ' . $report['random_blood_sugar'], 0, 1);
$pdf->Cell(0, 10, 'Health Metric Date: ' . $report['health_metric_created_at'], 0, 1);

// Output the PDF as a downloadable file
$pdf->Output('D', 'Medical_Report_' . $report['report_id'] . '.pdf');
?>