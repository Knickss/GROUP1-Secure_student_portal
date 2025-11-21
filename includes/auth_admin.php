<?php
if (!isset($_SESSION)) { session_start(); }

// Block access if not logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../LoginPage/login.php");
    exit;
}

// Block access if role is NOT admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../LoginPage/login.php");
    exit;
}
?>
