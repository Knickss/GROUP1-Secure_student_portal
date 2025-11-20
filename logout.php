<?php
session_start();

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
        time() - 42000, // expired cookie time
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session data on server
session_destroy();

// Fully prevent accidental caching of protected pages
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page
header("Location: LoginPage/login.php");
exit;
?>
