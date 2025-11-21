<?php
// ========== SECURITY CHECK: STUDENTS ONLY ==========
if (!isset($_SESSION)) { session_start(); }

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
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

    <a href="dashboard_st.php"
       class="<?= ($current_page == 'dashboard_st.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-house"></i> Dashboard
    </a>

    <a href="courses_st.php"
       class="<?= ($current_page == 'courses_st.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-book"></i> Courses
    </a>

    <a href="grades_st.php"
       class="<?= ($current_page == 'grades_st.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-graduation-cap"></i> Grades
    </a>

    <a href="announcements_st.php"
       class="<?= ($current_page == 'announcements_st.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-bullhorn"></i> Announcements
    </a>

    <a href="profile_st.php"
       class="<?= ($current_page == 'profile_st.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-user"></i> Profile
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
