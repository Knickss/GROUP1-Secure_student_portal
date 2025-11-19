<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// ===== Fetch total assigned classes =====
$class_query = $conn->prepare("SELECT COUNT(*) AS total_classes FROM courses WHERE teacher_id = ?");
$class_query->bind_param("i", $user_id);
$class_query->execute();
$class_result = $class_query->get_result()->fetch_assoc();
$total_classes = $class_result['total_classes'] ?? 0;
$class_query->close();

// ===== Fetch total unique students =====
$student_query = $conn->prepare("
  SELECT COUNT(DISTINCT e.student_id) AS total_students
  FROM enrollments e
  JOIN courses c ON e.course_id = c.course_id
  WHERE c.teacher_id = ?
");
$student_query->bind_param("i", $user_id);
$student_query->execute();
$student_result = $student_query->get_result()->fetch_assoc();
$total_students = $student_result['total_students'] ?? 0;
$student_query->close();

// ===== Fetch recent announcements (own + global) =====
$announcements_query = $conn->prepare("
  SELECT a.title, a.content, a.date_posted, u.full_name AS author_name
  FROM announcements a
  LEFT JOIN users u ON a.author_id = u.user_id
  WHERE a.author_id = ? OR a.audience = 'all'
  ORDER BY a.date_posted DESC
  LIMIT 2
");
$announcements_query->bind_param("i", $user_id);
$announcements_query->execute();
$announcements_result = $announcements_query->get_result();

// ===== Fetch latest announcement (for summary card) =====
$latest_query = $conn->prepare("
  SELECT title, content
  FROM announcements
  WHERE author_id = ? OR audience = 'all'
  ORDER BY date_posted DESC
  LIMIT 1
");
$latest_query->bind_param("i", $user_id);
$latest_query->execute();
$latest = $latest_query->get_result()->fetch_assoc();
$latest_announcement = $latest['title'] ?? 'No announcements yet.';
$latest_query->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Professor Dashboard</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="portal-layout">

    <!-- Sidebar -->
    <?php include('sidebar_prof.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
      <header class="topbar">
        <div class="search-container">
          <input type="text" placeholder="Search..." class="search-bar">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <div class="profile-section">
          <img src="images/ProfileImg.png" alt="User Avatar" class="avatar">
          <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
        </div>
      </header>

      <section class="dashboard-body">
        <h1>Welcome back, <?php echo htmlspecialchars($full_name); ?>!</h1>
        <p class="semester-text">Teaching Overview – 1st Semester, A.Y. 2025–2026</p>

        <!-- Summary Cards -->
        <div class="summary-container">

          <!-- Total Classes -->
          <div class="summary-card">
            <h4><i class="fa-solid fa-book"></i> Total Assigned Classes</h4>
            <p><?php echo $total_classes; ?></p>
          </div>

          <!-- Total Students -->
          <div class="summary-card">
            <h4><i class="fa-solid fa-user-graduate"></i> Total Students</h4>
            <p><?php echo $total_students; ?></p>
          </div>

          <!-- Latest Announcement -->
          <div class="summary-card">
            <h4><i class="fa-solid fa-bullhorn"></i> Latest Announcement</h4>
            <p><?php echo htmlspecialchars($latest_announcement); ?></p>
          </div>

          <!-- Quick Actions -->
          <div class="summary-card quick-actions">
            <h4><i class="fa-solid fa-bolt"></i></h4>
            <div class="quick-buttons">
              <a href="grades_prof.php" class="quick-btn">Manage Grades</a>
              <a href="announcements_prof.php" class="quick-btn">Post Announcement</a>
            </div>
          </div>
        </div>

        <!-- Recent Announcements Section -->
        <section class="announcements-section">
          <h2><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h2>
          <?php if ($announcements_result->num_rows > 0): ?>
            <?php while ($row = $announcements_result->fetch_assoc()): ?>
              <div class="announcement-card">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p class="announce-date">
                  Posted by <?php echo htmlspecialchars($row['author_name'] ?? 'Unknown'); ?> • 
                  <?php echo date("F j, Y", strtotime($row['date_posted'])); ?>
                </p>

                <p class="announce-preview"><?php echo htmlspecialchars(substr($row['content'], 0, 150)); ?>...</p>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No announcements available.</p>
          <?php endif; ?>
        </section>
      </section>
    </main>
  </div>
</body>
</html>
