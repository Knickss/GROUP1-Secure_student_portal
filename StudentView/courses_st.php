<?php
include("../includes/auth_session.php");
include("../includes/auth_student.php");
include("../config/db_connect.php");

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

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

/* ===================== FETCH ENROLLED COURSES ===================== */
$stmt = $conn->prepare("
  SELECT 
    c.course_id,
    c.course_code,
    c.course_name,
    c.units,
    c.semester,
    u.full_name AS instructor_name,
    c.schedule_day,
    c.schedule_time,
    c.description
  FROM enrollments e
  JOIN courses c ON e.course_id = c.course_id
  LEFT JOIN users u ON c.teacher_id = u.user_id
  WHERE e.student_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$courses = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Enrolled Courses</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="portal-layout">

    <!-- Sidebar -->
    <?php include('sidebar_st.php'); ?>

    <!-- Main Content -->
    <main class="main-content">

      <!-- CLEAN TOPBAR (no search, no dropdown) -->
      <header class="topbar">
        <div></div> <!-- Keeps spacing aligned -->

        <div class="profile-section">
          <img src="<?= $avatar ?>" class="avatar">
          <span class="profile-name"><?= htmlspecialchars($full_name) ?></span>
        </div>
      </header>

      <!-- Courses Body -->
      <section class="dashboard-body">
        <h1>Enrolled Courses</h1>
        <p class="semester-text">1st Semester, A.Y. 2025–2026</p>

        <div class="course-grid">
          <?php if ($courses->num_rows > 0): ?>
            <?php while ($row = $courses->fetch_assoc()): ?>
              
              <div class="course-card">
                <h3><?= htmlspecialchars($row['course_code']) . ": " . htmlspecialchars($row['course_name']); ?></h3>

                <p><strong>Instructor:</strong> <?= htmlspecialchars($row['instructor_name'] ?? "TBA"); ?></p>
                <p><strong>Units:</strong> <?= htmlspecialchars($row['units']); ?></p>
                <p><strong>Schedule:</strong> 
                  <?= htmlspecialchars($row['schedule_day'] ?? "TBA"); ?> | 
                  <?= htmlspecialchars($row['schedule_time'] ?? "TBA"); ?>
                </p>

                <button class="details-btn" onclick="openModal('modal<?= $row['course_id']; ?>')">
                  <i class="fa-solid fa-eye"></i> View Details
                </button>
              </div>

              <!-- Modal for each course -->
<div id="modal<?= $row['course_id']; ?>" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('modal<?= $row['course_id']; ?>')">&times;</span>
    
    <h2><?= htmlspecialchars($row['course_code']) . ": " . htmlspecialchars($row['course_name']); ?></h2>

    <p style="white-space:pre-wrap; margin-top:10px; text-align:center;">
      <?= nl2br(htmlspecialchars($row['description'] ?? "No description available.")); ?>
    </p>
  </div>
</div>


            <?php endwhile; ?>
          <?php else: ?>
            <p>No enrolled courses found.</p>
          <?php endif; ?>
        </div>

      </section>
    </main>
  </div>

  <script>
    function openModal(id){ document.getElementById(id).style.display = "flex"; }
    function closeModal(id){ document.getElementById(id).style.display = "none"; }

    window.onclick = function(event){
      document.querySelectorAll(".modal").forEach(m => {
        if(event.target === m) m.style.display = "none";
      });
    };
  </script>

</body>
</html>
