<?php
session_start();
include("../config/db_connect.php");
require_once("../includes/mail_otp.php"); // for admin OTP emails

$error = "";

// Prevent autofill on reload
$username_input = "";
$password_input = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Keep blank values so browser doesn't autofill
    $username_input = "";
    $password_input = "";

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("
            SELECT user_id, username, password, role, full_name, email
            FROM users
            WHERE username = ?
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                // Save base session
                $_SESSION['user_id']   = $row['user_id'];
                $_SESSION['username']  = $row['username'];
                $_SESSION['role']      = $row['role'];
                $_SESSION['full_name'] = $row['full_name'];

                // Reset 2FA for every login
                unset($_SESSION['otp_code'], $_SESSION['otp_expires'], $_SESSION['2fa_passed']);

                if ($row['role'] === 'admin') {
                    // ========== ADMIN 2FA WORKFLOW ==========
                    $otp = (string)rand(100000, 999999);

                    $_SESSION['otp_code']    = $otp;
                    $_SESSION['otp_expires'] = time() + 300; // 5 minutes
                    $_SESSION['2fa_passed']  = false;

                    // Send OTP email
                    send_otp_email($row['email'], $row['full_name'], $otp);

                    header("Location: admin_2fa.php");
                    exit;
                }

                // ========== Normal Roles ==========
                switch ($row['role']) {
                    case 'student':
                        header("Location: ../StudentView/dashboard_st.php");
                        exit;

                    case 'teacher':
                        header("Location: ../ProfView/dashboard_prof.php");
                        exit;

                    case 'admin':
                        // admin 2FA handled above
                        break;

                    default:
                        $error = "Invalid user role.";
                }

            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra - Login</title>
  <link rel="stylesheet" href="format/css.css">
</head>
<body>
  <div class="center-wrapper">
    <div class="content">
      <h1 class="title">Escolink Centra</h1>
      <img src="LoginLogo.png" alt="CEU Logo" class="logo">

      <div class="login-box">
        <form method="POST" action="" autocomplete="off">
          
          <input 
            type="text" 
            name="username" 
            placeholder="Username" 
            value="" 
            autocomplete="off"
            required
          >

          <input 
            type="password" 
            name="password" 
            placeholder="Password"
            value=""
            autocomplete="new-password"
            required
          >

          <button type="submit" class="login-btn">Login</button>

          <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>
          
        </form>
      </div>
    </div>
  </div>
</body>
</html>
