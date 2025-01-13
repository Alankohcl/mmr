<?php
session_start();
include "database.php";


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php?error=unauthorized access");
    exit;
}

$patient_id = $_SESSION['user_id'];
$query = "SELECT name, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

$reports_query = "SELECT report_id, patient_id, lab_assistant_id, doctor_id, created_at, remarks FROM medical_reports WHERE patient_id = ?";
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
            .sidebar a {
                display: block;
                margin: 10px 0;
                color: #000;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
    <?php include 'header.php' ?>
    <?php include 'footer.php' ?>
        <div class="sidebar">
            <h4><?= htmlspecialchars($patient['name'])?></h4>
            <a href="view_user_profile.php">Profile</a>
            <a href="#" onclick="showView('viewReports')">View Medical Report</a>
            <a href="#" onclick="showView('trendline')">Trendline Selection</a>
            <a href="logout.php">Logout</a>
        </div>

        <div class="content">
            <!--View Medical Reports-->
            <div id="viewReports" class="view" style="display: none;">
                <h2>Medical Reports History></h2>
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
                                    <td><?= htmlspecialchars($report['created_at']) ?></td>
                                    <td>
                                        <a href="view_reports.php?id=<?= $report['report_id'] ?>" class="btn btn-sm btn-primary">View</a>
                                        <a href="download_report.php?id=<?= $report['report_id'] ?>" class="btn btn-sm btn-success">Download</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No Medical Reports Found.</p>
                <?php endif; ?>
            </div>

            <!-- Trendline Selection -->
            <div id="trendline" class="view" style="display: none">
                <h2>Trendline Selection</h2>
                <form id="trendlineForm" onsubmit="generateTrendline(event)">
                    <div class="mb-3">
                        <label for="metric" class="form-label">Select Metric</label>
                        <select class="form-select" id="metric" name="metric">
                            <option value="bp">Blood Pressure</option>
                            <option value="bmi">BMI</option>
                            <option value="hba1c">HbA1c</option>
                            <option value="pr">Pulse Rate</option>
                            <option value="rbs">Random Blood Sugar</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Generate Trendline</button>
                </form>
                <canvas id="trendlineChart" width="400" height="200" class="mt-4"></canvas>
            </div>
        </div>

        <script>
        // Function to show the appropriate view
        function showView(viewId) {
            document.querySelectorAll('.view').forEach(view => view.style.display = 'none');
            document.getElementById(viewId).style.display = 'block';
            window.location.hash = viewId;
        }

        // Function to generate trendline
        function generateTrendline(event) {
            event.preventDefault();
            const metric = document.getElementById('metric').value;
            console.log("selected metric:", metric);
            
            // Fetch trendline data via AJAX
            fetch(`fetch_trendline_data.php?metric=${metric}`)
                .then(response => response.json())
                .then(data => {
                    if(data.error){
                        alert(data.error);
                        return;
                    }
                    //destroy the existing chart if exist
                    const chartCanvas = document.getElementById('trendlineChart');
                    if (window.myChart){
                        window.myChart.destroy();
                    }

                    //render the chart
                    const ctx = chartCanvas.getContext('2d');
                    window.myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: metric.toUpperCase(),
                                data: data.values,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 2,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: { 
                                    title: {
                                        display:true,
                                        text: 'Date'
                                    }
                                },
                                y: { 
                                    title: {
                                        display:true,
                                        text:metric.toUpperCase()
                                    },
                                    beginAtZero: true 
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error("error fetching trendline data:", error);
                    alert("Failed to fetch trendline data. Please try again, please ensure backend is returning valid JSON");
                });
        }

        // Show the first view by default
        showView('viewReports');
        </script>
    </body>
</html>