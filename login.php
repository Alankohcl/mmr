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
                header("Location: doctor_dashboard.php");
                exit;
            } elseif ($user['role'] === 'lab_assistant') {
                header("Location: lab_assistant_dashboard.php");
                exit;
            } else if ($user['role'] === 'patient') {
                header("Location: patient_dashboard.php");
                exit;
            } else if ($user['role'] === 'nurse'){
                header("Location: nurse.php");
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

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include 'header.php' ?>
        <?php include 'footer.php' ?>
        
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>
        <br>
        <h2>Admin Login</h2>
        <button onclick="window.location.href='admin_login.php'">Admin Login</button>
    </body>
</html>