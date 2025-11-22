<?php
include("../includes/auth_session.php");
include("../includes/auth_teacher.php");
include("../config/db_connect.php");

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Teacher';

/* ---------------- FETCH PROFILE PIC ---------------- */
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_pic);
$stmt->fetch();
$stmt->close();

$avatar = !empty($profile_pic)
    ? "../uploads/" . htmlspecialchars($profile_pic)
    : "images/ProfileImg.png";


/* ---------------- TOTAL ASSIGNED CLASSES ---------------- */
$sql = "SELECT COUNT(*) AS total_classes FROM courses WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_classes = $stmt->get_result()->fetch_assoc()['total_classes'] ?? 0;
$stmt->close();

/* ---------------- TOTAL UNIQUE STUDENTS ---------------- */
$sql = "
  SELECT COUNT(DISTINCT e.student_id) AS total_students
  FROM enrollments e
  JOIN courses c ON e.course_id = c.course_id
  WHERE c.teacher_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_students = $stmt->get_result()->fetch_assoc()['total_students'] ?? 0;
$stmt->close();

/* ---------------- LATEST ANNOUNCEMENT (for card) ---------------- */
$sql = "
  SELECT a.title
  FROM announcements a
  LEFT JOIN courses c ON a.course_id = c.course_id
  WHERE 
      a.audience = 'all'
      OR a.audience = 'teachers'
      OR (a.audience = 'class' AND c.teacher_id = ?)
      OR a.author_id = ?
  ORDER BY a.date_posted DESC
  LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$latest_announcement = $row['title'] ?? "No announcements yet.";
$stmt->close();

/* ---------------- RECENT ANNOUNCEMENTS (same logic as announcements_prof.php) ---------------- */
$sql = "
  SELECT 
      a.announcement_id,
      a.title,
      a.content,
      a.date_posted,
      a.course_id,
      a.audience,
      c.course_code,
      u.full_name AS author_name
  FROM announcements a
  LEFT JOIN courses c ON c.course_id = a.course_id
  LEFT JOIN users u ON u.user_id = a.author_id
  WHERE 
      a.audience = 'all'
      OR a.audience = 'teachers'
      OR (a.audience = 'class' AND c.teacher_id = ?)
      OR a.author_id = ?
  ORDER BY a.date_posted DESC
  LIMIT 4
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$announcements = $stmt->get_result();
$stmt->close();
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

  <?php include('sidebar_prof.php'); ?>

  <main class="main-content">

    <!-- CLEAN TOPBAR -->
    <header class="topbar">
      <div></div>
      <div class="profile-section">
        <img src="<?= $avatar ?>" class="avatar">
        <span class="profile-name"><?= htmlspecialchars($full_name) ?></span>
      </div>
    </header>

    <section class="dashboard-body">

      <h1>Welcome back, <?= htmlspecialchars($full_name) ?>!</h1>
      <p class="semester-text">Teaching Overview – 1st Semester, A.Y. 2025–2026</p>

      <!-- SUMMARY CARDS -->
      <div class="summary-container">

        <div class="summary-card">
          <h4><i class="fa-solid fa-book"></i> Total Assigned Classes</h4>
          <p><?= $total_classes ?></p>
        </div>

        <div class="summary-card">
          <h4><i class="fa-solid fa-user-graduate"></i> Total Students</h4>
          <p><?= $total_students ?></p>
        </div>

        <div class="summary-card">
          <h4><i class="fa-solid fa-bullhorn"></i> Latest Announcement</h4>
          <p><?= htmlspecialchars($latest_announcement) ?></p>
        </div>

        <div class="summary-card quick-actions">
          <h4><i class="fa-solid fa-bolt"></i></h4>
          <div class="quick-buttons">
            <a href="grades_prof.php" class="quick-btn">Manage Grades</a>
            <a href="announcements_prof.php" class="quick-btn">Post Announcement</a>
          </div>
        </div>

      </div>

      <!-- RECENT ANNOUNCEMENTS -->
      <section class="announcements-section">
        <h2><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h2>

        <?php if ($announcements->num_rows > 0): ?>
          <?php while ($a = $announcements->fetch_assoc()): ?>

            <?php
              if ($a['audience'] === 'all')          $target = "All Users";
              elseif ($a['audience'] === 'teachers') $target = "All Teachers";
              elseif ($a['audience'] === 'class')    $target = $a['course_code'] ?: "Class";
              else                                   $target = "Unknown";
            ?>

            <div class="announcement-card">
              <h3><?= htmlspecialchars($a['title']) ?></h3>

              <p class="announce-date">
                Posted by <?= htmlspecialchars($a['author_name'] ?? 'Unknown') ?> |
                <?= date("F j, Y", strtotime($a['date_posted'])) ?> |
                Target: <?= htmlspecialchars($target) ?>
              </p>

              <?php
$content = $a['content'] ?? '';

if (mb_strlen($content) > 300) {
    $preview = mb_substr($content, 0, 300) . "…";
} else {
    $preview = $content;
}
?>
<p class="announce-preview"><?= nl2br(htmlspecialchars($preview)) ?></p>

            </div>

          <?php endwhile; ?>
        <?php else: ?>
          <p style="text-align:center; font-style:italic;">No announcements available.</p>
        <?php endif; ?>

      </section>

    </section>
  </main>

</div>
</body>
</html>
