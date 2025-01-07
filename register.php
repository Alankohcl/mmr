<?php
include 'database.php';

header("Content-Type: application/json");

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $role = $_POST['role'];
    $specialization = $_POST['specialization'];
    $address = $_POST['address'];

    if (!$name || !$email || !$password || !$phone || !$gender || !$dob || !$role || !$address) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    //check if email exist
    $sql_check = "SELECT email FROM users WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check === false) { 
        // die("Prepare failed: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if($result_check->num_rows > 0){
        header("Location: register.html?error=Email already exist");
        echo json_encode(["success" => false, "message" => "Specialization is required for doctors."]);
        exit();
    }

    //handle registration based on role
    if($role === "doctor"){
        $sql = "INSERT INTO users (name, email, password, phone_number, gender, date_of_birth, address, role, specialization)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if($stmt === false){
            echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
            exit();
        }
        $stmt->bind_param("sssssssss", $name, $email, $password, $phone, $gender, $dob, $address, $role, $specialization);
    } else {
        // register users
        $sql = "INSERT INTO users (name, email, password, phone_number, gender, date_of_birth, address, role)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) { 
            //die("Prepare failed: " . $conn->error);
            echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
            exit();
        }
        $stmt->bind_param("sssssssss", $name, $email, $password, $phone, $gender, $dob, $address, $role);
    }
    
    if($stmt->execute()){
        echo json_encode(["success" => true, "message" => "Registration successful."]);
        header("Location: login.php?success=1");
    }else{
        header("Location: register.html?error=Registration failed");
        echo json_encode(["success" => false, "message" => "Registration failed."]);
    }

    $stmt->close();
    $stmt_check->close();
    $conn->close();
}else{
    header("Location: register.html");
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit();
}

?>