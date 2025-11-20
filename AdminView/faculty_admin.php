<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

/* ============================
   ADMIN PROFILE (Topbar Sync)
============================ */
$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? "Administrator";

// Fetch profile pic
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_pic);
$stmt->fetch();
$stmt->close();

$avatar = (!empty($profile_pic))
    ? "../uploads/" . $profile_pic
    : "images/ProfileImg.png";

/* ============================
   HELPERS
============================ */
function build_query(array $overrides = []): string {
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === null) unset($params[$k]);
        else $params[$k] = $v;
    }
    return '?' . http_build_query($params);
}

/* ============================
   HANDLE POST (ADD / EDIT / DELETE)
============================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_faculty') {
        $user_id       = (int)($_POST['user_id'] ?? 0);
        $teacher_id    = trim($_POST['teacher_id'] ?? '');
        $department_id = ($_POST['department_id'] !== '') ? (int)$_POST['department_id'] : null;

        if ($user_id > 0) {

            // Check if record exists
            $stmt = $conn->prepare("SELECT id FROM teacher_info WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($id);
            $exists = $stmt->fetch();
            $stmt->close();

            if ($exists) {
                $stmt = $conn->prepare("
                    UPDATE teacher_info
                    SET teacher_id = ?, department_id = ?
                    WHERE user_id = ?
                ");
                $stmt->bind_param("sii", $teacher_id, $department_id, $user_id);
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO teacher_info (user_id, teacher_id, department_id)
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("isi", $user_id, $teacher_id, $department_id);
            }

            $stmt->execute();
            $stmt->close();
        }

        header("Location: faculty_admin.php" . build_query(['page' => null]));
        exit;
    }

    elseif ($action === 'delete_faculty') {
        $user_id = (int)($_POST['user_id'] ?? 0);

        if ($user_id > 0) {
            $stmt = $conn->prepare("DELETE FROM teacher_info WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: faculty_admin.php" . build_query(['page' => null]));
        exit;
    }
}

/* ============================
   SEARCH + PAGINATION
============================ */
$search = trim($_GET['search'] ?? '');

$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$where = "u.role = 'teacher'";
$params = [];
$types = '';

if ($search !== '') {
    $where .= " AND (
        COALESCE(t.teacher_id, '') LIKE ? OR
        u.full_name LIKE ? OR
        d.department_name LIKE ? OR
        u.email LIKE ?
    )";
    $like = '%' . $search . '%';
    $params = array_fill(0, 4, $like);
    $types = 'ssss';
}

// COUNT
$countSql = "
    SELECT COUNT(*) AS total
    FROM users u
    LEFT JOIN teacher_info t ON u.user_id = t.user_id
    LEFT JOIN departments d ON t.department_id = d.department_id
    WHERE $where
";
$stmt = $conn->prepare($countSql);
if ($types !== '') $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = max(1, ceil($total / $perPage));

// FETCH DATA
$dataSql = "
    SELECT 
        u.user_id,
        u.full_name,
        u.email,
        t.teacher_id,
        d.department_name,
        u.created_at
    FROM users u
    LEFT JOIN teacher_info t ON u.user_id = t.user_id
    LEFT JOIN departments d ON t.department_id = d.department_id
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
$res = $stmt->get_result();
$faculty = [];
while ($r = $res->fetch_assoc()) $faculty[] = $r;
$stmt->close();

