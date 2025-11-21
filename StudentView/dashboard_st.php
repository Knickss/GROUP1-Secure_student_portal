<?php
// ====================== SECURITY & CONNECTION ======================
include("../includes/auth_session.php");   // prevents access without login
include("../includes/auth_student.php");
include("../config/db_connect.php");       // database connection

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// ====================== FETCH DASHBOARD DATA ======================

// Total enrolled subjects
$stmt = $conn->prepare("SELECT COUNT(*) AS total_subjects FROM enrollments WHERE student_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_subjects = $stmt->get_result()->fetch_assoc()['total_subjects'] ?? 0;
$stmt->close();

// Current GWA (if grades exist)
$stmt = $conn->prepare("
  SELECT 
    ROUND(SUM(g.grade * c.units) / SUM(c.units), 2) AS gwa
  FROM grades g
  JOIN courses c ON g.course_id = c.course_id
  WHERE g.student_id = ? AND g.grade IS NOT NULL
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$gwa = $result['gwa'] ?? "N/A";
$stmt->close();

// Recent announcements (limit 2–3)
$stmt = $conn->prepare("
  SELECT title, content, DATE_FORMAT(date_posted, '%b %e, %Y') AS formatted_date
  FROM announcements
  WHERE audience IN ('all','student','class')
  ORDER BY date_posted DESC
  LIMIT 3
");
$stmt->execute();
$announcements = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Dashboard</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="portal-layout">

    <!-- Sidebar -->
    <?php include('sidebar_st.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
      <header class="topbar">
        <div class="search-container">
          <input type="text" placeholder="Search..." class="search-bar">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <div class="profile-section">
          <img src="images/ProfilePic.png" alt="User Avatar" class="avatar">
          <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
        </div>
      </header>

      <!-- Dashboard Body -->
      <section class="dashboard-body">
        <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
        <p class="semester-text">Current Semester: 1st Semester, A.Y. 2025–2026</p>

        <!-- Summary Cards -->
        <div class="summary-container">

          <div class="summary-card">
            <h4><i class="fa-solid fa-chart-line"></i> Current GWA</h4>
            <p><?php echo $gwa; ?></p>
          </div>

          <div class="summary-card">
            <h4><i class="fa-solid fa-book"></i> Total Enrolled Subjects</h4>
            <p><?php echo $total_subjects; ?></p>
          </div>

          <div class="summary-card">
            <h4><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h4>
            <p>
              <?php
              $announce_count = $announcements->num_rows;
              echo ($announce_count > 0) ? "$announce_count new update" . ($announce_count > 1 ? "s" : "") : "No updates";
              ?>
            </p>
          </div>

          <!-- Quick Links -->
          <div class="summary-card quick-links-horizontal">
            <i class="fa-solid fa-link quick-icon"></i>
            <div class="quick-buttons">
              <a href="courses_st.php" class="quick-btn">View Courses</a>
              <a href="grades_st.php" class="quick-btn">View Grades</a>
            </div>
          </div>

        </div>

        <!-- Recent Announcements Section -->
        <div class="announcements-section">
          <h2><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h2>

          <?php if ($announcements->num_rows > 0): ?>
            <?php while ($row = $announcements->fetch_assoc()): ?>
              <div class="announcement-card">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p class="announce-date">Posted: <?php echo $row['formatted_date']; ?></p>
                <p class="announce-preview">
                  <?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 200))); ?>...
                </p>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No announcements available.</p>
          <?php endif; ?>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
