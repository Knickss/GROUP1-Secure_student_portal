<?php
include("../includes/auth_session.php");
include("../includes/auth_student.php");
include("../config/db_connect.php");

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Student';

/* ===================== FETCH PROFILE PIC ===================== */
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_pic);
$stmt->fetch();
$stmt->close();

$avatar = !empty($profile_pic)
    ? "../uploads/" . htmlspecialchars($profile_pic)
    : "images/ProfilePic.png";

/* ===================== TOTAL SUBJECTS ===================== */
$stmt = $conn->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total_subjects);
$stmt->fetch();
$stmt->close();

/* ===================== CURRENT GWA ===================== */
$stmt = $conn->prepare("
  SELECT ROUND(SUM(g.grade * c.units) / SUM(c.units), 2)
  FROM grades g
  JOIN courses c ON g.course_id = c.course_id
  WHERE g.student_id = ? AND g.grade IS NOT NULL
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($gwa);
$stmt->fetch();
$stmt->close();

$gwa = $gwa ?? "N/A";

/* ===================== RECENT ANNOUNCEMENTS =====================
   Same logic as announcements_st.php:
   - all
   - students
   - class announcements where student is enrolled
=============================================================== */
$stmt = $conn->prepare("
  SELECT 
    a.title,
    a.content,
    a.date_posted,
    a.audience,
    c.course_code
  FROM announcements a
  LEFT JOIN courses c ON c.course_id = a.course_id
  WHERE 
      a.audience = 'all'
      OR a.audience = 'students'
      OR (
            a.audience = 'class'
            AND a.course_id IN (
                SELECT course_id FROM enrollments WHERE student_id = ?
            )
         )
  ORDER BY a.date_posted DESC
  LIMIT 3
");
$stmt->bind_param("i", $user_id);
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

  <!-- SIDEBAR -->
  <?php include('sidebar_st.php'); ?>

  <!-- MAIN -->
  <main class="main-content">

    <!-- CLEAN TOPBAR -->
    <header class="topbar">
      <div></div>
      <div class="profile-section">
        <img src="<?= $avatar ?>" class="avatar">
        <span class="profile-name"><?= htmlspecialchars($full_name) ?></span>
      </div>
    </header>

    <!-- BODY -->
    <section class="dashboard-body">

      <h1>Welcome, <?= htmlspecialchars($full_name) ?>!</h1>
      <p class="semester-text">Current Semester: 1st Semester, A.Y. 2025–2026</p>

      <!-- SUMMARY CARDS -->
      <div class="summary-container">

        <div class="summary-card">
          <h4><i class="fa-solid fa-chart-line"></i> Current GWA</h4>
          <p><?= htmlspecialchars($gwa) ?></p>
        </div>

        <div class="summary-card">
          <h4><i class="fa-solid fa-book"></i> Total Enrolled Subjects</h4>
          <p><?= htmlspecialchars($total_subjects) ?></p>
        </div>

        <div class="summary-card">
          <h4><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h4>
          <p>
            <?= ($announcements->num_rows > 0)
                ? $announcements->num_rows . " new update" . ($announcements->num_rows > 1 ? "s" : "")
                : "No updates"; ?>
          </p>
        </div>

        <!-- QUICK LINKS -->
        <div class="summary-card quick-links-horizontal">
          <i class="fa-solid fa-link quick-icon"></i>
          <div class="quick-buttons">
            <a href="courses_st.php" class="quick-btn">View Courses</a>
            <a href="grades_st.php" class="quick-btn">View Grades</a>
          </div>
        </div>

      </div>

      <!-- RECENT ANNOUNCEMENTS SECTION -->
      <section class="announcements-section">
        <h2><i class="fa-solid fa-bullhorn"></i> Recent Announcements</h2>

        <?php if ($announcements->num_rows > 0): ?>
          <?php while ($a = $announcements->fetch_assoc()): ?>

            <?php
              if ($a['audience'] === 'all')         $target = "All Users";
              elseif ($a['audience'] === 'students') $target = "All Students";
              elseif ($a['audience'] === 'class')    $target = $a['course_code'] ?? "Your Class";
              else                                   $target = "Unknown";
            ?>

            <div class="announcement-card">
              <h3><?= htmlspecialchars($a['title']) ?></h3>

              <p class="announce-date">
                Posted: <?= date("M d, Y", strtotime($a['date_posted'])) ?>
                • Target: <?= htmlspecialchars($target) ?>
              </p>

              <p class="announce-preview">
                <?= nl2br(htmlspecialchars(mb_strimwidth($a['content'], 0, 180, "..."))) ?>
              </p>
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
