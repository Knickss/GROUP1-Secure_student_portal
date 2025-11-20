<?php
session_start();
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../includes/mail_otp.php";

// Must be logged in + must be admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = "";
$info  = "";

// Handle POST submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // VERIFY OTP
    if (isset($_POST['verify_otp'])) {
        $input_otp = trim($_POST['otp'] ?? '');

        if (empty($input_otp)) {
            $error = "Please enter the code.";
        } 
        elseif (!isset($_SESSION['otp_code'], $_SESSION['otp_expires'])) {
            $error = "No active code. Please request a new one.";
        } 
        elseif (time() > (int)$_SESSION['otp_expires']) {
            $error = "Code expired. Request a new one.";
        } 
        elseif ($input_otp !== $_SESSION['otp_code']) {
            $error = "Invalid code.";
        }
        else {
            // SUCCESS
            $_SESSION['2fa_passed'] = true;

            unset($_SESSION['otp_code'], $_SESSION['otp_expires']);

            header("Location: ../AdminView/dashboard_admin.php");
            exit;
        }
    }

    // RESEND OTP
    if (isset($_POST['resend_otp'])) {

        $otp = (string)rand(100000, 999999);
        $_SESSION['otp_code']    = $otp;
        $_SESSION['otp_expires'] = time() + 300;

        // Fetch admin email (always safe)
        $stmt = $conn->prepare("SELECT email, full_name FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($email, $full_name);
        $stmt->fetch();
        $stmt->close();

        send_otp_email($email, $full_name, $otp);
        $info = "A new code has been sent.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Admin Verification</title>
  <link rel="stylesheet" href="format/css.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="login-body">

  <div class="center-wrapper">
    <div class="content">

      <h1 class="title">Admin Verification</h1>

      <div class="login-box">

        <?php if (!empty($error)): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php elseif (!empty($info)): ?>
          <div class="success"><?= htmlspecialchars($info) ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">

          <input 
            type="text" 
            name="otp" 
            placeholder="Enter 6-digit code" 
            maxlength="6" 
            autocomplete="off"
            required
          >

          <button type="submit" name="verify_otp" class="login-btn">Verify</button>

          <button type="submit" name="resend_otp" class="login-btn" 
            style="background-color:#888;margin-top:10px;">
            Resend Code
          </button>

        </form>

        <form method="POST" action="logout.php" style="margin-top: 15px;">
          <button type="submit" class="login-btn" style="background-color:#555;">
            Cancel Login
          </button>
        </form>

      </div>
    </div>
  </div>

</body>
</html>
