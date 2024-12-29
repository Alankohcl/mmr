<?php
session_start();
include "database.php";


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php?error=unauthorized access");
    exit;
}

$patient_id = $_SESSION['user_id'];
$query = "SELECT name, email, FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

$reports_query = "SELECT report_id, patient_id, lab_assistant_id, doctor_id, created_at, remarks, FROM medical_reports WHERE patient_id = ?";
$stmt = $conn->prepare($reports_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$reports_result = $stmt->get_result();
$reports = $reports_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Patient Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            .content {
                flex: 1;
                padding: 20px;
            }
            .sidebar img {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                margin-bottom: 15px;
            }
            .sidebar a {
                display: block;
                margin: 10px 0;
                color: #000;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="sidebar">
            <h4><?= htmlspecialchars($patient['name'])?></h4>
            <a href="#" onclick="showView('viewReports')">View Medical Report</a>
            <a href="#" onlcick="showView('trendline')">Trendline Selection</a>
            <a href="logout.php">Logout</a>
        </div>

        <div class="content">
            <!--View Medical Reports-->
            <div id="viewReports" class="view" style="display: none;">
                <h2 Medical Reports History></h2>
                <?php if(!empty($reports)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($reports as $report): ?>
                                <tr>
                                    <td><?= htmlspecialchars($report['report_date']) ?></td>
                                    <td>
                                        <a href="view_report.php?id=<?= $report['report_id'] ?>" class="btn btn-sm btn-primary">View</a>
                                        <a href="download_report.php?id=<?= $report['report_id'] ?>" class="btn btn-sm btn-success">Download</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <P>No Medical Reports Found.</P>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>