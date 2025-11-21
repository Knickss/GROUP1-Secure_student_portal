<?php
if (!isset($_SESSION)) { session_start(); }

// Block if not logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../LoginPage/login.php");
    exit;
}

// Only allow teacher role
if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../LoginPage/login.php");
    exit;
}
?>