// Dropdown data
$facultyUsers = $conn->query("SELECT user_id, full_name FROM users WHERE role='teacher' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$departments = $conn->query("SELECT department_id, department_name FROM departments ORDER BY department_name ASC")->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Faculty Management</title>

<link rel="stylesheet" href="CSS/format.css">
<link rel="stylesheet" href="CSS/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
<div class="portal-layout">
<?php include('sidebar_admin.php'); ?>

<main class="main-content">

<!-- ===================== TOPBAR ===================== -->
<header class="topbar">

    <!-- remove search in top-left -->
    <div class="topbar-left"></div>

    <div class="profile-section">
        <img src="<?= htmlspecialchars($avatar); ?>" class="avatar">
        <span class="profile-name"><?= htmlspecialchars($full_name); ?></span>
    </div>

</header>

<!-- ===================== PAGE BODY ===================== -->
<section class="dashboard-body">

    <h1>Faculty Management</h1>
    <p class="semester-text">Manage academic details for all faculty members.</p>

    <!-- SEARCH TOOLBAR -->
    <div class="faculty-toolbar">
        <form method="get">
            <input type="text" name="search"
                placeholder="Search by ID, name, department..."
                value="<?= htmlspecialchars($search); ?>">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>

            <?php if ($search !== ''): ?>
                <a href="faculty_admin.php" class="clear-link">Clear</a>
            <?php endif; ?>
        </form>

        <button id="addFacultyBtn"><i class="fa-solid fa-user-plus"></i> Add Faculty Info</button>
    </div>

    <!-- TABLE -->
    <div class="table-wrapper">
        <table class="user-table">
            <thead>
                <tr>
                    <th>Teacher ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($faculty)): ?>
                <tr><td colspan="5" style="text-align:center;padding:18px;">No faculty found.</td></tr>

            <?php else: ?>
                <?php foreach ($faculty as $f): ?>
                <tr>
                    <td><?= htmlspecialchars($f['teacher_id'] ?? '—'); ?></td>
                    <td><?= htmlspecialchars($f['full_name']); ?></td>
                    <td><?= htmlspecialchars($f['department_name'] ?? '—'); ?></td>
                    <td><?= htmlspecialchars($f['email']); ?></td>

                    <td>
                        <button class="edit-btn faculty-edit-btn"
                            data-user-id="<?= $f['user_id']; ?>"
                            data-teacher-id="<?= htmlspecialchars($f['teacher_id'] ?? ''); ?>"
                            data-department="<?= htmlspecialchars($f['department_name'] ?? ''); ?>"
                            data-name="<?= htmlspecialchars($f['full_name']); ?>"
                        ><i class="fa-solid fa-pen"></i></button>

                        <button class="delete-btn faculty-delete-btn"
                            data-user-id="<?= $f['user_id']; ?>"
                            data-name="<?= htmlspecialchars($f['full_name']); ?>"
                        ><i class="fa-solid fa-trash"></i></button>
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

<!-- ===================== MODALS ===================== -->
<div id="facultyModal" class="modal">
  <div class="modal-content user-modal">
    <span class="close" id="facultyModalClose">&times;</span>

    <div class="modal-header">
      <i class="fa-solid fa-user-tie"></i>
      <h2 id="facultyModalTitle">Add Faculty Info</h2>
    </div>

    <form id="facultyForm" method="post">
      <input type="hidden" name="action" value="save_faculty">
      <input type="hidden" name="user_id" id="facultyUserId">

      <label>Faculty User</label>
      <select id="facultyUserSelect">
        <option value="">-- Select faculty --</option>
        <?php foreach ($facultyUsers as $fu): ?>
          <option value="<?= $fu['user_id']; ?>">
            <?= htmlspecialchars($fu['full_name']); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Teacher ID</label>
      <input type="text" id="teacherIdInput" name="teacher_id">

      <label>Department</label>
      <select id="departmentSelect" name="department_id">
        <option value="">-- None --</option>
        <?php foreach ($departments as $d): ?>
          <option value="<?= $d['department_id']; ?>">
            <?= htmlspecialchars($d['department_name']); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <div class="button-group">
        <button type="submit" class="save-btn">Save</button>
        <button type="button" class="cancel-btn" id="facultyModalCancel">Cancel</button>
      </div>
    </form>
  </div>
</div>

<div id="facultyDeleteModal" class="modal">
  <div class="logout-modal">
    <div class="logout-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <h3>Delete Faculty Info?</h3>
    <p id="deleteFacultyText"></p>

    <form method="post">
      <input type="hidden" name="action" value="delete_faculty">
      <input type="hidden" name="user_id" id="deleteFacultyUserId">

      <div class="logout-buttons">
        <button type="submit" class="confirm-btn">Yes, Delete</button>
        <button type="button" class="cancel-btn" id="facultyDeleteCancel">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- ===================== PAGINATION ===================== -->
