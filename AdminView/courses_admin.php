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
    $q = http_build_query($params);
    return $q ? ('?' . $q) : '';
}

// ---------- HANDLE POST (ADD / EDIT / DELETE) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_course') {
        $course_id     = (int)($_POST['course_id'] ?? 0);
        $code          = trim($_POST['course_code'] ?? '');
        $name          = trim($_POST['course_name'] ?? '');
        $units         = (int)($_POST['units'] ?? 0);
        $semester      = trim($_POST['semester'] ?? '');
        $schedule_day  = trim($_POST['schedule_day'] ?? '');
        $schedule_time = trim($_POST['schedule_time'] ?? '');
        $description   = trim($_POST['description'] ?? '');
        $teacher_id    = ($_POST['teacher_id'] !== '') ? (int)$_POST['teacher_id'] : null;

        if ($course_id > 0) {
            // UPDATE existing course
            $stmt = $conn->prepare("
                UPDATE courses
                SET course_code = ?, course_name = ?, units = ?, semester = ?,
                    schedule_day = ?, schedule_time = ?, description = ?, teacher_id = ?
                WHERE course_id = ?
            ");
            $stmt->bind_param(
                "ssissssii",
                $code,
                $name,
                $units,
                $semester,
                $schedule_day,
                $schedule_time,
                $description,
                $teacher_id,
                $course_id
            );
        } else {
            // INSERT new course
            $stmt = $conn->prepare("
                INSERT INTO courses
                    (course_code, course_name, units, semester,
                     schedule_day, schedule_time, description, teacher_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssissssi",
                $code,
                $name,
                $units,
                $semester,
                $schedule_day,
                $schedule_time,
                $description,
                $teacher_id
            );
        }

        $stmt->execute();
        $stmt->close();

        header("Location: courses_admin.php" . build_query(['page' => null]));
        exit;

    } elseif ($action === 'delete_course') {
        $course_id = (int)($_POST['course_id'] ?? 0);
        if ($course_id > 0) {
            $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
            $stmt->bind_param("i", $course_id);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: courses_admin.php" . build_query(['page' => null]));
        exit;
    }
}

// ---------- FILTERS ----------
$search        = trim($_GET['search'] ?? '');
$teacherFilter = isset($_GET['teacher_filter']) ? (int)$_GET['teacher_filter'] : 0;

// ---------- PAGINATION ----------
$perPage = 10;
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset  = ($page - 1) * $perPage;

// ---------- BUILD WHERE ----------
$where  = "1";
$params = [];
$types  = "";

// search by course code, name, or teacher name
if ($search !== '') {
    $where .= " AND (
        c.course_code LIKE ?
        OR c.course_name LIKE ?
        OR COALESCE(u.full_name, '') LIKE ?
    )";
    $like   = '%' . $search . '%';
    $params = array_merge($params, [$like, $like, $like]);
    $types .= 'sss';
}

// filter by teacher
if ($teacherFilter > 0) {
    $where   .= " AND c.teacher_id = ?";
    $params[] = $teacherFilter;
    $types   .= 'i';
}

// ---------- COUNT ----------
$countSql = "
    SELECT COUNT(*) AS total
    FROM courses c
    LEFT JOIN users u ON c.teacher_id = u.user_id
    WHERE $where
