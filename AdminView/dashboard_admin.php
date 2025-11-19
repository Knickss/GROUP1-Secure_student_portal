<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

// Fetch admin info
$admin_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
  SELECT full_name, email, profile_pic 
  FROM users 
  WHERE user_id = ? AND role = 'admin'
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fallback profile picture
$adminPic = !empty($admin['profile_pic'])
  ? "../uploads/" . $admin['profile_pic']
  : "images/ProfileImg.png";

// Fetch statistics
$totalUsers     = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalCourses   = $conn->query("SELECT COUNT(*) AS total FROM courses")->fetch_assoc()['total'] ?? 0;
$activeSessions = 0; // placeholder

// Fetch latest 5 logs from activity_logs table
$logs = $conn->query("
  SELECT action, details, timestamp 
  FROM activity_logs 
  ORDER BY log_id DESC 
  LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Admin Dashboard</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="CSS/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="portal-layout">

    <!-- Sidebar -->
    <?php include('sidebar_admin.php'); ?>

    <!-- Main Content -->
    <main class="main-content">

      <!-- Topbar -->
      <header class="topbar">
        <div class="search-container">
          <input type="text" placeholder="Search..." class="search-bar">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <div class="profile-section">
          <img src="<?= htmlspecialchars($adminPic) ?>" alt="Admin Avatar" class="avatar">
          <span class="profile-name"><?= htmlspecialchars($admin['full_name']) ?></span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
        </div>
      </header>

      <!-- Dashboard Body -->
      <section class="dashboard-body">
        <h1>Welcome, System Admin <?= htmlspecialchars($admin['full_name']) ?></h1>
        <p class="semester-text">System Overview – Escolink Centra Management Console</p>

        <!-- Statistic Cards -->
        <div class="summary-container">
          <div class="summary-card">
            <h4><i class="fa-solid fa-users"></i> Total Users</h4>
            <p id="stat-total-users"><?= $totalUsers ?></p>
          </div>

          <div class="summary-card">
            <h4><i class="fa-solid fa-book-open"></i> Total Courses</h4>
            <p id="stat-total-courses"><?= $totalCourses ?></p>
          </div>

          <div class="summary-card">
            <h4><i class="fa-solid fa-signal"></i> Active Sessions</h4>
            <p id="stat-active-sessions"><?= $activeSessions ?></p>
          </div>

          <div class="summary-card">
            <h4><i class="fa-solid fa-clock-rotate-left"></i> Recent Activity</h4>
            <p id="stat-recent">Latest System Actions</p>
          </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="activity-section">
          <h2><i class="fa-solid fa-list"></i> Recent System Activity</h2>

          <div class="activity-feed">
            <?php if ($logs && $logs->num_rows > 0): ?>
              <?php while ($log = $logs->fetch_assoc()): ?>
                <div class="activity-item">
                  <span class="activity-label">[<?= date('h:i A', strtotime($log['timestamp'])) ?>]</span>
                  <?= htmlspecialchars($log['details']) ?>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div class="activity-item">No recent activities logged.</div>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Logout Confirmation Modal -->
  <div id="adminLogoutModal" class="modal">
    <div class="logout-modal">
      <i class="fa-solid fa-right-from-bracket logout-icon"></i>
      <h3>Confirm Logout</h3>
      <p>Are you sure you want to log out of your account?</p>
      <div class="logout-buttons">
        <button class="confirm-btn" onclick="confirmLogout()">Yes, Logout</button>
        <button class="cancel-btn" onclick="closeModal('adminLogoutModal')">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    function openModal(id) {
      document.getElementById(id).style.display = "flex";
    }

    function closeModal(id) {
      document.getElementById(id).style.display = "none";
    }

    function confirmLogout() {
      window.location.href = "../LoginPage/login.php";
    }

    window.onclick = function(event) {
      document.querySelectorAll(".modal").forEach(m => {
        if (event.target === m) m.style.display = "none";
      });
    };
  </script>
</body>
</html>
