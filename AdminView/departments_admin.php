<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

// ---------- HELPERS ----------
function build_query(array $overrides = []): string {
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === null) unset($params[$k]);
        else $params[$k] = $v;
    }
    return '?' . http_build_query($params);
}

// ---------- HANDLE POST (ADD / EDIT / DELETE) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD / EDIT
    if ($action === 'save_department') {
        $dept_id   = isset($_POST['department_id']) ? (int)$_POST['department_id'] : 0;
        $dept_name = trim($_POST['department_name'] ?? '');

        if ($dept_name !== "") {

            if ($dept_id > 0) {
                // UPDATE
                $stmt = $conn->prepare("UPDATE departments SET department_name = ? WHERE department_id = ?");
                $stmt->bind_param("si", $dept_name, $dept_id);
            } else {
                // INSERT
                $stmt = $conn->prepare("INSERT INTO departments (department_name) VALUES (?)");
                $stmt->bind_param("s", $dept_name);
            }

            $stmt->execute();
            $stmt->close();
        }

        header("Location: departments_admin.php" . build_query(['page' => null]));
        exit;
    }

    // DELETE
    if ($action === 'delete_department') {
        $dept_id = (int)($_POST['department_id'] ?? 0);

        if ($dept_id > 0) {
            $stmt = $conn->prepare("DELETE FROM departments WHERE department_id = ?");
            $stmt->bind_param("i", $dept_id);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: departments_admin.php" . build_query(['page' => null]));
        exit;
    }
}

// ---------- SEARCH ----------
$search = trim($_GET['search'] ?? '');

$perPage = 10;
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset  = ($page - 1) * $perPage;

$where = "1";
$params = [];
$types  = '';

if ($search !== '') {
    $where .= " AND department_name LIKE ?";
    $params[] = '%' . $search . '%';
    $types   .= 's';
}

// COUNT
$countSql = "SELECT COUNT(*) AS total FROM departments WHERE $where";
$stmt = $conn->prepare($countSql);

if ($types !== '') $stmt->bind_param($types, ...$params);

$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = max(1, ceil($total / $perPage));

// FETCH ROWS
$dataSql = "SELECT department_id, department_name
            FROM departments
            WHERE $where
            ORDER BY department_name ASC
            LIMIT ? OFFSET ?";

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
$departments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Department Management</title>

<link rel="stylesheet" href="CSS/format.css">
<link rel="stylesheet" href="CSS/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


</head>

<body>
<div class="portal-layout">

<?php include('sidebar_admin.php'); ?>

<main class="main-content">

<!-- TOPBAR -->
<header class="topbar">
    <form class="search-container" method="get">
        <input type="text" name="search" class="search-bar"
               placeholder="Search departments..."
               value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
    </form>

    <div class="profile-section">
        <img src="images/ProfileImg.png" class="avatar">
        <span class="profile-name">System Administrator</span>
        <i class="fa-solid fa-chevron-down dropdown-icon"></i>
    </div>
</header>

<section class="dashboard-body">
    <h1>Department Management</h1>
    <p class="semester-text">Manage academic departments used for faculty assignment.</p>

    <div class="department-toolbar">
    <form method="get">
        <input type="text" name="search"
               placeholder="Search department name..."
               value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>">

        <button type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>

        <?php if ($search !== ''): ?>
            <a href="departments_admin.php"
               style="color:#b21e8f;text-decoration:none;font-size:13px;">Clear</a>
        <?php endif; ?>
    </form>

    <button id="addDepartmentBtn">
        <i class="fa-solid fa-building"></i> Add Department
    </button>
</div>


    <!-- TABLE -->
    <div class="table-wrapper">
        <table class="user-table">
            <thead>
                <tr>
                    <th>Department ID</th>
                    <th>Department Name</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($departments)): ?>
                <tr><td colspan="3" style="text-align:center;padding:18px;">No departments found.</td></tr>
            <?php else: ?>
                <?php foreach ($departments as $d): ?>
                <tr>
                    <td><?php echo $d['department_id']; ?></td>
                    <td><?php echo htmlspecialchars($d['department_name']); ?></td>

                    <td>
                        <button class="edit-btn dept-edit-btn"
                            data-id="<?php echo $d['department_id']; ?>"
                            data-name="<?php echo htmlspecialchars($d['department_name']); ?>">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <button class="delete-btn dept-delete-btn"
                            data-id="<?php echo $d['department_id']; ?>"
                            data-name="<?php echo htmlspecialchars($d['department_name']); ?>">
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

