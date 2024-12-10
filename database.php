<?php
$host = "localhost";
$database = "mmr_app";
$username = "root";
$password = "";

$conn = mysqli_connect($host, $database, $username, $password);

if(!$conn){
    die("Connection Failed".mysqli_connect_error());
}

?>