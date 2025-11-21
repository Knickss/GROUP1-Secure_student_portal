<?php
include("../includes/auth_session.php");
include("../includes/auth_teacher.php");
include("../config/db_connect.php");

$user_id = $_SESSION['user_id'];

// ===== FETCH PROFESSOR INFO =====
$stmt = $conn->prepare("
  SELECT 
    u.user_id,
    u.full_name,
    u.email,
    u.teacher_id,
    u.profile_pic,
    u.password,
    d.department_name
  FROM users u
  LEFT JOIN departments d ON u.department_id = d.department_id
  WHERE u.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
  die("User not found.");
}

$full_name  = $user['full_name']        ?? '';
$email      = $user['email']            ?? '';
$teacher_id = $user['teacher_id']       ?? '';
$department = $user['department_name']  ?? '';
$profile_pic= $user['profile_pic']      ?? '';
$error      = '';
$success    = '';

// ===== FETCH COURSES HANDLED BY PROFESSOR =====
$course_stmt = $conn->prepare("
  SELECT course_code
  FROM courses 
  WHERE teacher_id = ?
");
$course_stmt->bind_param("i", $user_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();

$courses = [];
while ($row = $course_result->fetch_assoc()) {
    $courses[] = $row['course_code'];
}
$course_list = $courses ? implode(', ', $courses) : "No courses assigned";

// ===== PROFILE UPDATE =====
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_profile"])) {
  $new_name  = trim($_POST["full_name"]);
  $new_email = trim($_POST["email"]);
  $new_pfp   = $profile_pic;

  if (!empty($_FILES['profile_image']['name'])) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0775, true);

    $filename = basename($_FILES["profile_image"]["name"]);
    $newpath  = $target_dir . time() . "_" . $filename;

    $ext = strtolower(pathinfo($newpath, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png'];

    if (in_array($ext, $allowed)) {
      if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $newpath)) {
        $new_pfp = basename($newpath);
      }
    }
  }

  $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, profile_pic=? WHERE user_id=?");
  $stmt->bind_param("sssi", $new_name, $new_email, $new_pfp, $user_id);
  $stmt->execute();
  $stmt->close();

  $_SESSION['full_name'] = $new_name;
  header("Location: profile_prof.php?updated=1");
  exit;
}

// ===== PASSWORD CHANGE =====
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["change_password"])) {
  $current = $_POST["current_password"] ?? '';
  $new     = $_POST["new_password"] ?? '';
  $confirm = $_POST["confirm_password"] ?? '';

  $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($hashed);
  $stmt->fetch();
  $stmt->close();

  if (!password_verify($current, $hashed)) {
    $error = "Current password is incorrect.";
  } elseif ($new !== $confirm) {
    $error = "New passwords do not match.";
  } elseif (strlen($new) < 6) {
    $error = "New password must be at least 6 characters.";
  } else {
    $new_hash = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
    $stmt->bind_param("si", $new_hash, $user_id);
    $stmt->execute();
    $stmt->close();
    $success = "Password updated successfully.";
  }
}

$avatar = (!empty($profile_pic))
  ? '../uploads/' . htmlspecialchars($profile_pic)
  : 'images/ProfileImg.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Professor Profile</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="portal-layout">

    <?php include('sidebar_prof.php'); ?>

    <main class="main-content">

      <!-- TOPBAR -->
      <header class="topbar">
        <div class="search-container">
          <input type="text" placeholder="Search..." class="search-bar">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <div class="profile-section">
          <img src="<?php echo $avatar; ?>" class="avatar">
          <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
        </div>
      </header>

      <section class="dashboard-body">
        <section class="profile-wrapper">

          <!-- BANNER -->
          <div class="profile-banner">
            <img src="<?php echo $avatar; ?>" class="profile-avatar">
            <div class="profile-basic">
              <h2><?php echo htmlspecialchars($full_name); ?></h2>
              <p>College of Information and Computing Studies</p>
            </div>
          </div>

          <!-- DETAILS -->
          <div class="profile-details">

            <div class="profile-column">
              <h3><i class="fa-solid fa-user"></i> Personal Info</h3>
              <p><strong>Teacher ID:</strong> <?php echo htmlspecialchars($teacher_id); ?></p>
              <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            </div>

            <div class="profile-column">
              <h3><i class="fa-solid fa-chalkboard-teacher"></i> Teaching Info</h3>
              <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>
              <p><strong>Courses:</strong> <?php echo htmlspecialchars($course_list); ?></p>
            </div>

          </div>

          <!-- ACTION BUTTONS -->
          <div class="profile-actions">
            <button class="edit-btn" onclick="openEditProfile()">Edit Profile</button>
            <button class="change-btn" onclick="openChangePassword()">Change Password</button>
          </div>

          <!-- STATUS MESSAGE (INSIDE CARD NOW) -->
          <?php if (!empty($error)): ?>
            <div class="profile-message error"><?php echo htmlspecialchars($error); ?></div>
          <?php elseif (!empty($success)): ?>
            <div class="profile-message success"><?php echo htmlspecialchars($success); ?></div>
          <?php elseif (isset($_GET['updated'])): ?>
            <div class="profile-message success">Profile updated successfully.</div>
          <?php endif; ?>

        </section> <!-- profile-wrapper NOW closes AFTER message -->
      </section>
    </main>
  </div>

  <!-- EDIT PROFILE MODAL -->
  <div id="editProfileModal" class="modal">
    <div class="modal-content profile-modal">
      <span class="close" onclick="closeEditProfile()">&times;</span>
      <div class="modal-header">
        <i class="fa-solid fa-user-pen"></i>
        <h2>Edit Profile</h2>
      </div>

      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_profile" value="1">

        <label>Full Name</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label>Profile Picture</label>
        <input type="file" name="profile_image" accept="image/*">

        <div class="button-group">
          <button class="save-btn" type="submit">Save Changes</button>
          <button type="button" class="cancel-btn" onclick="closeEditProfile()">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- CHANGE PASSWORD MODAL -->
  <div id="changePasswordModal" class="modal">
    <div class="modal-content password-modal">
      <span class="close" onclick="closeChangePassword()">&times;</span>

      <div class="modal-header">
        <i class="fa-solid fa-lock"></i>
        <h2>Change Password</h2>
      </div>

      <form method="POST">
        <input type="hidden" name="change_password" value="1">

        <label>Current Password</label>
        <input type="password" name="current_password" required>

        <label>New Password</label>
        <input type="password" name="new_password" required>

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required>

        <div class="button-group">
          <button type="submit" class="save-btn">Update Password</button>
          <button type="button" class="cancel-btn" onclick="closeChangePassword()">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openEditProfile(){document.getElementById("editProfileModal").style.display="flex";}
    function openChangePassword(){document.getElementById("changePasswordModal").style.display="flex";}
    function closeEditProfile(){document.getElementById("editProfileModal").style.display="none";}
    function closeChangePassword(){document.getElementById("changePasswordModal").style.display="none";}

    window.addEventListener("click", (e) => {
      if (e.target === document.getElementById("editProfileModal")) closeEditProfile();
      if (e.target === document.getElementById("changePasswordModal")) closeChangePassword();
    });
  </script>
</body>
</html>
