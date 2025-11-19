<?php
include('../includes/auth_session.php');
include('../config/db_connect.php');

// Ensure only teachers can access
if ($_SESSION['role'] !== 'teacher') {
  header("Location: ../LoginPage/login.php");
  exit;
}

$teacher_id = $_SESSION['user_id'];
$selected_course_id = $_GET['course_id'] ?? null;
$success_message = "";

// ================== SAVE GRADES ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grades'])) {
  $course_id = $_POST['course_id'];

  foreach ($_POST['grades'] as $student_id => $grade) {
    if ($grade === "") continue;

    // Insert or update existing record
    $stmt = $conn->prepare("
      INSERT INTO grades (student_id, course_id, grade, encoded_by, date_encoded)
      VALUES (?, ?, ?, ?, NOW())
      ON DUPLICATE KEY UPDATE 
        grade = VALUES(grade),
        encoded_by = VALUES(encoded_by),
        date_encoded = NOW()
    ");
    $stmt->bind_param("iidi", $student_id, $course_id, $grade, $teacher_id);
    $stmt->execute();
  }
  $success_message = "Grades successfully saved!";
}

// ================== FETCH TEACHER'S COURSES ==================
$stmt = $conn->prepare("
  SELECT course_id, course_code, course_name
  FROM courses
  WHERE teacher_id = ?
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$courses = $stmt->get_result();

// ================== FETCH STUDENTS FOR SELECTED COURSE ==================
$students = [];
if ($selected_course_id) {
  $stmt = $conn->prepare("
    SELECT u.user_id AS student_id, u.full_name, g.grade
    FROM enrollments e
    JOIN users u ON e.student_id = u.user_id
    LEFT JOIN grades g 
      ON g.student_id = u.user_id AND g.course_id = e.course_id
    WHERE e.course_id = ?
  ");
  $stmt->bind_param("i", $selected_course_id);
  $stmt->execute();
  $students = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Grades Management</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="portal-layout">
    
    <!-- Sidebar -->
    <?php include('sidebar_prof.php'); ?>

    <!-- Main Content -->
    <main class="main-content">

      <!-- Topbar -->
      <header class="topbar">
        <div class="search-container">
          <input type="text" placeholder="Search..." class="search-bar">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <div class="profile-section">
          <img src="images/ProfileImg.png" alt="User Avatar" class="avatar">
          <span class="profile-name"><?= htmlspecialchars($_SESSION['full_name']); ?></span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
        </div>
      </header>

      <!-- Grades Management Body -->
      <section class="grades-management">
        <h1>Grades Management</h1>
        <p class="semester-text">A.Y. 2025–2026 | 1st Semester</p>

        <!-- Dropdown -->
        <form method="GET" action="">
          <div class="class-dropdown">
            <label for="classSelect"><i class="fa-solid fa-book"></i> Select Class:</label>
            <select id="classSelect" name="course_id" onchange="this.form.submit()">
              <option value="">-- Choose a class --</option>
              <?php while ($c = $courses->fetch_assoc()): ?>
                <option value="<?= $c['course_id']; ?>" <?= $selected_course_id == $c['course_id'] ? 'selected' : ''; ?>>
                  <?= htmlspecialchars($c['course_code'] . ': ' . $c['course_name']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </form>

        <!-- Table -->
        <form method="POST" action="">
          <input type="hidden" name="course_id" value="<?= htmlspecialchars($selected_course_id ?? '') ?>">

          <div class="table-wrapper" id="gradesTableContainer">
            <table class="table">
              <thead>
                <tr>
                  <th>Student ID</th>
                  <th>Student Name</th>
                  <th>Grade</th>
                </tr>
              </thead>
              <tbody id="gradesTableBody">
                <?php if ($students && $students->num_rows > 0): ?>
                  <?php while ($st = $students->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($st['student_id'] ?? '—') ?></td>
                      <td><?= htmlspecialchars($st['full_name'] ?? 'Unknown') ?></td>
                      <td>
                        <input 
                          type="text" 
                          name="grades[<?= $st['student_id']; ?>]" 
                          class="editable-grade" 
                          value="<?= htmlspecialchars($st['grade'] ?? '') ?>"
                          pattern="^[0-9]{1,2}(\.[0-9]{1,2})?$"
                          title="Enter a valid number (e.g., 1.00)"
                        >
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" style="text-align:center; color:#777;">Select a class to view students.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Save Button -->
          <?php if ($selected_course_id): ?>
            <button type="submit" class="save-btn">Save Changes</button>
          <?php endif; ?>
        </form>

        <!-- Success message -->
        <?php if (!empty($success_message)): ?>
          <p style="color:green; text-align:center; margin-top:10px;">
            <?= htmlspecialchars($success_message); ?>
          </p>
        <?php endif; ?>

      </section>
    </main>
  </div>
</body>
</html>
