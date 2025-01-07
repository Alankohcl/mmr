<?php
//set ehader for JSON response
//header("Content-Type: application/json");

//database credentials
$host = "localhost";
$database = "mmr_app";
$username = "root";
$password = "";

$conn = mysqli_connect($host, $username, $password, $database);

// if(!$conn){
//     echo json_encode(["status"=>"error", "message"=>"Database connection failed(1)" . mysqli_connect_error()]);
//     die("Connection Failed". mysqli_connect_error());
    
// }
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}
?>