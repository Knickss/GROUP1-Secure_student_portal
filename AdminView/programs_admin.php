<?php
include("../includes/auth_session.php");
include("../includes/auth_admin.php"); 
include("../config/db_connect.php");

// =======================
// FETCH ADMIN INFO
// =======================
$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Administrator';

$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($profile_pic);
$stmt->fetch();
$stmt->close();

$avatar = (!empty($profile_pic))
    ? "../uploads/" . htmlspecialchars($profile_pic)
    : "images/ProfileImg.png";

// =======================
// HELPERS
// =======================
function build_query(array $overrides = []): string {
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === null) unset($params[$k]);
        else $params[$k] = $v;
    }
    return '?' . http_build_query($params);
}

// =======================
// HANDLE POST
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD / EDIT PROGRAM
    if ($action === 'save_program') {
        $program_id   = isset($_POST['program_id']) ? (int)$_POST['program_id'] : 0;
        $program_name = trim($_POST['program_name'] ?? '');

        if ($program_name !== '') {
            if ($program_id > 0) {
                $stmt = $conn->prepare("UPDATE programs SET program_name = ? WHERE program_id = ?");
                $stmt->bind_param("si", $program_name, $program_id);
            } else {
                $stmt = $conn->prepare("INSERT INTO programs (program_name) VALUES (?)");
                $stmt->bind_param("s", $program_name);
            }

            $stmt->execute();
            $stmt->close();
        }

        header("Location: programs_admin.php" . build_query(['page' => null]));
        exit;
    }

    // DELETE PROGRAM
    if ($action === 'delete_program') {
        $program_id = (int)($_POST['program_id'] ?? 0);

        if ($program_id > 0) {
            $stmt = $conn->prepare("DELETE FROM programs WHERE program_id = ?");
            $stmt->bind_param("i", $program_id);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: programs_admin.php" . build_query(['page' => null]));
        exit;
    }
}

// =======================
// SEARCH & PAGINATION
// =======================
$search  = trim($_GET['search'] ?? '');
$perPage = 10;
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset  = ($page - 1) * $perPage;

$where  = "1";
$params = [];
$types  = '';

if ($search !== '') {
    $where .= " AND program_name LIKE ?";
    $params[] = '%' . $search . '%';
    $types   .= 's';
}

// COUNT
$countSql = "SELECT COUNT(*) AS total FROM programs WHERE $where";
$stmt = $conn->prepare($countSql);
if ($types !== '') $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = max(1, ceil($total / $perPage));

// FETCH ROWS
$dataSql = "SELECT program_id, program_name
            FROM programs
            WHERE $where
            ORDER BY program_name ASC
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
$programs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Program Management</title>

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

    <!-- Removed top-left search bar -->
    <div class="topbar-left"></div>

    <!-- Synced profile -->
    <div class="profile-section">
        <img src="<?php echo $avatar; ?>" class="avatar">
        <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
    </div>

</header>

<!-- BODY -->
<section class="dashboard-body">

    <h1>Program Management</h1>
    <p class="semester-text">Manage academic programs used for student profiles.</p>

    <div class="program-toolbar">
        <form method="get">
            <input type="text" name="search" 
                   placeholder="Search program name..."
                   value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>">

            <button type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <?php if ($search !== ''): ?>
                <a href="programs_admin.php" 
                   style="color:#b21e8f;text-decoration:none;font-size:13px;">Clear</a>
            <?php endif; ?>
        </form>

        <button id="addProgramBtn">
            <i class="fa-solid fa-layer-group"></i> Add Program
        </button>
    </div>

    <div class="table-wrapper">
        <table class="user-table">
            <thead>
                <tr>
                    <th>Program ID</th>
                    <th>Program Name</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if (empty($programs)): ?>
                <tr><td colspan="3" style="text-align:center;padding:18px;">
                    No programs found.
                </td></tr>
            <?php else: ?>
                <?php foreach ($programs as $p): ?>
                <tr>
                    <td><?php echo $p['program_id']; ?></td>
                    <td><?php echo htmlspecialchars($p['program_name']); ?></td>

                    <td>
                        <button class="edit-btn program-edit-btn"
                                data-id="<?php echo $p['program_id']; ?>"
                                data-name="<?php echo htmlspecialchars($p['program_name']); ?>">
                                <i class="fa-solid fa-pen"></i>
                        </button>

                        <button class="delete-btn program-delete-btn"
                                data-id="<?php echo $p['program_id']; ?>"
                                data-name="<?php echo htmlspecialchars($p['program_name']); ?>">
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
<div id="programModal" class="modal">
  <div class="modal-content user-modal">
    <span class="close" id="programModalClose">&times;</span>

    <div class="modal-header">
      <i class="fa-solid fa-layer-group"></i>
      <h2 id="programModalTitle">Add Program</h2>
    </div>

    <form id="programForm" method="post">
      <input type="hidden" name="action" value="save_program">
      <input type="hidden" name="program_id" id="programIdInput">

      <label>Program Name</label>
      <input type="text" name="program_name" id="programNameInput" required
             placeholder="e.g. Bachelor of Science in IT">

      <div class="button-group">
        <button type="submit" class="save-btn">Save</button>
        <button type="button" class="cancel-btn" id="programModalCancel">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- DELETE MODAL -->
<div id="programDeleteModal" class="modal">
  <div class="logout-modal">
    <div class="logout-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <h3>Delete Program?</h3>
    <p id="deleteProgramText"></p>

    <form method="post">
      <input type="hidden" name="action" value="delete_program">
      <input type="hidden" name="program_id" id="deleteProgramId">

      <div class="logout-buttons">
        <button type="submit" class="confirm-btn">Yes, Delete</button>
        <button type="button" class="cancel-btn" id="programDeleteCancel">Cancel</button>
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
const programModal = document.getElementById("programModal");
const programDeleteModal = document.getElementById("programDeleteModal");

// ADD BUTTON
document.getElementById("addProgramBtn").addEventListener("click", () => {
    document.getElementById("programModalTitle").textContent = "Add Program";
    document.getElementById("programIdInput").value = "";
    document.getElementById("programNameInput").value = "";
    programModal.style.display = "flex";
});

// EDIT PROGRAM
document.querySelectorAll(".program-edit-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        document.getElementById("programModalTitle").textContent = "Edit Program";

        document.getElementById("programIdInput").value = btn.dataset.id;
        document.getElementById("programNameInput").value = btn.dataset.name;

        programModal.style.display = "flex";
    });
});

// DELETE PROGRAM
document.querySelectorAll(".program-delete-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        document.getElementById("deleteProgramId").value = btn.dataset.id;
        document.getElementById("deleteProgramText").textContent =
            'Delete "' + btn.dataset.name + '"?';

        programDeleteModal.style.display = "flex";
    });
});

// CLOSE MODALS
document.getElementById("programModalClose").onclick =
document.getElementById("programModalCancel").onclick = function() {
    programModal.style.display = "none";
};

document.getElementById("programDeleteCancel").onclick = function() {
    programDeleteModal.style.display = "none";
};

window.onclick = e => {
    if (e.target === programModal) programModal.style.display = "none";
    if (e.target === programDeleteModal) programDeleteModal.style.display = "none";
};
</script>

</body>
</html>
