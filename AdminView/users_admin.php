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


// ================== PASSWORD RULE FUNCTION ==================
function validate_password_rule($password, &$errorMsg) {
    if (strlen($password) < 8) {
        $errorMsg = "Password must be at least 8 characters long.";
        return false;
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errorMsg = "Password must include at least one number.";
        return false;
    }
    if (!preg_match('/[\W_]/', $password)) {
        $errorMsg = "Password must include at least one special character.";
        return false;
    }
    return true;
}


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
        $full_name_new = trim($_POST['full_name']);
        $email         = trim($_POST['email']);
        $username_new  = trim($_POST['username']);
        $role_new      = trim($_POST['role']);
        $password      = $_POST['password'];

        $error = "";

        // Validate password according to rule
        if (!validate_password_rule($password, $error)) {
            echo "<script>alert('$error'); window.history.back();</script>";
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO users (username, password, full_name, email, role, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param("sssss", $username_new, $hash, $full_name_new, $email, $role_new);
        $stmt->execute();
        $stmt->close();

        // LOG: Created user
        log_activity(
            $conn,
            (int)$user_id,
            "Created User",
            "Created user '{$full_name_new}' (username: {$username_new}, role: {$role_new}).",
            "success"
        );

        header("Location: users_admin.php?msg=user_created");
        exit;
    }

    // ---- EDIT USER ----
    if ($action === 'edit_user') {
        $uid            = (int)$_POST['user_id'];
        $edit_full_name = trim($_POST['edit_full_name']);
        $edit_email     = trim($_POST['edit_email']);
        $edit_username  = trim($_POST['edit_username']);
        $edit_role      = trim($_POST['edit_role']);
        $new_pass       = $_POST['edit_password'];

        if ($uid > 0 && $edit_full_name && $edit_email && $edit_username && $edit_role) {

            if ($new_pass !== "") {
                // Validate new password
                $error = "";
                if (!validate_password_rule($new_pass, $error)) {
                    echo "<script>alert('$error'); window.history.back();</script>";
                    exit;
                }

                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("
                    UPDATE users
                    SET username=?, full_name=?, email=?, role=?, password=?, updated_at=NOW()
                    WHERE user_id=?
                ");
                $stmt->bind_param(
                    "sssssi",
                    $edit_username,
                    $edit_full_name,
                    $edit_email,
                    $edit_role,
                    $hash,
                    $uid
                );
            } else {
                // No password change
                $stmt = $conn->prepare("
                    UPDATE users
                    SET username=?, full_name=?, email=?, role=?, updated_at=NOW()
                    WHERE user_id=?
                ");
                $stmt->bind_param(
                    "ssssi",
                    $edit_username,
                    $edit_full_name,
                    $edit_email,
                    $edit_role,
                    $uid
                );
            }

            $stmt->execute();
            $stmt->close();

            // LOG: Edited user
            $detail_msg = "Edited user ID {$uid} ({$edit_full_name}, username: {$edit_username}, role: {$edit_role})";
            if ($new_pass !== "") {
                $detail_msg .= " with password change.";
            } else {
                $detail_msg .= " without password change.";
            }

            log_activity(
                $conn,
                (int)$user_id,
                "Edited User",
                $detail_msg,
                "success"
            );
        }

        header("Location: users_admin.php?msg=user_updated");
        exit;
    }

    // ---- DELETE USER ----
    if ($action === 'delete_user') {
        $uid = (int)$_POST['user_id'];

        if ($uid > 0) {
            // Best-effort delete of linked info + user
            $conn->query("DELETE FROM student_info WHERE user_id=$uid");
            $conn->query("DELETE FROM teacher_info WHERE user_id=$uid");
            $conn->query("DELETE FROM users        WHERE user_id=$uid");

            // LOG: Deleted user
            log_activity(
                $conn,
                (int)$user_id,
                "Deleted User",
                "Deleted user ID {$uid} and any linked academic info.",
                "success"
            );
        }

        header("Location: users_admin.php?msg=user_deleted");
        exit;
    }
}


// ================== STATUS MESSAGE (AFTER REDIRECT) ==================
$status_msg   = "";
$status_class = "";

if (!empty($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'user_created':
            $status_msg   = "User created successfully.";
            $status_class = "success";
            break;
        case 'user_updated':
            $status_msg   = "User updated successfully.";
            $status_class = "success";
            break;
        case 'user_deleted':
            $status_msg   = "User deleted successfully.";
            $status_class = "success";
            break;
        default:
            $status_msg = "";
    }
}


// ================== SEARCH / FILTER / PAGINATION ==================
$search     = trim($_GET['search'] ?? "");
$roleFilter = $_GET['role'] ?? "all";

$perPage = 10;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$where  = "1=1";
$params = [];
$types  = "";

$validRoles = ["student", "teacher", "admin"];

if ($roleFilter !== 'all' && in_array($roleFilter, $validRoles, true)) {
    $where   .= " AND u.role=?";
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
$sql  = "SELECT COUNT(*) AS total FROM users u WHERE $where";
$stmt = $conn->prepare($sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = max(1, ceil($total / $perPage));

// Fetch paginated rows
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

<style>
  .status-message {
    margin-bottom: 15px;
    padding: 10px 18px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
  }
  .status-message.success {
    color: #0c6b24;
    background-color: #d8f5e1;
  }
  .status-message.error {
    color: #b21e1e;
    background-color: #fbdada;
  }
</style>
</head>

<body>
<div class="portal-layout">

    <?php include("sidebar_admin.php"); ?>

    <main class="main-content">

        <header class="topbar">
            <div class="search-container" style="visibility:hidden;"></div>

            <div class="profile-section">
                <img src="<?php echo $avatar; ?>" class="avatar">
                <span class="profile-name"><?php echo htmlspecialchars($full_name); ?></span>
            </div>
        </header>

        <section class="dashboard-body">

            <?php if ($status_msg): ?>
              <div class="status-message <?php echo htmlspecialchars($status_class); ?>" id="statusMessage">
                <?php echo htmlspecialchars($status_msg); ?>
              </div>
            <?php endif; ?>

            <h1>User Management</h1>
            <p class="semester-text">Manage all registered users across Escolink Centra</p>

            <form method="get" class="user-controls-form">
                <div class="user-controls-left">

                    <select name="role" onchange="this.form.submit()">
                        <option value="all"     <?= $roleFilter==="all" ? "selected" : "" ?>>All Roles</option>
                        <option value="student" <?= $roleFilter==="student" ? "selected" : "" ?>>Student</option>
                        <option value="teacher" <?= $roleFilter==="teacher" ? "selected" : "" ?>>Professor</option>
                        <option value="admin"   <?= $roleFilter==="admin" ? "selected" : "" ?>>Admin</option>
                    </select>

                    <input
                        type="text"
                        name="search"
                        placeholder="Search by username, name or email..."
                        value="<?= htmlspecialchars($search) ?>">

                    <button type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                    <?php if ($search !== "" || $roleFilter !== "all"): ?>
                        <a href="users_admin.php" class="clear-link">Clear</a>
                    <?php endif; ?>

                </div>

                <button type="button" class="add-btn" id="openAddUserModal">
                    <i class="fa-solid fa-user-plus"></i> Add New User
                </button>
            </form>

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
                        <tr>
                            <td colspan="6" style="text-align:center;">No users found.</td>
                        </tr>
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
                    <?php endforeach; endif; ?>
                    </tbody>

                </table>
            </div>

        </section>

    </main>
</div>

<!-- ====================== PAGINATION (STICKY) ====================== -->
<div class="pagination-bar">
  <div class="pagination-inner">
    <?php
      $prev = $page - 1;
      $next = $page + 1;
    ?>

    <!-- Previous -->
    <span class="<?php echo ($page <= 1) ? 'disabled' : ''; ?>">
      <?php if ($page > 1): ?>
        <a href="<?php echo build_query(['page' => $prev]); ?>">&laquo;</a>
      <?php else: ?>
        &laquo;
      <?php endif; ?>
    </span>

    <!-- Page numbers -->
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <?php if ($i == $page): ?>
        <span class="current-page"><?php echo $i; ?></span>
      <?php else: ?>
        <a href="<?php echo build_query(['page' => $i]); ?>"><?php echo $i; ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <!-- Next -->
    <span class="<?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
      <?php if ($page < $totalPages): ?>
        <a href="<?php echo build_query(['page' => $next]); ?>">&raquo;</a>
      <?php else: ?>
        &raquo;
      <?php endif; ?>
    </span>
  </div>
</div>


<!-- ====================== ADD USER MODAL ====================== -->
<div id="addUserModal" class="modal">
  <div class="modal-content user-modal">
    <span class="close" id="addUserClose">&times;</span>

    <div class="modal-header">
      <i class="fa-solid fa-user-plus"></i>
      <h2>Add New User</h2>
    </div>

    <form method="post" autocomplete="off">
      <input type="hidden" name="action" value="add_user">

      <label>Full Name</label>
      <input type="text" name="full_name" required autocomplete="off">

      <label>Email</label>
      <input type="email" name="email" required autocomplete="off">

      <label>Username</label>
      <input type="text" name="username" required autocomplete="off">

      <label>Role</label>
      <select name="role" required autocomplete="off">
        <option value="student">Student</option>
        <option value="teacher">Professor</option>
        <option value="admin">Admin</option>
      </select>

      <label>Password</label>
      <input type="password" name="password" required autocomplete="new-password">

      <small style="color:#b21e1e; font-size:13px;">
        Password must be at least 8 characters, include a number, and a special character.
      </small>

      <div class="button-group">
        <button type="submit" class="save-btn">Save</button>
        <button type="button" class="cancel-btn" id="addUserCancel">Cancel</button>
      </div>
    </form>

  </div>
</div>

<!-- ====================== EDIT USER MODAL ====================== -->
<div id="editUserModal" class="modal">
  <div class="modal-content user-modal">
    <span class="close" id="editUserClose">&times;</span>

    <div class="modal-header">
      <i class="fa-solid fa-user-pen"></i>
      <h2>Edit User</h2>
    </div>

    <form method="post" autocomplete="off">
      <input type="hidden" name="action" value="edit_user">
      <input type="hidden" name="user_id" id="edit_user_id">

      <label>Full Name</label>
      <input type="text" id="edit_full_name" name="edit_full_name" required autocomplete="off">

      <label>Email</label>
      <input type="email" id="edit_email" name="edit_email" required autocomplete="off">

      <label>Username</label>
      <input type="text" id="edit_username" name="edit_username" required autocomplete="off">

      <label>Role</label>
      <select name="edit_role" id="edit_role" required autocomplete="off">
        <option value="student">Student</option>
        <option value="teacher">Professor</option>
        <option value="admin">Admin</option>
      </select>

      <label>Change user's password</label>
      <input type="password" id="edit_password" name="edit_password" autocomplete="new-password">

      <small style="color:#b21e1e; font-size:13px;">
        Leave blank to keep current password. If changing, it must be at least 8 characters,
        include a number, and a special character.
      </small>

      <div class="button-group">
        <button type="submit" class="save-btn">Save Changes</button>
        <button type="button" class="cancel-btn" id="editUserCancel">Cancel</button>
      </div>
    </form>

  </div>
</div>

<!-- ====================== DELETE MODAL ====================== -->
<div id="deleteUserModal" class="modal">
  <div class="logout-modal">
    <i class="fa-solid fa-triangle-exclamation logout-icon"></i>
    <h3>Delete User?</h3>
    <p id="deleteUserText"></p>

    <form method="post">
      <input type="hidden" name="action" value="delete_user">
      <input type="hidden" name="user_id" id="delete_user_id">

      <div class="logout-buttons">
        <button type="submit" class="confirm-btn">Yes, Delete</button>
        <button type="button" class="cancel-btn" id="deleteUserCancel">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- ====================== JS ====================== -->
<script>
function openModal(id){
  const m = document.getElementById(id);
  if (m) m.style.display = "flex";
}

function closeModal(id){
  const m = document.getElementById(id);
  if (m) m.style.display = "none";
}

// Add User
document.getElementById("openAddUserModal").onclick  = () => openModal("addUserModal");
document.getElementById("addUserClose").onclick      = () => closeModal("addUserModal");
document.getElementById("addUserCancel").onclick     = () => closeModal("addUserModal");

// Edit User
document.querySelectorAll(".user-edit-btn").forEach(btn => {
  btn.onclick = () => {
    document.getElementById("edit_user_id").value   = btn.dataset.userId;
    document.getElementById("edit_full_name").value = btn.dataset.fullName;
    document.getElementById("edit_email").value     = btn.dataset.email;
    document.getElementById("edit_username").value  = btn.dataset.username;
    document.getElementById("edit_role").value      = btn.dataset.role;
    document.getElementById("edit_password").value  = "";
    openModal("editUserModal");
  };
});

document.getElementById("editUserClose").onclick  = () => closeModal("editUserModal");
document.getElementById("editUserCancel").onclick = () => closeModal("editUserModal");

// Delete User
document.querySelectorAll(".user-delete-btn").forEach(btn => {
  btn.onclick = () => {
    document.getElementById("delete_user_id").value = btn.dataset.userId;
    document.getElementById("deleteUserText").textContent =
      'This will remove user "' + btn.dataset.fullName + '" and any linked academic info.';
    openModal("deleteUserModal");
  };
});

document.getElementById("deleteUserCancel").onclick = () => closeModal("deleteUserModal");

// Auto-hide status message after 3 seconds
const statusEl = document.getElementById('statusMessage');
if (statusEl) {
  setTimeout(() => {
    statusEl.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    statusEl.style.opacity = '0';
    statusEl.style.transform = 'translateY(-5px)';
    setTimeout(() => {
      statusEl.remove();
    }, 400);
  }, 3000);
}
</script>

</body>
</html>