<div class="pagination-bar">
  <div class="pagination-inner">

    <?php $prev = $page - 1; $next = $page + 1; ?>

    <span class="<?= ($page <= 1) ? 'disabled' : ''; ?>">
      <?php if ($page > 1): ?>
        <a href="<?= build_query(['page'=>$prev]); ?>">&laquo;</a>
      <?php else: ?>&laquo;<?php endif; ?>
    </span>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <?php if ($i == $page): ?>
        <span class="current-page"><?= $i; ?></span>
      <?php else: ?>
        <a href="<?= build_query(['page'=>$i]); ?>"><?= $i; ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <span class="<?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
      <?php if ($page < $totalPages): ?>
        <a href="<?= build_query(['page'=>$next]); ?>">&raquo;</a>
      <?php else: ?>&raquo;<?php endif; ?>
    </span>

  </div>
</div>

<!-- ===================== JS ===================== -->
<script>
const facultyModal = document.getElementById("facultyModal");
const facultyDeleteModal = document.getElementById("facultyDeleteModal");

const facultyUserSelect = document.getElementById("facultyUserSelect");
const facultyUserIdHidden = document.getElementById("facultyUserId");

const teacherIdInput = document.getElementById("teacherIdInput");
const departmentSelect = document.getElementById("departmentSelect");

document.getElementById("addFacultyBtn").addEventListener("click", () => {
  document.getElementById("facultyModalTitle").textContent = "Add Faculty Info";

  facultyUserSelect.disabled = false;
  facultyUserSelect.value = "";
  facultyUserIdHidden.value = "";

  teacherIdInput.value = "";
  departmentSelect.value = "";

  facultyModal.style.display = "flex";
});

facultyUserSelect.addEventListener("change", function(){
  facultyUserIdHidden.value = this.value;
});

document.querySelectorAll(".faculty-edit-btn").forEach(btn => {
  btn.addEventListener("click", () => {

    const uid  = btn.dataset.userId;
    const name = btn.dataset.name;
    const teacherId = btn.dataset.teacherId || "";
    const deptName  = btn.dataset.department || "";

    document.getElementById("facultyModalTitle").textContent =
      "Edit Faculty Info – " + name;

    facultyUserIdHidden.value = uid;
    facultyUserSelect.value = uid;
    facultyUserSelect.disabled = true;

    teacherIdInput.value = teacherId;

    let matchedDept = "";
    <?php foreach ($departments as $d): ?>
      if (deptName === "<?= $d['department_name']; ?>") {
        matchedDept = "<?= $d['department_id']; ?>";
      }
    <?php endforeach; ?>
    departmentSelect.value = matchedDept;

    facultyModal.style.display = "flex";
  });
});

document.querySelectorAll(".faculty-delete-btn").forEach(btn => {
  btn.addEventListener("click", () => {

    const uid  = btn.dataset.userId;
    const name = btn.dataset.name;

    document.getElementById("deleteFacultyUserId").value = uid;
    document.getElementById("deleteFacultyText").textContent =
      'Delete academic info for "' + name + '"?';

    facultyDeleteModal.style.display = "flex";
  });
});

document.getElementById("facultyModalClose").onclick =
document.getElementById("facultyModalCancel").onclick = function() {
  facultyModal.style.display = "none";
  facultyUserSelect.disabled = false;
};

document.getElementById("facultyDeleteCancel").onclick = function() {
  facultyDeleteModal.style.display = "none";
};

window.onclick = e => {
  if (e.target === facultyModal) {
    facultyModal.style.display = "none";
    facultyUserSelect.disabled = false;
  }
  if (e.target === facultyDeleteModal) {
    facultyDeleteModal.style.display = "none";
  }
};
</script>

</body>
</html>
