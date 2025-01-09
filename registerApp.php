<?php
include 'database.php';

header("Content-Type: application/json");

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

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($gender) || empty($dob) || empty($role) || empty($address)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    // Check if email already exists
    $sql_check = "SELECT email FROM users WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check === false) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email already exists."]);
        exit();
    }

    // Handle registration based on role
    if ($role === "doctor") {
        if (empty($specialization)) {
            echo json_encode(["success" => false, "message" => "Specialization is required for doctors."]);
            exit();
        }

        $sql = "INSERT INTO users (name, email, password, phone_number, gender, date_of_birth, address, role, specialization)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
            exit();
        }
        $stmt->bind_param("sssssssss", $name, $email, $password, $phone, $gender, $dob, $address, $role, $specialization);
    } else {
        $sql = "INSERT INTO users (name, email, password, phone_number, gender, date_of_birth, address, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
            exit();
        }
        $stmt->bind_param("ssssssss", $name, $email, $password, $phone, $gender, $dob, $address, $role);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful."]);
    } else {
        echo json_encode(["success" => false, "message" => "Registration failed: " . $stmt->error]);
    }

    $stmt->close();
    $stmt_check->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit();
}
?>