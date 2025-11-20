<?php
session_start();
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/mail_otp.php";

// Redirect if no OTP session exists
if (!isset($_SESSION['pending_2fa']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// --- Handle OTP Submission ---
$info = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["verify_otp"])) {
    $entered = trim($_POST["otp_code"] ?? "");

    if (empty($entered)) {
        $error = "Please enter the code.";
    } elseif (!isset($_SESSION['otp_code']) || !isset($_SESSION['otp_expires'])) {
        $error = "No verification code found.";
    } elseif (time() > $_SESSION['otp_expires']) {
        $error = "The code has expired. Please resend a new one.";
    } elseif ($entered !== $_SESSION['otp_code']) {
        $error = "Incorrect code. Try again.";
    } else {
        // OTP success → complete login
        unset($_SESSION['pending_2fa']);
        unset($_SESSION['otp_code']);
        unset($_SESSION['otp_expires']);

        header("Location: ../AdminView/dashboard_admin.php");
        exit;
    }
}

// --- Handle Resend ---
if (isset($_POST['resend_otp'])) {

    $otp = (string)rand(100000, 999999);
    $_SESSION['otp_code']    = $otp;
    $_SESSION['otp_expires'] = time() + 300;

    // Fetch admin email
    $stmt = $conn->prepare("SELECT email, full_name FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($email, $full_name);
    $stmt->fetch();
    $stmt->close();

    // Safe fallbacks
    $safeEmail = $email ?: "";
    $safeName  = $full_name ?: "Administrator";

    send_otp_email($safeEmail, $safeName, $otp);
    $info = "A new verification code has been sent.";
}

// --- Cancel Login ---
if (isset($_POST["cancel_login"])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Verification</title>
    <link rel="stylesheet" href="format/css.css">
</head>

<body class="login-body">

<div class="center-wrapper">
    <div class="content">

        <h1 class="title">Admin Verification</h1>

        <div class="login-box" style="margin-top:20px;">

            <!-- OTP FORM -->
            <form method="POST">
                <input type="hidden" name="verify_otp" value="1">

                <input 
                    type="text" 
                    name="otp_code" 
                    placeholder="Enter 6-digit code"
                    maxlength="6"
                    required
                >

                <button type="submit" class="login-btn" style="margin-top:10px;">
                    Verify Code
                </button>
            </form>

            <!-- RESEND BUTTON (separate form!) -->
            <form method="POST" style="margin-top:10px;">
                <input type="hidden" name="resend_otp" value="1">
                <button type="submit" class="login-btn" style="background:#777;">
                    Resend Code
                </button>
            </form>

            <!-- CANCEL LOGIN -->
            <form method="POST" style="margin-top:10px;">
                <input type="hidden" name="cancel_login" value="1">
                <button type="submit" class="login-btn" style="background:#444;">
                    Cancel Login
                </button>
            </form>

            <!-- Status messages -->
            <?php if (!empty($error)): ?>
                <div class="error" style="margin-top:10px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($info)): ?>
                <div class="info" style="margin-top:10px;color:green;"><?= htmlspecialchars($info) ?></div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>
