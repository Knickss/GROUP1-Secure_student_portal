<?php
include("../includes/auth_session.php");
include("../includes/auth_teacher.php");
include("../config/db_connect.php");

if ($_SESSION['role'] !== 'teacher') {
    header("Location: ../LoginPage/login.php");
    exit;
}

$teacher_id = $_SESSION['user_id'];
$full_name  = $_SESSION['full_name'] ?? 'Teacher';

/* ================= PROFILE PICTURE ================= */
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$stmt->bind_result($profile_pic);
$stmt->fetch();
$stmt->close();

$avatar = (!empty($profile_pic))
    ? "../uploads/" . htmlspecialchars($profile_pic)
    : "images/ProfileImg.png";

/* ================= FETCH COURSES ================= */
$sql = "
  SELECT course_id, course_code, course_name, semester,
         schedule_day, schedule_time, description
  FROM courses
  WHERE teacher_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ================= COUNT STUDENTS ================= */
$student_counts = [];
foreach ($courses as $c) {
    $cid = $c['course_id'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM enrollments WHERE course_id = ?");
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $student_counts[$cid] = $res['total'] ?? 0;
    $stmt->close();
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

  <!-- CLEAN TOPBAR -->
  <header class="topbar">
      <div></div>
      <div class="profile-section">
          <img src="<?= $avatar ?>" class="avatar">
          <span class="profile-name"><?= htmlspecialchars($full_name) ?></span>
      </div>
  </header>

  <section class="dashboard-body">

    <h1>My Classes</h1>
    <p class="semester-text">Teaching Load – 1st Semester, A.Y. 2025–2026</p>

    <div class="course-grid">

      <?php if (!empty($courses)): ?>
        <?php foreach ($courses as $c): ?>
        <div class="course-card">

          <h3><?= htmlspecialchars($c['course_code'] . ": " . $c['course_name']) ?></h3>

          <p><strong>Semester:</strong> <?= htmlspecialchars($c['semester']) ?></p>
          <p><strong>Schedule:</strong> <?= htmlspecialchars($c['schedule_day']) ?> | <?= htmlspecialchars($c['schedule_time']) ?></p>
          <p><strong>Students:</strong> <?= $student_counts[$c['course_id']] ?></p>

          <div class="card-actions">
            <button class="details-btn" onclick="openModal('modal<?= $c['course_id'] ?>')">View Students</button>
            <button class="export-btn">Export List</button>
          </div>

        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No assigned classes yet.</p>
      <?php endif; ?>

    </div>


    <!-- ================= STUDENT LIST MODALS ================ -->
    <?php foreach ($courses as $c): ?>

      <?php
      /* FETCH STUDENTS (with student_id, program, year_level) */
      $sql = "
        SELECT 
          si.student_id,
          si.program,
          si.year_level,
          u.full_name,
          u.email
        FROM enrollments e
        JOIN student_info si ON si.user_id = e.student_id
        JOIN users u ON u.user_id = e.student_id
        WHERE e.course_id = ?
      ";

      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $c['course_id']);
      $stmt->execute();
      $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      $stmt->close();
      ?>

      <div id="modal<?= $c['course_id'] ?>" class="modal">
        <div class="modal-content large-modal">
          <span class="close" onclick="closeModal('modal<?= $c['course_id'] ?>')">&times;</span>

          <h2><?= htmlspecialchars($c['course_code']) ?>: <?= htmlspecialchars($c['course_name']) ?></h2>
          <p><strong>Schedule:</strong> <?= htmlspecialchars($c['schedule_day']) ?> | <?= htmlspecialchars($c['schedule_time']) ?></p>

          <?php if (!empty($students)): ?>
          <table class="student-table">
            <thead>
              <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Year Level</th>
              </tr>
            </thead>

            <tbody>
            <?php foreach ($students as $s): ?>
              <tr>
                <td><?= htmlspecialchars($s['student_id']) ?></td>
                <td><?= htmlspecialchars($s['full_name']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['program']) ?></td>
                <td><?= htmlspecialchars($s['year_level']) ?></td>
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
function openModal(id){
  document.getElementById(id).style.display = "flex";
}
function closeModal(id){
  document.getElementById(id).style.display = "none";
}
window.onclick = function(e){
  document.querySelectorAll(".modal").forEach(modal => {
    if(e.target === modal) modal.style.display = "none";
  });
}
</script>

</body>
</html>
