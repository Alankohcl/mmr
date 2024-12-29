<?php
include_once 'database.php';
session_start();

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT user_id, role, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        //if(password_verify($password, $user['password'])){
        if($password === $user['password']){
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                echo "Redirecting to admin dashboard.";
                header("Location: admin_dashboard.php");
                exit;
            } elseif ($user['role'] === 'doctor') {
                header("Location: doctor_dashboard.html");
                exit;
            } elseif ($user['role'] === 'lab_assistant') {
                header("Location: lab_assistant_dashboard.html");
                exit;
            } else if ($user['role'] === 'patient') {
                header("Location: patient_dashboard.php");
                exit;
            } else if ($user['role'] === 'nurse'){
                header("Location: nurse.html");
                exit;
            }else {
                header("Location: login.php?error=Invalid role");
            }
        } else {
            header("Location: login.php?error=Incorrect password");
            exit;
        }
        $stmt->close();
        $conn->close();
    
    } else {
        header("Location: login.php?error=User not found");
        exit;
        
    }
}
?>