";
$stmt = $conn->prepare($countSql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$countRes = $stmt->get_result();
$total    = $countRes->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = max(1, ceil($total / $perPage));

// ---------- FETCH COURSES ----------
$dataSql = "
    SELECT
        c.course_id,
        c.course_code,
        c.course_name,
        c.units,
        c.semester,
        c.schedule_day,
        c.schedule_time,
        c.description,
        c.teacher_id,
        u.full_name AS teacher_name
    FROM courses c
    LEFT JOIN users u ON c.teacher_id = u.user_id
    WHERE $where
    ORDER BY c.course_code ASC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($dataSql);
if ($types !== '') {
    $types2  = $types . "ii";
    $params2 = array_merge($params, [$perPage, $offset]);
    $stmt->bind_param($types2, ...$params2);
} else {
    $stmt->bind_param("ii", $perPage, $offset);
}
$stmt->execute();
$res = $stmt->get_result();

$courses = [];
while ($row = $res->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();

// ---------- TEACHER LIST ----------
$teachers = [];
$tRes = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'teacher' ORDER BY full_name ASC");
if ($tRes) {
    while ($t = $tRes->fetch_assoc()) {
        $teachers[] = $t;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Escolink Centra | Courses Management</title>
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
                    placeholder="Search courses..."
                    class="search-bar"
                    value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>"
                >
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
            </form>

            <div class="profile-section">
                <img src="images/ProfileImg.png" alt="Admin Avatar" class="avatar">
                <span class="profile-name">Admin Shamir</span>
                <i class="fa-solid fa-chevron-down dropdown-icon"></i>
            </div>
        </header>

        <!-- Body -->
        <section class="dashboard-body">
            <h1>Courses Management</h1>
            <p class="semester-text">Maintain the school's master list of subjects.</p>

            <!-- TOOLBAR: teacher filter + add button -->
            <div class="course-toolbar">
                <form method="get" class="course-filter-form">
                    <select name="teacher_filter">
                        <option value="0">All Teachers</option>
                        <?php foreach ($teachers as $t): ?>
                            <option
                                value="<?php echo (int)$t['user_id']; ?>"
                                <?php if ($teacherFilter === (int)$t['user_id']) echo 'selected'; ?>
                            >
                                <?php echo htmlspecialchars($t['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>">

                    <button type="submit">
                        <i class="fa-solid fa-filter"></i>
                        Filter
                    </button>
                </form>

                <button id="addCourseBtn">
                    <i class="fa-solid fa-book-medical"></i>
                    Add Course
                </button>
            </div>

            <!-- Courses Table -->
            <div class="table-wrapper">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Units</th>
                            <th>Assigned Teacher</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center;padding:18px;color:#555;">
                                No courses found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $c): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($c['course_code']); ?></td>
                                <td><?php echo htmlspecialchars($c['course_name']); ?></td>
                                <td><?php echo (int)$c['units']; ?></td>
                                <td>
                                    <?php
                                        echo $c['teacher_name']
                                            ? htmlspecialchars($c['teacher_name'])
                                            : '—';
                                    ?>
                                </td>
                                <td>
                                    <button
                                        class="edit-btn course-edit-btn"
                                        data-course-id="<?php echo (int)$c['course_id']; ?>"
                                        data-course-code="<?php echo htmlspecialchars($c['course_code'], ENT_QUOTES); ?>"
                                        data-course-name="<?php echo htmlspecialchars($c['course_name'], ENT_QUOTES); ?>"
                                        data-units="<?php echo (int)$c['units']; ?>"
                                        data-semester="<?php echo htmlspecialchars($c['semester'] ?? '', ENT_QUOTES); ?>"
                                        data-schedule-day="<?php echo htmlspecialchars($c['schedule_day'] ?? '', ENT_QUOTES); ?>"
                                        data-schedule-time="<?php echo htmlspecialchars($c['schedule_time'] ?? '', ENT_QUOTES); ?>"
                                        data-description="<?php echo htmlspecialchars($c['description'] ?? '', ENT_QUOTES); ?>"
                                        data-teacher-id="<?php echo $c['teacher_id'] !== null ? (int)$c['teacher_id'] : ''; ?>"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <button
                                        class="delete-btn course-delete-btn"
                                        data-course-id="<?php echo (int)$c['course_id']; ?>"
                                        data-course-code="<?php echo htmlspecialchars($c['course_code'], ENT_QUOTES); ?>"
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

<!-- ==========================
     ADD / EDIT COURSE MODAL
=========================== -->
<div id="courseModal" class="modal course-modal-overlay">
  <div class="course-modal-card">
    <div class="course-modal-header">
      <h2 id="courseModalTitle">Add Course</h2>
      <button type="button" class="close-modal-btn" id="courseModalClose">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="courseForm" method="post">
      <input type="hidden" name="action" value="save_course">
      <input type="hidden" name="course_id" id="courseId">

      <div class="course-modal-body">
        <div class="course-modal-grid">
          <!-- LEFT COLUMN -->
          <div class="course-modal-col">
            <label>Course Code</label>
            <input type="text" name="course_code" placeholder="e.g. ITPROG-301" required>

            <label>Course Name</label>
            <input type="text" name="course_name" placeholder="Enter course name" required>

            <label>Units</label>
            <input type="number" name="units" min="1" placeholder="e.g. 3">

            <label>Semester</label>
            <input type="text" name="semester" placeholder="e.g. 1st Semester">
          </div>

          <!-- RIGHT COLUMN -->
          <div class="course-modal-col">
            <label>Schedule Day</label>
            <input type="text" name="schedule_day" placeholder="e.g. Mon & Wed">

            <label>Schedule Time</label>
            <input type="text" name="schedule_time" placeholder="e.g. 9:00–10:30 AM">

            <label>Assigned Teacher</label>
            <select name="teacher_id">
              <option value="">-- None --</option>
              <?php foreach ($teachers as $t): ?>
                <option value="<?php echo (int)$t['user_id']; ?>">
                  <?php echo htmlspecialchars($t['full_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- DESCRIPTION FULL WIDTH -->
          <div class="course-modal-full">
            <label>Description</label>
            <textarea
              name="description"
              class="course-description-textarea"
              placeholder="Optional course description..."
            ></textarea>
          </div>
        </div>
      </div>

      <div class="course-modal-footer">
        <button type="button" class="cancel-btn" id="courseModalCancel">Cancel</button>
        <button type="submit" class="save-btn" id="courseModalSubmitBtn">Save Course</button>
      </div>
    </form>
  </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div id="courseDeleteModal" class="modal">
    <div class="logout-modal">
        <div class="logout-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3>Delete Course?</h3>
        <p id="deleteCourseText"></p>

        <form id="courseDeleteForm" method="post">
            <input type="hidden" name="action" value="delete_course">
            <input type="hidden" name="course_id" id="deleteCourseId">

            <div class="logout-buttons">
                <button type="submit" class="confirm-btn">Yes, Delete</button>
                <button type="button" class="cancel-btn" id="courseDeleteCancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- PAGINATION -->
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
// ====== ELEMENT HOOKS ======
const courseModal        = document.getElementById('courseModal');
const addCourseBtn       = document.getElementById('addCourseBtn');
const courseForm         = document.getElementById('courseForm');
const courseModalTitle   = document.getElementById('courseModalTitle');
const courseModalClose   = document.getElementById('courseModalClose');
const courseModalCancel  = document.getElementById('courseModalCancel');
const courseModalSubmit  = document.getElementById('courseModalSubmitBtn');
const courseIdInput      = document.getElementById('courseId');

// form fields (use name selectors so no extra ids needed)
const codeInput      = courseForm.querySelector('[name="course_code"]');
const nameInput      = courseForm.querySelector('[name="course_name"]');
const unitsInput     = courseForm.querySelector('[name="units"]');
const semesterInput  = courseForm.querySelector('[name="semester"]');
const schedDayInput  = courseForm.querySelector('[name="schedule_day"]');
const schedTimeInput = courseForm.querySelector('[name="schedule_time"]');
const teacherSelect  = courseForm.querySelector('[name="teacher_id"]');
const descInput      = courseForm.querySelector('[name="description"]');

// delete modal
const courseDeleteModal  = document.getElementById('courseDeleteModal');
const courseDeleteCancel = document.getElementById('courseDeleteCancel');
const deleteCourseId     = document.getElementById('deleteCourseId');
const deleteCourseText   = document.getElementById('deleteCourseText');

// ====== HELPERS ======
function openCourseModal() {
    courseModal.style.display = 'flex';
}

function closeCourseModal() {
    courseModal.style.display = 'none';
}

function openDeleteModal() {
    courseDeleteModal.style.display = 'flex';
}

function closeDeleteModal() {
    courseDeleteModal.style.display = 'none';
}

function clearCourseForm() {
    courseIdInput.value  = '';
    codeInput.value      = '';
    nameInput.value      = '';
    unitsInput.value     = '';
    semesterInput.value  = '';
    schedDayInput.value  = '';
    schedTimeInput.value = '';
    teacherSelect.value  = '';
    descInput.value      = '';
}

// ====== ADD COURSE ======
if (addCourseBtn) {
    addCourseBtn.addEventListener('click', () => {
        clearCourseForm();
        courseModalTitle.textContent  = 'Add Course';
        courseModalSubmit.textContent = 'Save Course';
        openCourseModal();
    });
}

// ====== EDIT COURSE ======
document.querySelectorAll('.course-edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id        = btn.dataset.courseId   || '';
        const code      = btn.dataset.courseCode || '';
        const name      = btn.dataset.courseName || '';
        const units     = btn.dataset.units       || '';
        const semester  = btn.dataset.semester    || '';
        const schedDay  = btn.dataset.scheduleDay || '';
        const schedTime = btn.dataset.scheduleTime|| '';
        const desc      = btn.dataset.description || '';
        const teacherId = btn.dataset.teacherId   || '';

        courseIdInput.value  = id;
        codeInput.value      = code;
        nameInput.value      = name;
        unitsInput.value     = units;
        semesterInput.value  = semester;
        schedDayInput.value  = schedDay;
        schedTimeInput.value = schedTime;
        teacherSelect.value  = teacherId;
        descInput.value      = desc;

        courseModalTitle.textContent  = 'Edit Course';
        courseModalSubmit.textContent = 'Save Changes';
        openCourseModal();
    });
});

// ====== DELETE COURSE ======
document.querySelectorAll('.course-delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id   = btn.dataset.courseId  || '';
        const code = btn.dataset.courseCode|| '';
        deleteCourseId.value = id;
        deleteCourseText.textContent = 'Delete course "' + code + '"?';
        openDeleteModal();
    });
});

// ====== CLOSE HANDLERS ======
if (courseModalClose) {
    courseModalClose.addEventListener('click', closeCourseModal);
}
if (courseModalCancel) {
    courseModalCancel.addEventListener('click', closeCourseModal);
}
if (courseDeleteCancel) {
    courseDeleteCancel.addEventListener('click', closeDeleteModal);
}

window.addEventListener('click', (e) => {
    if (e.target === courseModal) {
        closeCourseModal();
    }
    if (e.target === courseDeleteModal) {
        closeDeleteModal();
    }
});
</script>

</body>
</html>
