<?php
session_start();

if (!isset($_SESSION['role'])) {
    die("Session role not set.");
}

if ($_SESSION['role'] !== 'patient') {
    die("Unauthorized access. Role: " . $_SESSION['role']);
}
?>