<!-- ADD / EDIT MODAL -->
<div id="departmentModal" class="modal">
  <div class="modal-content user-modal">
    <span class="close" id="departmentModalClose">&times;</span>

    <div class="modal-header">
      <i class="fa-solid fa-building"></i>
      <h2 id="departmentModalTitle">Add Department</h2>
    </div>

    <form id="departmentForm" method="post">
      <input type="hidden" name="action" value="save_department">
      <input type="hidden" name="department_id" id="departmentIdInput">

      <label>Department Name</label>
      <input type="text" name="department_name" id="departmentNameInput"
             placeholder="e.g. Information Technology" required>

      <div class="button-group">
        <button type="submit" class="save-btn">Save</button>
        <button type="button" class="cancel-btn" id="departmentModalCancel">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- DELETE MODAL -->
<div id="departmentDeleteModal" class="modal">
  <div class="logout-modal">
    <div class="logout-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <h3>Delete Department?</h3>
    <p id="deleteDepartmentText"></p>

    <form method="post">
      <input type="hidden" name="action" value="delete_department">
      <input type="hidden" name="department_id" id="deleteDepartmentId">

      <div class="logout-buttons">
        <button type="submit" class="confirm-btn">Yes, Delete</button>
        <button type="button" class="cancel-btn" id="departmentDeleteCancel">Cancel</button>
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
        <a href="<?php echo build_query(['page'=>$prev]); ?>">&laquo;</a>
      <?php else: ?>&laquo;<?php endif; ?>
    </span>

    <?php for ($i=1; $i <= $totalPages; $i++): ?>
      <?php if ($i == $page): ?>
        <span class="current-page"><?php echo $i; ?></span>
      <?php else: ?>
        <a href="<?php echo build_query(['page'=>$i]); ?>"><?php echo $i; ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <span class="<?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
      <?php if ($page < $totalPages): ?>
        <a href="<?php echo build_query(['page'=>$next]); ?>">&raquo;</a>
      <?php else: ?>&raquo;<?php endif; ?>
    </span>
  </div>
</div>

<script>
// MODALS
const departmentModal = document.getElementById("departmentModal");
const departmentDeleteModal = document.getElementById("departmentDeleteModal");

// ADD
document.getElementById("addDepartmentBtn").addEventListener("click", () => {
    document.getElementById("departmentModalTitle").textContent = "Add Department";
    document.getElementById("departmentIdInput").value = "";
    document.getElementById("departmentNameInput").value = "";
    departmentModal.style.display = "flex";
});

// EDIT
document.querySelectorAll(".dept-edit-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        document.getElementById("departmentModalTitle").textContent = "Edit Department";
        document.getElementById("departmentIdInput").value = btn.dataset.id;
        document.getElementById("departmentNameInput").value = btn.dataset.name;
        departmentModal.style.display = "flex";
    });
});

// DELETE
document.querySelectorAll(".dept-delete-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        document.getElementById("deleteDepartmentId").value = btn.dataset.id;
        document.getElementById("deleteDepartmentText").textContent =
          'Delete "' + btn.dataset.name + '"?';
        departmentDeleteModal.style.display = "flex";
    });
});

// CLOSE MODALS
document.getElementById("departmentModalClose").onclick =
document.getElementById("departmentModalCancel").onclick = function() {
    departmentModal.style.display = "none";
};

document.getElementById("departmentDeleteCancel").onclick = function() {
    departmentDeleteModal.style.display = "none";
};

window.onclick = e => {
    if (e.target === departmentModal) departmentModal.style.display = "none";
    if (e.target === departmentDeleteModal) departmentDeleteModal.style.display = "none";
};
</script>

</body>
</html>
