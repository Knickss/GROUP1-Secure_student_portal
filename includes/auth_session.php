<?php
session_start();

// ---------------------------------------------------------
// SESSION SECURITY FEATURES
// ---------------------------------------------------------

// 1. SESSION FIXATION PROTECTION (important)
if (!isset($_SESSION['session_regenerated'])) {
    session_regenerate_id(true);
    $_SESSION['session_regenerated'] = true;
}

// 2. SESSION TIMEOUT (30 minutes)
$timeout_duration = 1800; // 1800 seconds = 30 minutes

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../LoginPage/login.php?timeout=1");
    exit;
}

$_SESSION['last_activity'] = time();


// ---------------------------------------------------------
// BASIC SESSION VALIDATION
// ---------------------------------------------------------
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../LoginPage/login.php");
    exit;
}

$role = $_SESSION['role'] ?? '';
$currentPath = $_SERVER['PHP_SELF'];


// ---------------------------------------------------------
// ADMIN 2FA ENFORCEMENT
// ---------------------------------------------------------
if ($role === 'admin') {
    $currentFile = basename($currentPath);

    // Allow admin_2fa.php access WITHOUT 2FA being passed
    if ($currentFile !== "admin_2fa.php") {
        if (!isset($_SESSION['2fa_passed']) || $_SESSION['2fa_passed'] !== true) {
            header("Location: ../LoginPage/admin_2fa.php");
            exit;
        }
    }
}


// ---------------------------------------------------------
// ROLE-BASED ACCESS CONTROL
// ---------------------------------------------------------

// Student trying to access something outside StudentView
if (strpos($currentPath, 'StudentView') !== false && $role !== 'student') {
    header("Location: ../LoginPage/login.php");
    exit;
}

// Teacher trying to access outside ProfView
if (strpos($currentPath, 'ProfView') !== false && $role !== 'teacher') {
    header("Location: ../LoginPage/login.php");
    exit;
}

// Admin trying to access outside AdminView
if (strpos($currentPath, 'AdminView') !== false && $role !== 'admin') {
    header("Location: ../LoginPage/login.php");
    exit;
}
?>
