<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("
  SELECT 
    user_id,
    full_name,
    email,
    student_id,
    program,
    year_level,
    section,
    profile_pic,
    password
  FROM users
  WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
  die("User not found.");
}

// Normalize values to avoid null issues
$full_name   = $user['full_name']   ?? '';
$email       = $user['email']       ?? '';
$student_id  = $user['student_id']  ?? '';
$program     = $user['program']     ?? '';
$year_level  = $user['year_level']  ?? '';
$section     = $user['section']     ?? '';
$profile_pic = $user['profile_pic'] ?? '';

// ===== HANDLE PROFILE UPDATE (with optional image upload) =====
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_profile"])) {
  $name  = trim($_POST["full_name"]);
  $email = trim($_POST["email"]);
  $image_name = $profile_pic; // keep existing by default

  // Handle image upload if provided
  if (!empty($_FILES['profile_image']['name'])) {
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0775, true);
    }

    $file_name    = basename($_FILES["profile_image"]["name"]);
    $target_file  = $target_dir . time() . "_" . $file_name;
    $imageType    = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png'];

    if (in_array($imageType, $allowed_types)) {
      if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
        $image_name = basename($target_file);
      }
    }
  }

  $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, profile_pic = ? WHERE user_id = ?");
  $stmt->bind_param("sssi", $name, $email, $image_name, $user_id);
  $stmt->execute();
  $stmt->close();

  $_SESSION['full_name'] = $name;

  header("Location: profile_st.php?updated=1");
  exit;
}

// ===== HANDLE PASSWORD CHANGE =====
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["change_password"])) {
  $current = $_POST["current_password"] ?? '';
  $new     = $_POST["new_password"] ?? '';
  $confirm = $_POST["confirm_password"] ?? '';

  // Get current hash
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
    $new_hashed = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_hashed, $user_id);
    $stmt->execute();
    $stmt->close();
    $success = "Password updated successfully.";
  }
}

// Avatar path
$avatar = !empty($profile_pic)
  ? '../uploads/' . htmlspecialchars($profile_pic)
  : 'images/blank_pic.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Student Profile</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="portal-layout">
    <?php include('sidebar_st.php'); ?>

    <main class="main-content">
      <header class="topbar">
        <div class="search-container">
          <input type="text" placeholder="Search..." class="search-bar">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>
        <div class="profile-section">
          <img src="<?php echo $avatar; ?>" alt="User Avatar" class="avatar">
          <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
        </div>
      </header>

      <section class="dashboard-body">
        <section class="profile-wrapper">
          <div class="profile-banner">
            <img src="<?php echo $avatar; ?>" alt="Profile" class="profile-avatar">
            <div class="profile-basic">
              <h2><?php echo htmlspecialchars($full_name); ?></h2>
              <p>
                <?php echo htmlspecialchars($program ?: ''); ?>
                <?php if ($year_level || $section): ?>
                  | <?php echo htmlspecialchars($year_level ?: ''); ?>
                  <?php if ($section): ?>, <?php echo htmlspecialchars($section); ?><?php endif; ?>
                <?php endif; ?>
              </p>
            </div>
          </div>

          <div class="profile-details">
            <div class="profile-column">
              <h3><i class="fa-solid fa-user"></i> Personal Info</h3>
              <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?></p>
              <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            </div>
            <div class="profile-column">
              <h3><i class="fa-solid fa-graduation-cap"></i> Academic Info</h3>
              <p><strong>Program:</strong> <?php echo htmlspecialchars($program); ?></p>
              <p><strong>Year Level:</strong> <?php echo htmlspecialchars($year_level); ?></p>
              <p><strong>Section:</strong> <?php echo htmlspecialchars($section); ?></p>
            </div>
          </div>

          <div class="profile-actions">
  <button class="edit-btn" onclick="openEditProfile()">Edit Profile</button>
  <button class="change-btn" onclick="openChangePassword()">Change Password</button>
</div>

<?php if (!empty($error)): ?>
  <div class="profile-message error"><?php echo htmlspecialchars($error); ?></div>
<?php elseif (!empty($success)): ?>
  <div class="profile-message success"><?php echo htmlspecialchars($success); ?></div>
<?php elseif (isset($_GET['updated'])): ?>
  <div class="profile-message success">Profile updated successfully.</div>
<?php endif; ?>


        </section>
      </section>
    </main>
  </div>

  <!-- ===== EDIT PROFILE MODAL ===== -->
  <div id="editProfileModal" class="modal">
    <div class="logout-modal">
      <i class="fa-solid fa-user-pen logout-icon"></i>
      <h3>Edit Profile</h3>
      <form class="modal-form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_profile" value="1">

        <div class="form-group">
          <label for="edit-name">Full Name</label>
          <input type="text" id="edit-name" name="full_name"
                 value="<?php echo htmlspecialchars($full_name); ?>" required>
        </div>

        <div class="form-group">
          <label for="edit-email">Email</label>
          <input type="email" id="edit-email" name="email"
                 value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="form-group">
          <label for="profile-pic">Profile Picture</label>
          <input type="file" id="profile-pic" name="profile_image" accept="image/*">
        </div>



        <div class="modal-buttons">
          <button type="submit" class="confirm-btn">Save Changes</button>
          <button type="button" class="cancel-btn" onclick="closeEditProfile()">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== CHANGE PASSWORD MODAL ===== -->
  <div id="changePasswordModal" class="modal">
    <div class="logout-modal">
      <i class="fa-solid fa-lock logout-icon"></i>
      <h3>Change Password</h3>
      <form class="modal-form" method="POST">
        <input type="hidden" name="change_password" value="1">

        <div class="form-group">
          <label for="current-password">Current Password</label>
          <input type="password" id="current-password" name="current_password" required>
        </div>

        <div class="form-group">
          <label for="new-password">New Password</label>
          <input type="password" id="new-password" name="new_password" required>
        </div>

        <div class="form-group">
          <label for="confirm-password">Confirm New Password</label>
          <input type="password" id="confirm-password" name="confirm_password" required>
        </div>

        <div class="modal-buttons">
          <button type="submit" class="confirm-btn">Update Password</button>
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
