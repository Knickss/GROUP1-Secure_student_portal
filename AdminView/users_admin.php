<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

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


// ================== HELPER: BUILD QUERY STRING ==================
function build_query(array $overrides = []): string {
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === null) unset($params[$k]);
        else $params[$k] = $v;
    }
    $q = http_build_query($params);
    return $q ? "?$q" : "";
}


// ================== HANDLE POST (ADD / EDIT / DELETE) ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ---- ADD USER ----
    if ($action === 'add_user') {
        $full_name = trim($_POST['full_name']);
        $email     = trim($_POST['email']);
        $username  = trim($_POST['username']);
        $role      = trim($_POST['role']);
        $password  = $_POST['password'];

        if ($full_name && $email && $username && $password && $role) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("
                INSERT INTO users (username, password, full_name, email, role, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->bind_param("sssss", $username, $hash, $full_name, $email, $role);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: users_admin.php");
        exit;
    }

    // ---- EDIT USER ----
    if ($action === 'edit_user') {
        $uid       = (int)$_POST['user_id'];
        $full_name = trim($_POST['edit_full_name']);
        $email     = trim($_POST['edit_email']);
        $username  = trim($_POST['edit_username']);
        $role      = trim($_POST['edit_role']);
        $new_pass  = $_POST['edit_password'];

        if ($uid > 0 && $full_name && $email && $username && $role) {
            if ($new_pass !== "") {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("
                    UPDATE users SET username=?, full_name=?, email=?, role=?, password=?, updated_at=NOW()
                    WHERE user_id=?
                ");
                $stmt->bind_param("sssssi", $username, $full_name, $email, $role, $hash, $uid);
            } else {
                $stmt = $conn->prepare("
                    UPDATE users SET username=?, full_name=?, email=?, role=?, updated_at=NOW()
                    WHERE user_id=?
                ");
                $stmt->bind_param("ssssi", $username, $full_name, $email, $role, $uid);
            }

            $stmt->execute();
            $stmt->close();
        }

        header("Location: users_admin.php");
        exit;
    }

    // ---- DELETE USER ----
    if ($action === 'delete_user') {
        $uid = (int)$_POST['user_id'];

        if ($uid > 0) {
            $stmt = $conn->prepare("DELETE FROM student_info WHERE user_id=?");
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("DELETE FROM teacher_info WHERE user_id=?");
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: users_admin.php");
        exit;
    }
}


// ================== SEARCH / FILTER / PAGINATION ==================
$search     = trim($_GET['search'] ?? "");
$roleFilter = $_GET['role'] ?? "all";

// Pagination
$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

// Build WHERE
$where = "1=1";
$params = [];
$types  = "";

$validRoles = ["student", "teacher", "admin"];

if ($roleFilter !== 'all' && in_array($roleFilter, $validRoles, true)) {
    $where .= " AND u.role=?";
    $params[] = $roleFilter;
    $types   .= "s";
}

if ($search !== "") {
    $where .= " AND (u.username LIKE ? OR u.full_name LIKE ? OR u.email LIKE ? OR u.role LIKE ?)";
    $like = "%$search%";
    for ($i = 0; $i < 4; $i++) {
        $params[] = $like;
        $types   .= "s";
    }
}


// Count total
$sql = "SELECT COUNT(*) AS total FROM users u WHERE $where";
$stmt = $conn->prepare($sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = max(1, ceil($total / $perPage));


// Fetch data
$sql = "
    SELECT u.user_id, u.username, u.full_name, u.email, u.role, u.created_at
    FROM users u
    WHERE $where
    ORDER BY u.created_at DESC, u.user_id DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
if ($types) {
    $types2 = $types . "ii";
    $bind   = array_merge($params, [$perPage, $offset]);
    $stmt->bind_param($types2, ...$bind);
} else {
    $stmt->bind_param("ii", $perPage, $offset);
}

$stmt->execute();
$res   = $stmt->get_result();
$users = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function display_role($r) {
    return $r === "teacher" ? "Professor" : ucfirst($r);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Escolink Centra | User Management</title>

<link rel="stylesheet" href="CSS/format.css">
<link rel="stylesheet" href="CSS/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
<div class="portal-layout">
    <?php include("sidebar_admin.php"); ?>

    <main class="main-content">

        <!-- TOPBAR (RESTORED LAYOUT) -->
        <header class="topbar">
            <div class="search-container" style="visibility:hidden;"></div>

            <div class="profile-section">
                <img src="<?php echo $avatar; ?>" class="avatar">
                <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
            </div>
        </header>

        <section class="dashboard-body">
            <h1>User Management</h1>
            <p class="semester-text">Manage all registered users across Escolink Centra</p>

            <!-- FILTERS -->
            <form method="get" class="user-controls-form">
                <div class="user-controls-left">
                    <select name="role" onchange="this.form.submit()">
                        <option value="all"     <?= $roleFilter === "all" ? "selected" : "" ?>>All Roles</option>
                        <option value="student" <?= $roleFilter === "student" ? "selected" : "" ?>>Student</option>
                        <option value="teacher" <?= $roleFilter === "teacher" ? "selected" : "" ?>>Professor</option>
                        <option value="admin"   <?= $roleFilter === "admin" ? "selected" : "" ?>>Admin</option>
                    </select>

                    <input type="text" name="search"
                           placeholder="Search by username, name or email..."
                           value="<?= htmlspecialchars($search) ?>">

                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>

                    <?php if ($search !== "" || $roleFilter !== "all"): ?>
                        <a href="users_admin.php" style="font-size:13px;color:#b21e8f;">Clear</a>
                    <?php endif; ?>
                </div>

                <button type="button" class="add-btn" id="openAddUserModal">
                    <i class="fa-solid fa-user-plus"></i> Add New User
                </button>
            </form>

            <!-- TABLE -->
            <div class="table-wrapper">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (!$users): ?>
                        <tr><td colspan="6" style="text-align:center;">No users found.</td></tr>
                    <?php else: foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['user_id'] ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['full_name']) ?></td>
                            <td><?= display_role($u['role']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>

                            <td>
                                <button class="edit-btn user-edit-btn"
                                        data-user-id="<?= $u['user_id'] ?>"
                                        data-username="<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>"
                                        data-full-name="<?= htmlspecialchars($u['full_name'], ENT_QUOTES) ?>"
                                        data-email="<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>"
                                        data-role="<?= htmlspecialchars($u['role'], ENT_QUOTES) ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button class="delete-btn user-delete-btn"
                                        data-user-id="<?= $u['user_id'] ?>"
                                        data-full-name="<?= htmlspecialchars($u['full_name'], ENT_QUOTES) ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; endif;?>
                    </tbody>
                </table>
            </div>

        </section>
    </main>
</div>

<!-- PAGINATION BAR (RESTORED FULLY) -->
<div class="pagination-bar">
    <div class="pagination-inner">

        <!-- PREVIOUS -->
        <?php if ($page > 1): ?>
            <a href="<?= build_query(['page' => $page - 1]) ?>">&laquo;</a>
        <?php else: ?>
            <span class="disabled">&laquo;</span>
        <?php endif; ?>

        <!-- NUMBERS -->
        <?php for ($i=1; $i<=$totalPages; $i++): ?>
            <?php if ($i == $page): ?>
                <span class="current-page"><?= $i ?></span>
            <?php else: ?>
                <a href="<?= build_query(['page' => $i]) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <!-- NEXT -->
        <?php if ($page < $totalPages): ?>
            <a href="<?= build_query(['page' => $page + 1]) ?>">&raquo;</a>
        <?php else: ?>
            <span class="disabled">&raquo;</span>
        <?php endif; ?>

    </div>
</div>


<!-- MODALS + JS (UNCHANGED) -->
<script>
function openModal(id){ document.getElementById(id).style.display="flex"; }
function closeModal(id){ document.getElementById(id).style.display="none"; }

document.getElementById("openAddUserModal").onclick = ()=>openModal("addUserModal");
document.getElementById("addUserClose").onclick = ()=>closeModal("addUserModal");
document.getElementById("addUserCancel").onclick = ()=>closeModal("addUserModal");

document.querySelectorAll(".user-edit-btn").forEach(btn=>{
    btn.onclick = ()=>{
        document.getElementById("edit_user_id").value = btn.dataset.userId;
        document.getElementById("edit_full_name").value = btn.dataset.fullName;
        document.getElementById("edit_email").value = btn.dataset.email;
        document.getElementById("edit_username").value = btn.dataset.username;
        document.getElementById("edit_role").value = btn.dataset.role;
        document.getElementById("edit_password").value = "";
        openModal("editUserModal");
    };
});

document.getElementById("editUserClose").onclick = ()=>closeModal("editUserModal");
document.getElementById("editUserCancel").onclick = ()=>closeModal("editUserModal");

document.querySelectorAll(".user-delete-btn").forEach(btn=>{
    btn.onclick = ()=>{
        document.getElementById("delete_user_id").value = btn.dataset.userId;
        document.getElementById("deleteUserText").textContent =
            'This will remove user "' + btn.dataset.fullName + '" and linked academic info.';
        openModal("deleteUserModal");
    };
});

document.getElementById("deleteUserCancel").onclick = ()=>closeModal("deleteUserModal");
</script>

</body>
</html>
