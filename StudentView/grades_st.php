<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

// Ensure only students can access this view
if ($_SESSION['role'] !== 'student') {
  header("Location: ../LoginPage/login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// ================== FETCH GRADES ==================
$stmt = $conn->prepare("
  SELECT 
    c.course_code,
    c.course_name,
    c.units,
    u.full_name AS instructor_name,
    g.grade
  FROM grades g
  JOIN courses c ON g.course_id = c.course_id
  LEFT JOIN users u ON c.teacher_id = u.user_id
  WHERE g.student_id = ?
  ORDER BY c.course_code
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$grades = $stmt->get_result();
$stmt->close();

// ================== COMPUTE GWA ==================
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Grades</title>
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
          <input type="text" placeholder="Search grades..." class="search-bar" id="gradeSearch">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <div class="profile-section">
          <img src="images/ProfilePic.png" alt="User Avatar" class="avatar">
          <span class="profile-name"><?= htmlspecialchars($full_name); ?></span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
        </div>
      </header>

      <!-- Grades Body -->
      <section class="dashboard-body">
        <h1>Academic Grades</h1>
        <p class="semester-text">1st Semester, A.Y. 2025–2026</p>

        <div class="gwa-summary">
          <h3><i class="fa-solid fa-chart-line"></i> Current GWA: 
            <span class="gwa-value"><?= htmlspecialchars($gwa); ?></span>
          </h3>
        </div>

        <div class="grades-table-container">
          <table class="grades-table">
            <thead>
              <tr>
                <th>Subject</th>
                <th>Units</th>
                <th>Instructor</th>
                <th>Final Grade</th>
              </tr>
            </thead>
            <tbody id="gradesBody">
              <?php if ($grades->num_rows > 0): ?>
                <?php while ($row = $grades->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['course_code'] . ': ' . $row['course_name']); ?></td>
                    <td><?= htmlspecialchars($row['units']); ?></td>
                    <td><?= htmlspecialchars($row['instructor_name'] ?? 'TBA'); ?></td>
                    <td><?= htmlspecialchars($row['grade'] ?? 'N/A'); ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" style="text-align:center; font-style:italic;">No grades recorded.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="report-actions">
          <button class="download-btn" onclick="downloadReport()">
            <i class="fa-solid fa-file-arrow-down"></i> Download Report
          </button>
        </div>
      </section>
    </main>
  </div>

  <!-- Scripts -->
  <script>
    // Search filter
    document.getElementById("gradeSearch").addEventListener("keyup", function() {
      const query = this.value.toLowerCase();
      document.querySelectorAll("#gradesBody tr").forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? "" : "none";
      });
    });

    // Placeholder for future report export
    function downloadReport() {
      alert("Report download feature coming soon!");
    }
  </script>
</body>
</html>
