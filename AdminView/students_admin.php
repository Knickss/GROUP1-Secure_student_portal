<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

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

    if ($action === 'save_student') {
        $user_id    = (int)($_POST['user_id'] ?? 0);
        $student_id = trim($_POST['student_id'] ?? '');
        $program    = trim($_POST['program'] ?? '');
        $year_level = trim($_POST['year_level'] ?? '');
        $section    = trim($_POST['section'] ?? '');

        if ($user_id > 0) {
            // check if student_info row exists
            $stmt = $conn->prepare("SELECT id FROM student_info WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($sid);
            $exists = $stmt->fetch();
            $stmt->close();

            if ($exists) {
                // UPDATE existing academic info
                $stmt = $conn->prepare("
                    UPDATE student_info
                    SET student_id = ?, program = ?, year_level = ?, section = ?
                    WHERE user_id = ?
                ");
                $stmt->bind_param("ssssi", $student_id, $program, $year_level, $section, $user_id);
            } else {
                // INSERT new academic info
                $stmt = $conn->prepare("
                    INSERT INTO student_info (user_id, student_id, program, year_level, section)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("issss", $user_id, $student_id, $program, $year_level, $section);
            }

            $stmt->execute();
            $stmt->close();
        }

        // Always reset back to first page after save
        header("Location: students_admin.php" . build_query(['page' => null]));
        exit;

    } elseif ($action === 'delete_student') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        if ($user_id > 0) {
            $stmt = $conn->prepare("DELETE FROM student_info WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: students_admin.php" . build_query(['page' => null]));
        exit;
    }
}

// ---------- SEARCH / PAGINATION ----------
$search = trim($_GET['search'] ?? '');

// base WHERE: only student users
$where = "u.role = 'student'";
$params = [];
$types  = '';

// search across id, name, program, year, section, email
if ($search !== '') {
    $where .= " AND (
        COALESCE(s.student_id, '') LIKE ? OR
        u.full_name LIKE ? OR
        COALESCE(s.program, '') LIKE ? OR
        COALESCE(s.year_level, '') LIKE ? OR
        COALESCE(s.section, '') LIKE ? OR
        u.email LIKE ?
    )";
    $like = '%' . $search . '%';
    $params = array_fill(0, 6, $like);
    $types  = 'ssssss';
}

// pagination config
$perPage = 10;
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset  = ($page - 1) * $perPage;

// ----- COUNT -----
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
$countRes   = $stmt->get_result();
$total      = $countRes->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = max(1, ceil($total / $perPage));

// ----- FETCH DATA (ORDER BY CREATED_AT DESC) -----
$dataSql = "
    SELECT
        u.user_id,
        u.full_name,
        u.email,
        u.created_at,
        s.student_id,
        s.program,
        s.year_level,
        s.section
    FROM users u
    LEFT JOIN student_info s ON u.user_id = s.user_id
    WHERE $where
    ORDER BY u.created_at DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($dataSql);
if ($types !== '') {
    $types2 = $types . "ii";
    $p2 = array_merge($params, [$perPage, $offset]);
    $stmt->bind_param($types2, ...$p2);
} else {
    $stmt->bind_param("ii", $perPage, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();

// list of student users for dropdown (could show all students)
$userSql = "SELECT user_id, full_name FROM users WHERE role = 'student' ORDER BY created_at DESC";
$userRes = $conn->query($userSql);
$studentUsers = [];
while ($u = $userRes->fetch_assoc()) {
    $studentUsers[] = $u;
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
        <form class="search-container" method="get">
          <input
            type="text"
            name="search"
            placeholder="Search students..."
            class="search-bar"
            value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>"
          >
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </form>

        <div class="profile-section">
          <img src="images/ProfileImg.png" alt="Admin Avatar" class="avatar">
          <span class="profile-name">System Administrator</span>
          <i class="fa-solid fa-chevron-down dropdown-icon"></i>
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
        <button type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>

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
                <th>Section</th>
                <th>Email</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php if (empty($students)): ?>
              <tr>
                <td colspan="7" style="text-align:center;padding:18px;color:#555;">
                  No students found.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($students as $st): ?>
                <tr>
                  <td><?php echo htmlspecialchars($st['student_id'] ?? '—'); ?></td>
                  <td><?php echo htmlspecialchars($st['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($st['program'] ?? '—'); ?></td>
                  <td><?php echo htmlspecialchars($st['year_level'] ?? '—'); ?></td>
                  <td><?php echo htmlspecialchars($st['section'] ?? '—'); ?></td>
                  <td><?php echo htmlspecialchars($st['email']); ?></td>
                  <td>
                    <button
                      class="edit-btn student-edit-btn"
                      data-user-id="<?php echo (int)$st['user_id']; ?>"
                      data-student-id="<?php echo htmlspecialchars($st['student_id'] ?? '', ENT_QUOTES); ?>"
                      data-program="<?php echo htmlspecialchars($st['program'] ?? '', ENT_QUOTES); ?>"
                      data-year="<?php echo htmlspecialchars($st['year_level'] ?? '', ENT_QUOTES); ?>"
                      data-section="<?php echo htmlspecialchars($st['section'] ?? '', ENT_QUOTES); ?>"
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

        <label>Program</label>
        <input type="text" id="programInput" name="program" placeholder="e.g. Computer Science">

        <label>Year Level</label>
        <input type="text" id="yearInput" name="year_level" placeholder="e.g. 1, 2, 3, 4">

        <label>Section</label>
        <input type="text" id="sectionInput" name="section" placeholder="e.g. BSIT3A">

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
      <div class="logout-icon">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </div>
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
        <?php else: ?>
          &laquo;
        <?php endif; ?>
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
        <?php else: ?>
          &raquo;
        <?php endif; ?>
      </span>
    </div>
  </div>

  <script>
    const studentModal        = document.getElementById("studentModal");
    const studentDeleteModal  = document.getElementById("studentDeleteModal");

    const studentUserSelect   = document.getElementById("studentUserSelect");
    const studentUserIdHidden = document.getElementById("studentUserId");

    const studentIdInput      = document.getElementById("studentIdInput");
    const programInput        = document.getElementById("programInput");
    const yearInput           = document.getElementById("yearInput");
    const sectionInput        = document.getElementById("sectionInput");

    // Open "Add" modal
    document.getElementById("addStudentBtn").addEventListener("click", () => {
      document.getElementById("studentModalTitle").textContent = "Add Student Info";

      studentUserSelect.disabled = false;
      studentUserSelect.value = "";
      studentUserIdHidden.value = "";

      studentIdInput.value = "";
      programInput.value   = "";
      yearInput.value      = "";
      sectionInput.value   = "";

      studentModal.style.display = "flex";
    });

    // Link select -> hidden user_id
    studentUserSelect.addEventListener("change", function () {
      studentUserIdHidden.value = this.value;
    });

    // Edit existing / or create if missing
    document.querySelectorAll(".student-edit-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        const uid      = btn.dataset.userId;
        const name     = btn.dataset.name || "";
        const sid      = btn.dataset.studentId || "";
        const prog     = btn.dataset.program   || "";
        const year     = btn.dataset.year      || "";
        const section  = btn.dataset.section   || "";

        document.getElementById("studentModalTitle").textContent =
          "Edit Student Info – " + name;

        studentUserIdHidden.value = uid;
        studentUserSelect.value   = uid;
        studentUserSelect.disabled = true;

        studentIdInput.value = sid;
        programInput.value   = prog;
        yearInput.value      = year;
        sectionInput.value   = section;

        studentModal.style.display = "flex";
      });
    });

    // Delete academic info
    document.querySelectorAll(".student-delete-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        const uid  = btn.dataset.userId;
        const name = btn.dataset.name || "";

        document.getElementById("deleteStudentUserId").value = uid;
        document.getElementById("deleteStudentText").textContent =
          'Delete academic details for "' + name + '"?';

        studentDeleteModal.style.display = "flex";
      });
    });

    // Close modals
    document.getElementById("studentModalClose").onclick =
    document.getElementById("studentModalCancel").onclick = function () {
      studentModal.style.display = "none";
      studentUserSelect.disabled = false;
    };

    document.getElementById("studentDeleteCancel").onclick = function () {
      studentDeleteModal.style.display = "none";
    };

    window.onclick = function (e) {
      if (e.target === studentModal) {
        studentModal.style.display = "none";
        studentUserSelect.disabled = false;
      }
      if (e.target === studentDeleteModal) {
        studentDeleteModal.style.display = "none";
      }
    };
  </script>
</body>
</html>
