<?php
session_start();
include("../config/db_connect.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST["username"]);
  $password = trim($_POST["password"]);

  if (empty($username) || empty($password)) {
    $error = "Please enter both username and password.";
  } else {
    $stmt = $conn->prepare("SELECT user_id, username, password, role, full_name FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $row = $result->fetch_assoc();

      if (password_verify($password, $row['password'])) {
        // Save session variables
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['full_name'] = $row['full_name'];

        // Redirect based on user role
        switch ($row['role']) {
          case 'student':
            header("Location: ../StudentView/dashboard_st.php");
            exit;

          case 'teacher':
            header("Location: ../ProfView/dashboard_prof.php");
            exit;

          case 'admin':
            header("Location: ../AdminView/dashboard_admin.php");
            exit;

          default:
            $error = "Invalid user role.";
            break;
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
        <form method="POST" action="">
          <input type="text" name="username" placeholder="Username" required>
          <input type="password" name="password" placeholder="Password" required>
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
