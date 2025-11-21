<?php
session_start();

require_once("config/db_connect.php");     // <-- FIXED
require_once("includes/logging.php");      // <-- FIXED

// Capture user ID BEFORE destroying session
$userId = $_SESSION['user_id'] ?? null;

// Log the logout event
if (!empty($userId)) {
    log_activity(
        $conn,
        (int)$userId,
        "Logged Out",
        "User logged out of the system.",
        "success"
    );
}

// Remove sensitive 2FA session data
unset($_SESSION['otp_code'], $_SESSION['otp_expires'], $_SESSION['2fa_passed']);

// Clear all session data
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy server session
session_destroy();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page
header("Location: LoginPage/login.php");
exit;
?>
