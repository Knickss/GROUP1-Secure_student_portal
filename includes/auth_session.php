<?php
session_start();

// Must be logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../LoginPage/login.php");
    exit;
}

$role = $_SESSION['role'] ?? '';
$currentPath = $_SERVER['PHP_SELF'];

// ============ ADMIN 2FA PROTECTION ============
if ($role === 'admin') {
    // Prevent infinite loop: allow admin_2fa.php to load without redirect
    $currentFile = basename($_SERVER['PHP_SELF']);

    if ($currentFile !== 'admin_2fa.php') {
        if (!isset($_SESSION['2fa_passed']) || $_SESSION['2fa_passed'] !== true) {
            header("Location: ../LoginPage/admin_2fa.php");
            exit;
        }
    }
}

// ============ ROLE-BASED VIEW PROTECTION ============

// Student trying to enter other folder
if (strpos($currentPath, 'StudentView') !== false && $role !== 'student') {
    header("Location: ../LoginPage/login.php");
    exit;
}

// Teacher trying to enter other folder
if (strpos($currentPath, 'ProfView') !== false && $role !== 'teacher') {
    header("Location: ../LoginPage/login.php");
    exit;
}

// Admin trying to enter student/prof views
if (strpos($currentPath, 'AdminView') !== false && $role !== 'admin') {
    header("Location: ../LoginPage/login.php");
    exit;
}
?>
