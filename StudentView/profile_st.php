<?php
include("../includes/auth_session.php");
include("../includes/auth_student.php");
include("../config/db_connect.php");
include("../includes/logging.php");

// ================== PASSWORD RULE FUNCTION ==================
function validate_password_rule($password, &$errorMsg) {
    if (strlen($password) < 8) {
        $errorMsg = "New password must be at least 8 characters long.";
        return false;
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errorMsg = "New password must include at least one number.";
        return false;
    }
    if (!preg_match('/[\W_]/', $password)) {
        $errorMsg = "New password must include at least one special character.";
        return false;
    }
    return true;
}

$user_id = $_SESSION['user_id'];

// ===== FETCH STUDENT FULL INFO =====
$stmt = $conn->prepare("
  SELECT 
    u.user_id,
    u.full_name,
    u.email,
    u.profile_pic,
    u.about_me,
    s.student_id,
    s.program,
    s.year_level
  FROM users u
  LEFT JOIN student_info s ON u.user_id = s.user_id
  WHERE u.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user) die("User not found.");

$full_name   = $user['full_name']      ?? '';
$email       = $user['email']          ?? '';
$profile_pic = $user['profile_pic']    ?? '';
$about_me    = $user['about_me']       ?? '';
$student_id  = $user['student_id']     ?? '';
$program     = $user['program']        ?? '';
$year_level  = $user['year_level']     ?? '';

$error = '';
$success = '';


// ======================================================================
// UPDATE PROFILE (About Me + Profile Picture)
// ======================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_profile"])) {
    $new_about = trim($_POST["about_me"] ?? '');
    $new_pfp = $profile_pic;

    if (!empty($_FILES['profile_image']['name'])) {
        $dir = "../uploads/";
        if (!is_dir($dir)) mkdir($dir, 0775, true);

        $file = basename($_FILES["profile_image"]["name"]);
        $path = $dir . time() . "_" . $file;

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];

        if (in_array($ext, $allowed)) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $path)) {
                $new_pfp = basename($path);
            }
        }
    }

    $stmt = $conn->prepare("UPDATE users SET about_me = ?, profile_pic = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $new_about, $new_pfp, $user_id);
    $stmt->execute();
    $stmt->close();

    log_activity(
        $conn,
        (int)$user_id,
        "Updated Profile",
        "Student updated About Me + Profile Picture.",
        "success"
    );

    header("Location: profile_st.php?updated=1");
    exit;
}


// ======================================================================
// CHANGE PASSWORD (WITH STRONG RULE)
// ======================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["change_password"])) {
    
    $current = $_POST["current_password"] ?? '';
    $new     = $_POST["new_password"] ?? '';
    $confirm = $_POST["confirm_password"] ?? '';

    // Fetch current hashed password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current, $hashed)) {
        $error = "Current password is incorrect.";
    }
    elseif ($new !== $confirm) {
        $error = "New passwords do not match.";
    }
    else {
        $pwError = "";
        if (!validate_password_rule($new, $pwError)) {
            $error = $pwError;
        } else {
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
            $stmt->bind_param("si", $new_hash, $user_id);
            $stmt->execute();
            $stmt->close();

            $success = "Password updated successfully.";

            log_activity(
                $conn,
                (int)$user_id,
                "Changed Password",
                "Student changed password.",
                "success"
            );
        }
    }
}

$avatar = (!empty($profile_pic))
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
  <div></div>
  <div class="profile-section">
    <img src="<?php echo $avatar; ?>" class="avatar">
    <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
  </div>
</header>

<section class="dashboard-body">
<section class="profile-wrapper">

  <!-- SUCCESS / ERROR BANNER -->
  <?php if (!empty($error)): ?>
    <div class="profile-message error"><?php echo htmlspecialchars($error); ?></div>
  <?php elseif (!empty($success)): ?>
    <div class="profile-message success"><?php echo htmlspecialchars($success); ?></div>
  <?php elseif (isset($_GET['updated'])): ?>
    <div class="profile-message success">Profile updated successfully.</div>
  <?php endif; ?>


  <div class="profile-banner">
    <img src="<?php echo $avatar; ?>" class="profile-avatar">
    <div class="profile-basic">
      <h2><?php echo htmlspecialchars($full_name); ?></h2>
      <p><?php echo htmlspecialchars($program ?: ''); ?></p>
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
    </div>

    <div class="profile-column">
      <h3><i class="fa-solid fa-id-card"></i> About Me</h3>
      <p>
        <?php
          if (trim($about_me) === '') {
            echo "No description added yet.";
          } else {
            echo nl2br(htmlspecialchars($about_me));
          }
        ?>
      </p>
    </div>

  </div>

  <div class="profile-actions">
    <button class="edit-btn" onclick="openEditProfile()">Edit Profile</button>
    <button class="change-btn" onclick="openChangePassword()">Change Password</button>
  </div>

</section>
</section>
</main>
</div>

<!-- EDIT PROFILE MODAL -->
<div id="editProfileModal" class="modal">
  <div class="logout-modal">
    <i class="fa-solid fa-user-pen logout-icon"></i>
    <h3>Edit Profile</h3>

    <form class="modal-form" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="update_profile" value="1">

      <div class="form-group">
        <label>Full Name</label>
        <input type="text" value="<?php echo htmlspecialchars($full_name); ?>" disabled>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" value="<?php echo htmlspecialchars($email); ?>" disabled>
      </div>

      <div class="form-group">
        <label>About Me</label>
<textarea 
    name="about_me" 
    rows="4"
><?php echo htmlspecialchars($about_me); ?></textarea>
      </div>

      <div class="form-group">
        <label>Profile Picture</label>
        <input type="file" name="profile_image" accept="image/*">
      </div>

      <div class="modal-buttons">
        <button type="submit" class="confirm-btn">Save Changes</button>
        <button type="button" class="cancel-btn" onclick="closeEditProfile()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- CHANGE PASSWORD MODAL -->
<div id="changePasswordModal" class="modal">
  <div class="logout-modal">
    <i class="fa-solid fa-lock logout-icon"></i>
    <h3>Change Password</h3>

    <form class="modal-form" method="POST" autocomplete="off">
      <input type="hidden" name="change_password" value="1">

      <!-- Autofill prevention -->
      <input type="text" style="display:none">
      <input type="password" style="display:none">

      <div class="form-group">
        <label>Current Password</label>
        <input type="password" name="current_password" required autocomplete="off">
      </div>

      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" required autocomplete="new-password">
        <small style="color:#b21e1e; font-size:13px;">
          Must be at least 8 characters, include a number and a special character.
        </small>
      </div>

      <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required autocomplete="new-password">
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
window.addEventListener("click",(e)=>{
  if(e.target===document.getElementById("editProfileModal")) closeEditProfile();
  if(e.target===document.getElementById("changePasswordModal")) closeChangePassword();
});
</script>

</body>
</html>
