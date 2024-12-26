<?php
include 'database.php';

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

    $sql_check = "SELECT email FROM users WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    if ($stmt_check === false) { 
        die("Prepare failed: " . $conn->error);
    }
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if($result_check->num_rows > 0){
        header("Location: register.html?error=Email already exist");
        exit;
    }

    $sql = "INSERT INTO users (name, email, password, phone_number, gender, date_of_birth, address, role, specialization)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) { 
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssssssss", $name, $email, $password, $phone, $gender, $dob, $address, $role, $specialization);

    if($stmt->execute()){
        header("Location: register.html?success=1");
    }else{
        header("Location: register.html?error=Registration failed");
    }

    $stmt->close();
    $stmt_check->close();
    $conn->close();
}else{
    header("Location: register.html");
    exit;
}

?>