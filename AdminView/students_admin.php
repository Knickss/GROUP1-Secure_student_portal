<?php
include("../includes/auth_session.php");
include("../includes/auth_admin.php"); 
include("../config/db_connect.php");
include("../includes/logging.php"); // <-- ADDED

// ================== FETCH LOGGED-IN USER HEADER INFO ==================
$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'User';

$avatar = "images/ProfileImg.png";

// Load profile picture
$stmtP = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmtP->bind_param("i", $user_id);
$stmtP->execute();
$stmtP->bind_result($profile_pic);
if ($stmtP->fetch() && !empty($profile_pic)) {
    $avatar = "../uploads/" . htmlspecialchars($profile_pic, ENT_QUOTES);
}
$stmtP->close();

// ---------- SMALL HELPER ----------
function build_query(array $overrides = []): string {
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === null) {
            unset($params[$k]);
        } else {
            $params[$k] = $v;
        }
    }
    $query = http_build_query($params);
    return $query ? ('?' . $query) : '';
}

// ---------- HANDLE POST (ADD / EDIT / DELETE) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ================= SAVE STUDENT (ADD or UPDATE) =================
    if ($action === 'save_student') {

        $target_user_id = (int)($_POST['user_id'] ?? 0);
        $student_id     = trim($_POST['student_id'] ?? '');
        $program        = trim($_POST['program'] ?? '');
        $year_level     = trim($_POST['year_level'] ?? '');

        if ($target_user_id > 0) {

            // Check if student_info row already exists
            $stmt = $conn->prepare("SELECT id FROM student_info WHERE user_id = ?");
            $stmt->bind_param("i", $target_user_id);
            $stmt->execute();
            $stmt->bind_result($sid);
            $exists = $stmt->fetch();
            $stmt->close();

            if ($exists) {
                // UPDATE
                $stmt = $conn->prepare("
                    UPDATE student_info
                    SET student_id = ?, program = ?, year_level = ?
                    WHERE user_id = ?
                ");
                $stmt->bind_param("sssi", $student_id, $program, $year_level, $target_user_id);
                $stmt->execute();
                $stmt->close();

                // LOG UPDATE ACTION
                log_activity(
                    $conn,
                    (int)$user_id,
                    "Updated Student Info",
                    "Updated academic info for student user_id {$target_user_id} (Student ID: {$student_id}, Program: {$program}, Year: {$year_level}).",
                    "success"
                );

            } else {
                // INSERT
                $stmt = $conn->prepare("
                    INSERT INTO student_info (user_id, student_id, program, year_level)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->bind_param("isss", $target_user_id, $student_id, $program, $year_level);
                $stmt->execute();
                $stmt->close();

                // LOG INSERT ACTION
                log_activity(
                    $conn,
                    (int)$user_id,
                    "Added Student Info",
                    "Created academic info for student user_id {$target_user_id} (Student ID: {$student_id}, Program: {$program}, Year: {$year_level}).",
                    "success"
                );
            }
        }

        header("Location: students_admin.php" . build_query(['page' => null]));
        exit;
    }

    // ================= DELETE STUDENT INFO =================
    elseif ($action === 'delete_student') {

        $target_user_id = (int)($_POST['user_id'] ?? 0);

        if ($target_user_id > 0) {
            $stmt = $conn->prepare("DELETE FROM student_info WHERE user_id = ?");
            $stmt->bind_param("i", $target_user_id);
            $stmt->execute();
            $stmt->close();

            // LOG DELETE ACTION
            log_activity(
                $conn,
                (int)$user_id,
                "Deleted Student Info",
                "Removed academic info for student user_id {$target_user_id}.",
                "success"
            );
        }

        header("Location: students_admin.php" . build_query(['page' => null]));
        exit;
    }
}

// ---------- SEARCH / PAGINATION ----------
$search = trim($_GET['search'] ?? '');

// Base WHERE: only student users
$where  = "u.role = 'student'";
$params = [];
$types  = '';

if ($search !== '') {
    $where .= " AND (
        COALESCE(s.student_id, '') LIKE ? OR
        u.full_name LIKE ? OR
        COALESCE(s.program, '') LIKE ? OR
        COALESCE(s.year_level, '') LIKE ? OR
        u.email LIKE ?
    )";
    $like   = '%' . $search . '%';
    $params = [$like, $like, $like, $like, $like];
    $types  = 'sssss';
}

// Pagination setup
$perPage = 10;
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset  = ($page - 1) * $perPage;

// Count
$countSql = "
    SELECT COUNT(*) AS total
    FROM users u
    LEFT JOIN student_info s ON u.user_id = s.user_id
    WHERE $where
