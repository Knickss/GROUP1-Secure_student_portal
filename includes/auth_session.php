<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
  header("Location: ../LoginPage/login.php");
  exit;
}

// Identify which folder (view) the user is trying to access
$currentPath = $_SERVER['PHP_SELF'];
$role = $_SESSION['role'];

// Redirect users if they try to access a view not meant for their role
if (strpos($currentPath, 'StudentView') !== false && $role !== 'student') {
  header("Location: ../LoginPage/login.php");
  exit;
}

if (strpos($currentPath, 'ProfView') !== false && $role !== 'teacher') {
  header("Location: ../LoginPage/login.php");
  exit;
}

if (strpos($currentPath, 'AdminView') !== false && $role !== 'admin') {
  header("Location: ../LoginPage/login.php");
  exit;
}
?>
