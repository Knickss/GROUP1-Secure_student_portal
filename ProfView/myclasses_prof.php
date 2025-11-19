<?php
include('../config/db_connect.php');
include('../includes/auth_session.php'); // shared auth file

// Ensure only teachers can access
if ($_SESSION['role'] !== 'teacher') {
  header("Location: ../LoginPage/login.php");
  exit;
}

$teacher_id = $_SESSION['user_id'];

// Fetch courses assigned to the teacher
$query = "
  SELECT c.course_id, c.course_code, c.course_name, c.semester, 
         c.schedule_day, c.schedule_time, c.description
  FROM courses c
  WHERE c.teacher_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count students per course
$student_counts = [];
foreach ($courses as $course) {
  $cid = $course['course_id'];
  $count_sql = "SELECT COUNT(*) AS total_students FROM enrollments WHERE course_id = ?";
  $count_stmt = $conn->prepare($count_sql);
  $count_stmt->bind_param("i", $cid);
  $count_stmt->execute();
  $result = $count_stmt->get_result()->fetch_assoc();
  $student_counts[$cid] = $result['total_students'] ?? 0;
  $count_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | My Classes</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="portal-layout">

    <?php include('sidebar_prof.php'); ?>

    <main class="main-content">
      <header class="topbar">
        <div class="search-container">
          <input type="text" placeholder="Search classes..." class="search-bar">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <div class="profile-section">
          <img src="images/ProfileImg.png" alt="User Avatar" class="avatar">
          <span class="profile-name"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
        </div>
      </header>

      <section class="dashboard-body">
        <h1>My Classes</h1>
        <p class="semester-text">Teaching Load – 1st Semester, A.Y. 2025–2026</p>

        <div class="course-grid">
          <?php if (count($courses) > 0): ?>
            <?php foreach ($courses as $course): ?>
              <div class="course-card">
                <h3><?= htmlspecialchars($course['course_code']) . ': ' . htmlspecialchars($course['course_name']) ?></h3>
                <p><strong>Semester:</strong> <?= htmlspecialchars($course['semester']) ?></p>
                <p><strong>Schedule:</strong> <?= htmlspecialchars($course['schedule_day']) ?> | <?= htmlspecialchars($course['schedule_time']) ?></p>
                <p><strong>Students:</strong> <?= $student_counts[$course['course_id']] ?></p>
                <div class="card-actions">
                  <button class="details-btn" onclick="openModal('modal<?= $course['course_id'] ?>')">View Students</button>
                  <button class="export-btn">Export List</button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p>No assigned classes yet.</p>
          <?php endif; ?>
        </div>

        <?php foreach ($courses as $course): ?>
          <?php
            // Fetch enrolled students per course
            $sql = "
              SELECT u.student_id, u.full_name, u.email
              FROM enrollments e
              JOIN users u ON e.student_id = u.user_id
              WHERE e.course_id = ?
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $course['course_id']);
            $stmt->execute();
            $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
          ?>
          <div id="modal<?= $course['course_id'] ?>" class="modal">
            <div class="modal-content large-modal">
              <span class="close" onclick="closeModal('modal<?= $course['course_id'] ?>')">&times;</span>
              <h2><?= htmlspecialchars($course['course_code']) ?>: <?= htmlspecialchars($course['course_name']) ?></h2>
              <p><strong>Schedule:</strong> <?= htmlspecialchars($course['schedule_day']) ?> | <?= htmlspecialchars($course['schedule_time']) ?></p>

              <?php if (count($students) > 0): ?>
                <table class="student-table">
                  <thead>
                    <tr>
                      <th>Student ID</th>
                      <th>Student Name</th>
                      <th>Email</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($students as $st): ?>
                      <tr>
                        <td><?= htmlspecialchars($st['student_id'] ?? '') ?></td>

                        <td><?= htmlspecialchars($st['full_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($st['email'] ?? '') ?></td>

                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <p>No enrolled students yet.</p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>

      </section>
    </main>
  </div>

  <script>
    function openModal(id) {
      document.getElementById(id).style.display = "flex";
    }

    function closeModal(id) {
      document.getElementById(id).style.display = "none";
    }

    window.onclick = function(event) {
      document.querySelectorAll(".modal").forEach(m => {
        if (event.target === m) m.style.display = "none";
      });
    };
  </script>
</body>
</html>
