<?php
include("../includes/auth_session.php");
include("../includes/auth_student.php");
include("../config/db_connect.php");

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Fetch enrolled courses with instructor details
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
      <header class="topbar">
        <div class="search-container">
          <input type="text" placeholder="Search courses..." class="search-bar" id="courseSearch">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <div class="profile-section">
          <img src="images/ProfilePic.png" alt="User Avatar" class="avatar">
          <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
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
                <h3><?php echo htmlspecialchars($row['course_code']) . ": " . htmlspecialchars($row['course_name']); ?></h3>
                <p><strong>Instructor:</strong> <?php echo htmlspecialchars($row['instructor_name'] ?? "TBA"); ?></p>
                <p><strong>Units:</strong> <?php echo htmlspecialchars($row['units']); ?></p>
                <p><strong>Schedule:</strong> <?php echo htmlspecialchars($row['schedule_day'] ?? "TBA"); ?> | <?php echo htmlspecialchars($row['schedule_time'] ?? "TBA"); ?></p>
                <button class="details-btn" onclick="openModal('modal<?php echo $row['course_id']; ?>')">View Details</button>
              </div>

              <!-- Modal for each course -->
              <div id="modal<?php echo $row['course_id']; ?>" class="modal">
                <div class="modal-content">
                  <span class="close" onclick="closeModal('modal<?php echo $row['course_id']; ?>')">&times;</span>
                  <h2><?php echo htmlspecialchars($row['course_code']) . ": " . htmlspecialchars($row['course_name']); ?></h2>
                  <p><?php echo nl2br(htmlspecialchars($row['description'] ?? "No description available.")); ?></p>
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
    // Modal controls
    function openModal(id) {
      document.getElementById(id).style.display = "flex";
    }
    function closeModal(id) {
      document.getElementById(id).style.display = "none";
    }
    window.onclick = function(event) {
      const modals = document.querySelectorAll(".modal");
      modals.forEach(m => {
        if (event.target === m) m.style.display = "none";
      });
    };

    // Simple search filter
    document.getElementById("courseSearch").addEventListener("keyup", function() {
      const query = this.value.toLowerCase();
      document.querySelectorAll(".course-card").forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(query) ? "block" : "none";
      });
    });
  </script>
</body>
</html>
