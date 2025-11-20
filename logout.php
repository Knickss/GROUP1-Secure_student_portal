<?php
session_start();

// Explicitly remove all sensitive 2FA session data
unset($_SESSION['otp_code']);
unset($_SESSION['otp_expires']);
unset($_SESSION['2fa_passed']);

// Remove all session data
session_unset();

// Destroy the session fully
session_destroy();

// Redirect to login page
header("Location: LoginPage/login.php");
exit;
?>
