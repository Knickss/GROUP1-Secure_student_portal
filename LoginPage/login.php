<?php
// ======================================================
// SECURITY HEADERS (must be first)
// ======================================================
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Content-Security-Policy: default-src 'self' 'unsafe-inline' data:;");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header_remove("X-Powered-By");

// -------------------------------------------
// SECURE COOKIE SETTINGS — AUTO-DETECT HTTPS
// MUST COME BEFORE session_start()
// -------------------------------------------
$isHTTPS = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || $_SERVER['SERVER_PORT'] == 443;

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isHTTPS,  // automatically TRUE on HTTPS, FALSE on localhost
    'httponly' => true,    // ALWAYS TRUE for security
    'samesite' => 'Strict'
]);

session_start();

require_once("../config/db_connect.php");
require_once("../includes/security.php");
require_once("../includes/mail_otp.php");
require_once("../includes/logging.php"); // <-- ADDED

$error = "";

// LIGHT BRUTE-FORCE PROTECTION
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SESSION['login_attempts'] >= 8) {
    $error = "Too many failed attempts. Please wait 1 minute.";
    if (!isset($_SESSION['lockout_time'])) {
        $_SESSION['lockout_time'] = time();
    }

    if (time() - $_SESSION['lockout_time'] < 60) {
        // still locked out — also log
        log_activity($conn, null, "Login Locked", "Account temporarily locked due to too many attempts.", "failed");
    } 
    else {
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['lockout_time']);
    }
}


// ===========================================
// PROCESS LOGIN
// ===========================================

if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($error)) {

    $username = clean($_POST["username"] ?? "");
    $password = clean($_POST["password"] ?? "");

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

        // ===========================
        // USER FOUND
        // ===========================
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                // SESSION FIXATION PROTECTION
                session_regenerate_id(true);

                // Set session
                $_SESSION['user_id']   = $row['user_id'];
                $_SESSION['username']  = $row['username'];
                $_SESSION['role']      = $row['role'];
                $_SESSION['full_name'] = $row['full_name'];

                // Reset attempts
                $_SESSION['login_attempts'] = 0;

                // Log SUCCESSFUL LOGIN
                log_activity(
                    $conn,
                    (int)$row['user_id'],
                    "Logged In",
                    "User successfully logged in.",
                    "success"
                );

                // Reset 2FA values
                unset($_SESSION['otp_code'], $_SESSION['otp_expires'], $_SESSION['2fa_passed']);

                // ===========================
                // ADMIN → 2FA
                // ===========================
                if ($row['role'] === 'admin') {

                    $otp = (string)rand(100000, 999999);

                    $_SESSION['otp_code']    = $otp;
                    $_SESSION['otp_expires'] = time() + 300;
                    $_SESSION['2fa_passed']  = false;

                    send_otp_email($row['email'], $row['full_name'], $otp);

                    // Log 2FA initiated
                    log_activity(
                        $conn,
                        (int)$row['user_id'],
                        "Admin 2FA Initiated",
                        "OTP sent to admin email.",
                        "success"
                    );

                    header("Location: admin_2fa.php");
                    exit;
                }

                // ===========================
                // NORMAL ROLES
                // ===========================
                switch ($row['role']) {
                    case 'student':
                        header("Location: ../StudentView/dashboard_st.php");
                        exit;

                    case 'teacher':
                        header("Location: ../ProfView/dashboard_prof.php");
                        exit;

                    default:
                        $error = "Invalid user role.";
                }

            } else {
                // WRONG PASSWORD
                $_SESSION['login_attempts']++;
                $error = "Incorrect password.";

                // Log FAILED LOGIN (wrong password)
                log_activity(
                    $conn,
                    (int)$row['user_id'],
                    "Login Failed",
                    "Wrong password entered.",
                    "failed"
                );
            }

        } else {
            // USER NOT FOUND
            $_SESSION['login_attempts']++;
            $error = "User not found.";

            // Log FAILED LOGIN (username doesn't exist)
            log_activity(
                $conn,
                null,
                "Login Failed",
                "Unknown username entered: '{$username}'",
                "failed"
            );
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
        <form method="POST" autocomplete="off">

          <input 
            type="text" 
            name="username" 
            placeholder="Username"
            autocomplete="off" 
            required
          >

          <input 
            type="password" 
            name="password" 
            placeholder="Password"
            autocomplete="new-password" 
            required
          >

          <button type="submit" class="login-btn">Login</button>

          <?php if (!empty($error)): ?>
            <div class="error"><?= e($error) ?></div>
          <?php endif; ?>

        </form>
      </div>
    </div>
  </div>
</body>
</html>
