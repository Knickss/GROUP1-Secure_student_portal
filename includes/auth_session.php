<?php
// ======================================================
// SECURITY HEADERS (must be FIRST before any output)
// ======================================================
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header_remove("X-Powered-By");

// ======================================================
// Secure session cookie flags (must come BEFORE session_start)
// ======================================================
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current path/file
$currentPath = $_SERVER['PHP_SELF'] ?? '';
$currentFile = basename($currentPath);

// ======================================================
// 1. BASIC SESSION VALIDATION
// ======================================================
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // User NOT logged in → redirect to login
    header("Location: ../LoginPage/login.php");
    exit;
}

$role = $_SESSION['role'];

// ======================================================
// 2. ADMIN 2FA ENFORCEMENT
// ======================================================
if ($role === 'admin' && isset($_SESSION['user_id'])) {

    // If user is accessing admin pages but has not passed 2FA
    if ($currentFile !== "admin_2fa.php") {

        if (!isset($_SESSION['2fa_passed']) || $_SESSION['2fa_passed'] !== true) {

            // Redirect admin to 2FA page
            header("Location: ../LoginPage/admin_2fa.php");
            exit;
        }
    }
}

// ======================================================
// 3. Prevent cached pages from appearing after logout
// ======================================================
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