";
$stmt = $conn->prepare($countSql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = max(1, ceil($total / $perPage));

// Fetch data
$dataSql = "
    SELECT
        u.user_id,
        u.full_name,
        u.email,
        u.created_at,
        s.student_id,
        s.program,
        s.year_level
    FROM users u
    LEFT JOIN student_info s ON u.user_id = s.user_id
    WHERE $where
    ORDER BY u.created_at DESC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($dataSql);
if ($types !== '') {
    $types2 = $types . "ii";
    $p2     = array_merge($params, [$perPage, $offset]);
    $stmt->bind_param($types2, ...$p2);
} else {
    $stmt->bind_param("ii", $perPage, $offset);
}
$stmt->execute();
$result   = $stmt->get_result();
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();

// Student users list (for dropdown)
$userSql      = "SELECT user_id, full_name FROM users WHERE role = 'student' ORDER BY created_at DESC";
$studentUsers = $conn->query($userSql)->fetch_all(MYSQLI_ASSOC);

// Programs list (for Program dropdown)
$programSql   = "SELECT program_name FROM programs ORDER BY program_name ASC";
$programRes   = $conn->query($programSql);
$programs     = [];
if ($programRes) {
    while ($pr = $programRes->fetch_assoc()) {
        $programs[] = $pr['program_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Student Management</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="CSS/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="portal-layout">
    <?php include('sidebar_admin.php'); ?>

    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <div class="search-container" style="visibility:hidden;"></div>
        <div class="profile-section">
          <img src="<?php echo $avatar; ?>" class="avatar">
          <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
        </div>
      </header>

      <!-- Body -->
      <section class="dashboard-body">
        <h1>Student Management</h1>
        <p class="semester-text">Manage academic details for all students.</p>

        <div class="student-toolbar">
          <form method="get">
            <input
              type="text"
              name="search"
              placeholder="Search by ID, name, program, email..."
              value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>"
            >
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>

            <?php if ($search !== ''): ?>
              <a href="students_admin.php" class="clear-link">Clear</a>
            <?php endif; ?>
          </form>

          <button id="addStudentBtn">
            <i class="fa-solid fa-user-plus"></i> Add Student Info
          </button>
        </div>

        <!-- Students Table -->
        <div class="table-wrapper">
          <table class="user-table">
            <thead>
              <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Program</th>
                <th>Year</th>
                <th>Email</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php if (empty($students)): ?>
              <tr><td colspan="6" style="text-align:center;">No students found.</td></tr>
            <?php else: ?>
              <?php foreach ($students as $st): ?>
                <tr>
                  <td><?php echo htmlspecialchars($st['student_id'] ?? '—'); ?></td>
                  <td><?php echo htmlspecialchars($st['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($st['program'] ?? '—'); ?></td>
                  <td><?php echo htmlspecialchars($st['year_level'] ?? '—'); ?></td>
                  <td><?php echo htmlspecialchars($st['email']); ?></td>
                  <td>
                    <button
                      class="edit-btn student-edit-btn"
                      data-user-id="<?php echo (int)$st['user_id']; ?>"
                      data-student-id="<?php echo htmlspecialchars($st['student_id'] ?? '', ENT_QUOTES); ?>"
                      data-program="<?php echo htmlspecialchars($st['program'] ?? '', ENT_QUOTES); ?>"
                      data-year="<?php echo htmlspecialchars($st['year_level'] ?? '', ENT_QUOTES); ?>"
                      data-name="<?php echo htmlspecialchars($st['full_name'], ENT_QUOTES); ?>"
                    >
                      <i class="fa-solid fa-pen"></i>
                    </button>

                    <button
                      class="delete-btn student-delete-btn"
                      data-user-id="<?php echo (int)$st['user_id']; ?>"
                      data-name="<?php echo htmlspecialchars($st['full_name'], ENT_QUOTES); ?>"
                    >
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <!-- Add / Edit Student Modal -->
  <div id="studentModal" class="modal">
    <div class="modal-content user-modal">
      <span class="close" id="studentModalClose">&times;</span>
      <div class="modal-header">
        <i class="fa-solid fa-user-graduate"></i>
        <h2 id="studentModalTitle">Add Student Info</h2>
      </div>

      <form id="studentForm" method="post">
        <input type="hidden" name="action" value="save_student">
        <input type="hidden" name="user_id" id="studentUserId">

        <label>Student User</label>
        <select id="studentUserSelect">
          <option value="">-- Select student --</option>
          <?php foreach ($studentUsers as $su): ?>
            <option value="<?php echo $su['user_id']; ?>">
              <?php echo htmlspecialchars($su['full_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Student ID</label>
        <input type="text" id="studentIdInput" name="student_id" placeholder="e.g. 2025-001">

        <label program>Select Program</label>
        <select id="programSelect" name="program">
          <option value="">-- Select program --</option>
          <?php foreach ($programs as $progName): ?>
            <option value="<?php echo htmlspecialchars($progName, ENT_QUOTES); ?>">
              <?php echo htmlspecialchars($progName); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Year Level</label>
        <input type="number" id="yearInput" name="year_level" min="1" max="6" step="1">

        <div class="button-group">
          <button type="submit" class="save-btn">Save</button>
          <button type="button" class="cancel-btn" id="studentModalCancel">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="studentDeleteModal" class="modal">
    <div class="logout-modal">
      <div class="logout-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <h3>Delete Student Academic Info?</h3>
      <p id="deleteStudentText"></p>

      <form id="studentDeleteForm" method="post">
        <input type="hidden" name="action" value="delete_student">
        <input type="hidden" name="user_id" id="deleteStudentUserId">

        <div class="logout-buttons">
          <button type="submit" class="confirm-btn">Yes, Delete</button>
          <button type="button" class="cancel-btn" id="studentDeleteCancel">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Pagination -->
  <div class="pagination-bar">
    <div class="pagination-inner">
      <?php $prev = $page - 1; $next = $page + 1; ?>

      <span class="<?php echo ($page <= 1) ? 'disabled' : ''; ?>">
        <?php if ($page > 1): ?>
          <a href="<?php echo build_query(['page' => $prev]); ?>">&laquo;</a>
        <?php else: ?>&laquo;<?php endif; ?>
      </span>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $page): ?>
          <span class="current-page"><?php echo $i; ?></span>
        <?php else: ?>
          <a href="<?php echo build_query(['page' => $i]); ?>"><?php echo $i; ?></a>
        <?php endif; ?>
      <?php endfor; ?>

      <span class="<?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
        <?php if ($page < $totalPages): ?>
          <a href="<?php echo build_query(['page' => $next]); ?>">&raquo;</a>
        <?php else: ?>&raquo;<?php endif; ?>
      </span>
    </div>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", function () {
    const studentModal        = document.getElementById("studentModal");
    const studentDeleteModal  = document.getElementById("studentDeleteModal");

    const addBtn              = document.getElementById("addStudentBtn");
    const studentUserSelect   = document.getElementById("studentUserSelect");
    const studentUserIdHidden = document.getElementById("studentUserId");

    const studentIdInput      = document.getElementById("studentIdInput");
    const programSelect       = document.getElementById("programSelect");
    const yearInput           = document.getElementById("yearInput");

    const modalClose          = document.getElementById("studentModalClose");
    const modalCancel         = document.getElementById("studentModalCancel");
    const deleteCancel        = document.getElementById("studentDeleteCancel");

    // Open "Add Student" modal
    if (addBtn && studentModal) {
      addBtn.addEventListener("click", () => {
        document.getElementById("studentModalTitle").textContent = "Add Student Info";

        if (studentUserSelect) {
          studentUserSelect.disabled = false;
          studentUserSelect.value = "";
        }
        if (studentUserIdHidden) studentUserIdHidden.value = "";

        if (studentIdInput) studentIdInput.value = "";
        if (programSelect)  programSelect.value  = "";
        if (yearInput)      yearInput.value      = "";

        studentModal.style.display = "flex";
      });
    }

    // Sync dropdown -> hidden input
    if (studentUserSelect && studentUserIdHidden) {
      studentUserSelect.addEventListener("change", function () {
        studentUserIdHidden.value = this.value;
      });
    }

    // Edit buttons
    document.querySelectorAll(".student-edit-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        if (!studentModal) return;

        const uid     = btn.dataset.userId;
        const name    = btn.dataset.name || "";
        const sid     = btn.dataset.studentId || "";
        const prog    = btn.dataset.program || "";
        const year    = btn.dataset.year || "";

        document.getElementById("studentModalTitle").textContent =
          "Edit Student Info – " + name;

        if (studentUserIdHidden) studentUserIdHidden.value = uid;
        if (studentUserSelect) {
          studentUserSelect.value = uid;
          studentUserSelect.disabled = true;
        }

        if (studentIdInput) studentIdInput.value = sid;
        if (programSelect)  programSelect.value  = prog;
        if (yearInput)      yearInput.value      = year;

        studentModal.style.display = "flex";
      });
    });

    // Delete buttons
    document.querySelectorAll(".student-delete-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        if (!studentDeleteModal) return;

        const uid  = btn.dataset.userId;
        const name = btn.dataset.name || "";

        const deleteUserId = document.getElementById("deleteStudentUserId");
        const deleteText   = document.getElementById("deleteStudentText");

        if (deleteUserId) deleteUserId.value = uid;
        if (deleteText) deleteText.textContent =
          'Delete academic details for "' + name + '"?';

        studentDeleteModal.style.display = "flex";
      });
    });

    // Close Add/Edit modal
    const closeStudentModal = () => {
      if (studentModal) studentModal.style.display = "none";
      if (studentUserSelect) studentUserSelect.disabled = false;
    };

    if (modalClose)  modalClose.onclick  = closeStudentModal;
    if (modalCancel) modalCancel.onclick = closeStudentModal;

    // Close Delete modal
    if (deleteCancel && studentDeleteModal) {
      deleteCancel.onclick = () => {
        studentDeleteModal.style.display = "none";
      };
    }

    // Close on backdrop click
    window.addEventListener("click", (e) => {
      if (e.target === studentModal) {
        closeStudentModal();
      }
      if (e.target === studentDeleteModal) {
        studentDeleteModal.style.display = "none";
      }
    });
  });
  </script>
</body>
</html>
