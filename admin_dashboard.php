<?php
session_start();
// echo "<pre>Session Data: ";
// print_r($_SESSION);
// echo "</pre>";

if (!isset($_SESSION['role'])) {
    die("Session role not set.");
}

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized access. Role: " . $_SESSION['role']);
}

//echo "Passed all checks. Now displaying HTML.";
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container-md-4">
            <h1>Admin Dashboard</h1>
            <h2>Welcome Admin</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage User</h5>
                            <p class="card-text">Add, Edit, or Delete User in the system.</p>
                            <a href="manage_users.php" class="btn btn-primary">GO</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Hospital</h5>
                            <p class="card-text">Add, Edit, or Delete Hospital in the system.</p>
                            <a href="manage_hospital.php" class="btn btn-primary">GO</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">View Reports</h5>
                            <p class="card-text">View all submitted Medical reports</p>
                            <a href="view_reports.php" class="btn btn-primary">GO</a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="content" class="mt-4"></div>
        </div>
        
        <div>
            <h1>Test connection</h1>
            <button onclick="testConnection()">Test Connection</button>
            <pre id="output"></pre>
        </div>

        <script>
            async function testConnetion(){
                try{
                    const response = await fetch(database.php);
                    const data = await response.json();
                    console.log(data);
                    document.getElementById("output").textContext = JSON.stringify(data,null,2);

                }catch(error){
                    console.error("error connecting to the sever:", error);
                    
                }
            }
        </script>
    </body>
</html>