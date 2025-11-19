<?php
  $current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
  <div class="sidebar-header">
    <img src="images/LogoDashboard.png" alt="Escolink Logo" class="sidebar-logo">
  </div>

  <div class="sidebar-menu">

    <a href="dashboard_admin.php" 
       class="<?php echo ($current_page == 'dashboard_admin.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-chart-line"></i> Dashboard
    </a>

    <a href="users_admin.php" 
       class="<?php echo ($current_page == 'users_admin.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-users"></i> Users
    </a>

    <a href="faculty_admin.php" 
       class="<?php echo ($current_page == 'faculty_admin.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-chalkboard-user"></i> Faculty
    </a>

    <a href="students_admin.php" 
       class="<?php echo ($current_page == 'students_admin.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-user-graduate"></i> Students
    </a>

    <a href="courses_admin.php" 
       class="<?php echo ($current_page == 'courses_admin.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-book"></i> Courses
    </a>

    <a href="enroll_students_admin.php" 
   class="<?php echo ($current_page == 'enroll_students_admin.php') ? 'active' : ''; ?>">
  <i class="fa-solid fa-user-check"></i> Enroll Students
</a>


    <!-- NEW: Programs Management -->
    <a href="programs_admin.php" 
       class="<?php echo ($current_page == 'programs_admin.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-layer-group"></i> Programs
    </a>

    <!-- NEW: Departments Management -->
    <a href="departments_admin.php" 
       class="<?php echo ($current_page == 'departments_admin.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-building"></i> Departments
    </a>

    <a href="announcements_admin.php" 
       class="<?php echo ($current_page == 'announcements_admin.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-bullhorn"></i> Announcements
    </a>

    <a href="logs_admin.php" 
       class="<?php echo ($current_page == 'logs_admin.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-clipboard-list"></i> Logs
    </a>

    <a href="profile_admin.php" 
       class="<?php echo ($current_page == 'profile_admin.php') ? 'active' : ''; ?>">
      <i class="fa-solid fa-user-gear"></i> Profile
    </a>

  </div>

  <div class="sidebar-footer">
    <a href="#" id="admin-logout-trigger">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
  </div>
</div>

<!-- Logout Modal (unchanged) -->
<div id="adminLogoutModal" class="modal">
  <div class="logout-modal">
    <div class="logout-icon">
      <i class="fa-solid fa-right-from-bracket"></i>
    </div>
    <h3>Confirm Logout</h3>
    <p>Are you sure you want to log out of the admin portal?</p>
    <div class="logout-buttons">
      <button class="confirm-btn" id="admin-logout-confirm">Yes, Logout</button>
      <button class="cancel-btn" id="admin-logout-cancel">Cancel</button>
    </div>
  </div>
</div>

<script>
  const adminLogoutLink = document.getElementById('admin-logout-trigger');
  const adminLogoutModal = document.getElementById('adminLogoutModal');

  if (adminLogoutLink) {
    adminLogoutLink.addEventListener('click', function(e) {
      e.preventDefault();
      adminLogoutModal.style.display = 'flex';
    });
  }

  const adminLogoutCancel = document.getElementById('admin-logout-cancel');
  if (adminLogoutCancel) {
    adminLogoutCancel.addEventListener('click', function() {
      adminLogoutModal.style.display = 'none';
    });
  }

  const adminLogoutConfirm = document.getElementById('admin-logout-confirm');
  if (adminLogoutConfirm) {
    adminLogoutConfirm.addEventListener('click', function() {
      window.location.href = "../LoginPage/login.php";
    });
  }

  window.addEventListener('click', function(e) {
    if (e.target === adminLogoutModal) {
      adminLogoutModal.style.display = 'none';
    }
  });
</script>
