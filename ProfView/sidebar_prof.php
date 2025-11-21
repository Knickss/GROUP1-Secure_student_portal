<?php
// ========== SECURITY CHECK: TEACHERS ONLY ==========
if (!isset($_SESSION)) { session_start(); }

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../LoginPage/login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
  <div class="sidebar-header">
    <img src="images/LogoDashboard.png" alt="Escolink Logo" class="sidebar-logo">
  </div>

  <div class="sidebar-menu">

    <a href="dashboard_prof.php"
       class="<?= ($current_page == 'dashboard_prof.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-chalkboard"></i> Dashboard
    </a>

    <a href="myclasses_prof.php"
       class="<?= ($current_page == 'myclasses_prof.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-book-open"></i> My Classes
    </a>

    <a href="grades_prof.php"
       class="<?= ($current_page == 'grades_prof.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-pen-to-square"></i> Grades Management
    </a>

    <a href="announcements_prof.php"
       class="<?= ($current_page == 'announcements_prof.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-bullhorn"></i> Announcements
    </a>

    <a href="profile_prof.php"
       class="<?= ($current_page == 'profile_prof.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-user-tie"></i> Profile
    </a>

  </div>

  <div class="sidebar-footer">
    <a href="#" id="logout-link">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
  </div>
</div>

<!-- =================== LOGOUT MODAL =================== -->
<div id="logoutModal" class="modal">
  <div class="logout-modal">
    <i class="fa-solid fa-right-from-bracket logout-icon"></i>
    <h3>Confirm Logout</h3>
    <p>Are you sure you want to log out of your account?</p>
    <div class="logout-buttons">
      <button class="confirm-btn" onclick="confirmLogout()">Yes, Logout</button>
      <button class="cancel-btn" onclick="closeLogoutModal()">Cancel</button>
    </div>
  </div>
</div>

<!-- =================== LOGOUT SCRIPT =================== -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const logoutLink = document.getElementById("logout-link");
    const modal = document.getElementById("logoutModal");

    logoutLink.onclick = (e) => {
      e.preventDefault();
      modal.style.display = "flex";
    };

    window.closeLogoutModal = () => {
      modal.style.display = "none";
    };

    window.confirmLogout = () => {
      window.location.href = "../logout.php";
    };

    window.onclick = (event) => {
      if (event.target === modal) closeLogoutModal();
    };
  });
</script>
