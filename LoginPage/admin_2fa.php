<?php
session_start();
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/mail_otp.php";

// Must be logged in as admin to use this page
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = "";
$info  = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ---------- VERIFY OTP ----------
    if (isset($_POST["verify_otp"])) {
        $entered = trim($_POST["otp"] ?? "");

        if ($entered === "") {
            $error = "Please enter the code.";
        } elseif (!isset($_SESSION['otp_code'], $_SESSION['otp_expires'])) {
            $error = "No active code. Please request a new one.";
        } elseif (time() > (int)$_SESSION['otp_expires']) {
            $error = "Code has expired. Please request a new one.";
        } elseif ($entered !== $_SESSION['otp_code']) {
            $error = "Invalid code. Please try again.";
        } else {
            // Success: mark 2FA as passed and go to dashboard
            $_SESSION['2fa_passed'] = true;
            unset($_SESSION['otp_code'], $_SESSION['otp_expires']);

            header("Location: ../AdminView/dashboard_admin.php");
            exit;
        }
    }

    // ---------- RESEND OTP ----------
    if (isset($_POST["resend_otp"])) {
        $otp = (string)rand(100000, 999999);
        $_SESSION['otp_code']    = $otp;
        $_SESSION['otp_expires'] = time() + 300;

        // Fetch admin email + name from DB
        $stmt = $conn->prepare("SELECT email, full_name FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($email, $full_name);
        $stmt->fetch();
        $stmt->close();

        $safeEmail = $email ?: "";
        $safeName  = $full_name ?: "Administrator";

        send_otp_email($safeEmail, $safeName, $otp);
        $info = "A new code has been sent.";
    }

    // ---------- CANCEL LOGIN ----------
    if (isset($_POST["cancel_login"])) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Escolink Centra | Admin Verification</title>
    <link rel="stylesheet" href="format/css.css">
</head>
<body class="login-body">

  <div class="center-wrapper">
    <div class="content">

      <h1 class="title">Admin Verification</h1>

      <div class="login-box" style="margin-top: 20px;">

        <?php if (!empty($error)): ?>
          <div class="error" style="margin-bottom:10px;"><?= htmlspecialchars($error) ?></div>
        <?php elseif (!empty($info)): ?>
          <div class="info" style="margin-bottom:10px;color:green;"><?= htmlspecialchars($info) ?></div>
        <?php endif; ?>

        <!-- VERIFY FORM -->
        <form method="POST" autocomplete="off">
          <input 
            type="text" 
            name="otp" 
            placeholder="Enter 6-digit code" 
            maxlength="6" 
            autocomplete="off"
            required
          >
          <button type="submit" name="verify_otp" class="login-btn" style="margin-top:10px;">
            Verify Code
          </button>
        </form>

        <!-- RESEND FORM (separate so it doesn't require OTP field) -->
        <form method="POST" style="margin-top:10px;">
          <button type="submit" name="resend_otp" class="login-btn" style="background-color:#777;">
            Resend Code
          </button>
        </form>

        <!-- CANCEL LOGIN -->
        <form method="POST" style="margin-top:10px;">
          <button type="submit" name="cancel_login" class="login-btn" style="background-color:#444;">
            Cancel Login
          </button>
        </form>

      </div>
    </div>
  </div>

</body>
</html>
