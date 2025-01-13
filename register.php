<?php
// registerWeb.php
include 'database.php';

// Fetch hospitals for the dropdown
$sql = "SELECT hospital_id, name FROM hospitals";
$result = $conn->query($sql);
$hospitals = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .hidden{
                display: none;
            }
        </style>
    </head>
    <body>
    <?php include 'header.php' ?>
    <?php include 'footer.php' ?>
        <h2>Register</h2>
        <form method="POST", action="registerWeb.php">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>
            <br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>

            <label for="phone">Phone Number:</label>
            <input type="phone" id="phone" name="phone" required>
            <br>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            <br>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>
            <br>

            <label for="role">Role:</label>
            <select id="role" name="role" required onChange="toggleDoctorFields()">
                <option value="patient">Patient</option>
                <option value="doctor">Doctor</option>
                <option value="lab_assistant">Lab Assistant</option>
                <option value="nurse">Nurse</option>
                <option value="admin">Admin</option>
            </select>
            <br>

            <div id="hospitalField" class="hidden">
                <label for="hospital">Hospital:</label>
                <select id="hospital" name="hospital">
                    <!-- <option value="upnmMedicalCenter">UPNM Medical Center</option> -->
                     <?php
                        foreach ($hospitals as $hospital) {
                            echo "<option value='{$hospital['hospital_id']}'>{$hospital['name']}</option>";
                        }
                     ?>
                </select>
            </div>
            
            <div id="specializationField" class="hidden">
                <label for="specialization">Specialization:</label>
                <select id="specialization" name="specialization">
                    <option value="hematology">Hematology</option>
                    <option value="cardiology">Cardiology</option>
                </select>
                <br>
            </div>

            <label for="address">Address:</label>
            <textarea id="address" name="address" required></textarea>
            <br>

            <button type="submit">Register</button>
            <button type="reset">Reset</button>
        </form>

        <script>
            function toggleDoctorFields() {
                const role = document.getElementById("role").value;
                const hospitalField = document.getElementById("hospitalField");
                const specializationField = document.getElementById("specializationField");

                if (role === "doctor") {
                    hospitalField.classList.remove("hidden");
                    specializationField.classList.remove("hidden");
                } else {
                    hospitalField.classList.add("hidden");
                    specializationField.classList.add("hidden");
                }
            }

            // Initialize the form on page load
            document.addEventListener("DOMContentLoaded", function () {
                toggleDoctorFields(); // Set initial visibility
            });

        </script>
    </body>
</html>
