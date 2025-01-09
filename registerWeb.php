<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get POST data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $role = $_POST['role'] ?? '';
    $specialization = $_POST['specialization'] ?? ''; // Optional field
    $address = $_POST['address'] ?? '';
    $hospital_id = $_POST['hospital'] ?? '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($gender) || empty($dob) || empty($role) || empty($address)) {
        header("Location: register.php?error=All fields are required.");
        exit();
    }

    // Check if email already exists
    $sql_check = "SELECT email FROM users WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check === false) {
        header("Location: register.php?error=Database error.");
        exit();
    }

    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        header("Location: register.php?error=Email already exists.");
        exit();
    }

    // Handle registration based on role
    if ($role === "doctor") {
        if (empty($specialization || empty($hospital_id))) {
            header("Location: register.php?error=Specialization and Hospital is required for doctors.");
            exit();
        }

        $sql = "INSERT INTO users (name, email, password, 
        phone_number, gender, date_of_birth, address, role, specialization, hospital_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            header("Location: register.php?error=Database error.");
            exit();
        }
        $stmt->bind_param("ssssssssss", 
        $name, $email, $password, $phone, $gender, 
        $dob, $address, $role, $specialization, $hospital_id);
    
    } else {
        $sql = "INSERT INTO users (name, email, password, 
        phone_number, gender, date_of_birth, address, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            header("Location: register.php?error=Database error.");
            exit();
        }
        $stmt->bind_param("ssssssss", 
        $name, $email, $password, $phone, $gender, 
        $dob, $address, $role);
    }

    if ($stmt->execute()) {
        header("Location: login.php?success=1");
    } else {
        header("Location: register.php?error=Registration failed.");
    }

    $stmt->close();
    $stmt_check->close();
    $conn->close();
} else {
    header("Location: register.php");
    exit();
}
?>