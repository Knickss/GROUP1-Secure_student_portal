<?php
include("../includes/auth_session.php");
include("../config/db_connect.php");

// ================== HEADER USER INFO (name + profile pic) ==================
$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'System Administrator';

// default avatar
$avatar = "images/ProfileImg.png";

// try to load profile_pic from DB
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($profile_pic);
    if ($stmt->fetch() && !empty($profile_pic)) {
        // stored filename in DB, actual file is in ../uploads/
        $avatar = "../uploads/" . $profile_pic;
    }
    $stmt->close();
}

// ================== HELPER: BUILD QUERY STRING ==================
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

// ================== HANDLE POST (ADD / EDIT / DELETE) ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ---- ADD USER ----
    if ($action === 'add_user') {
        $full_name = trim($_POST['full_name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $username  = trim($_POST['username'] ?? '');
        $role      = trim($_POST['role'] ?? '');
        $password  = $_POST['password'] ?? '';

        if ($full_name !== '' && $email !== '' && $username !== '' && $password !== '' && $role !== '') {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("
                INSERT INTO users (username, password, full_name, email, role, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->bind_param("sssss", $username, $password_hash, $full_name, $email, $role);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: users_admin.php" . build_query(['page' => null]));
        exit;
    }

    // ---- EDIT USER ----
    if ($action === 'edit_user') {
        $user_id   = (int)($_POST['user_id'] ?? 0);
        $full_name = trim($_POST['edit_full_name'] ?? '');
        $email     = trim($_POST['edit_email'] ?? '');
        $username  = trim($_POST['edit_username'] ?? '');
        $role      = trim($_POST['edit_role'] ?? '');
        $new_pass  = $_POST['edit_password'] ?? '';

        if ($user_id > 0 && $full_name !== '' && $email !== '' && $username !== '' && $role !== '') {
            // If password field is not empty, update it too
            if ($new_pass !== '') {
                $password_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("
                    UPDATE users
                    SET username = ?, full_name = ?, email = ?, role = ?, password = ?, updated_at = NOW()
                    WHERE user_id = ?
                ");
                $stmt->bind_param("sssssi", $username, $full_name, $email, $role, $password_hash, $user_id);
            } else {
                $stmt = $conn->prepare("
                    UPDATE users
                    SET username = ?, full_name = ?, email = ?, role = ?, updated_at = NOW()
                    WHERE user_id = ?
                ");
                $stmt->bind_param("ssssi", $username, $full_name, $email, $role, $user_id);
            }

            $stmt->execute();
            $stmt->close();
        }

        header("Location: users_admin.php" . build_query(['page' => null]));
        exit;
    }

    // ---- DELETE USER ----
    if ($action === 'delete_user') {
        $user_id = (int)($_POST['user_id'] ?? 0);

        if ($user_id > 0) {
            // Remove academic details first
            $stmt = $conn->prepare("DELETE FROM student_info WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("DELETE FROM teacher_info WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();

            // Then delete user
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }

        header("Location: users_admin.php" . build_query(['page' => null]));
        exit;
    }
}

// ================== SEARCH / FILTER / PAGINATION ==================
$search     = trim($_GET['search'] ?? '');
$roleFilter = $_GET['role'] ?? 'all';

// Pagination
$perPage = 10;
$page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset  = ($page - 1) * $perPage;

// Build WHERE
$where  = "1=1";
$params = [];
$types  = "";

// Filter by role
$validRoles = ['student', 'teacher', 'admin'];
if ($roleFilter !== 'all' && in_array($roleFilter, $validRoles, true)) {
    $where .= " AND u.role = ?";
    $params[] = $roleFilter;
    $types   .= "s";
}

// Search anywhere
if ($search !== '') {
    $where .= " AND (
        u.username  LIKE ? OR
        u.full_name LIKE ? OR
        u.email     LIKE ? OR
        u.role      LIKE ?
    )";
    $like = '%' . $search . '%';
    for ($i = 0; $i < 4; $i++) {
        $params[] = $like;
        $types   .= "s";
    }
}

// COUNT total
$countSql = "SELECT COUNT(*) AS total FROM users u WHERE $where";
$stmt = $conn->prepare($countSql);
if ($types !== '') $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = max(1, ceil($total / $perPage));

// FETCH rows
$dataSql = "
    SELECT 
        u.user_id,
        u.username,
        u.full_name,
        u.email,
        u.role,
        u.created_at
    FROM users u
    WHERE $where
    ORDER BY u.created_at DESC, u.user_id DESC
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

$users = [];
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    $users[] = $r;
}
$stmt->close();

// Helper: display role nicely
function display_role($role) {
    if ($role === 'teacher') return 'Professor';
    if ($role === 'student') return 'Student';
    if ($role === 'admin')   return 'Admin';
    return ucfirst($role);
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
    <?php include('sidebar_admin.php'); ?>

    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <!-- removed search bar on the left -->
        <div class="topbar-left"></div>

        <div class="profile-section">
          <img
            src="<?php echo htmlspecialchars($avatar, ENT_QUOTES); ?>"
            alt="User Avatar"
            class="avatar"
          >
          <span class="profile-name">
            <?php echo htmlspecialchars($full_name); ?>
          </span>
          <!-- dropdown icon removed -->
        </div>
      </header>

      <!-- Main body -->
      <section class="dashboard-body">
        <h1>User Management</h1>
        <p class="semester-text">Manage all registered users across Escolink Centra</p>

        <!-- Filters + Add button -->
        <form method="get" class="user-controls-form">
          <div class="user-controls-left">
            <select name="role" onchange="this.form.submit()">
              <option value="all"     <?php echo ($roleFilter === 'all')     ? 'selected' : ''; ?>>All Roles</option>
              <option value="student" <?php echo ($roleFilter === 'student') ? 'selected' : ''; ?>>Student</option>
              <option value="teacher" <?php echo ($roleFilter === 'teacher') ? 'selected' : ''; ?>>Professor</option>
              <option value="admin"   <?php echo ($roleFilter === 'admin')   ? 'selected' : ''; ?>>Admin</option>
            </select>

            <input
              type="text"
              name="search"
              placeholder="Search by username, name or email..."
              value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>"
            >

            <button type="submit">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <?php if ($search !== '' || $roleFilter !== 'all'): ?>
              <a href="users_admin.php" style="font-size:13px;color:#b21e8f;text-decoration:none;">Clear</a>
            <?php endif; ?>
          </div>

          <button type="button" class="add-btn" id="openAddUserModal">
            <i class="fa-solid fa-user-plus"></i> Add New User
          </button>
        </form>

        <!-- Users Table -->
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
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="6" style="text-align:center;padding:18px;color:#555;">No users found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($users as $u): ?>
                <tr>
                  <td><?php echo htmlspecialchars($u['user_id']); ?></td>
                  <td><?php echo htmlspecialchars($u['username']); ?></td>
                  <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                  <td><?php echo htmlspecialchars(display_role($u['role'])); ?></td>
                  <td><?php echo htmlspecialchars($u['email']); ?></td>
                  <td>
                    <button
                      class="edit-btn user-edit-btn"
                      data-user-id="<?php echo (int)$u['user_id']; ?>"
                      data-username="<?php echo htmlspecialchars($u['username'], ENT_QUOTES); ?>"
                      data-full-name="<?php echo htmlspecialchars($u['full_name'], ENT_QUOTES); ?>"
                      data-email="<?php echo htmlspecialchars($u['email'], ENT_QUOTES); ?>"
                      data-role="<?php echo htmlspecialchars($u['role'], ENT_QUOTES); ?>"
                    >
                      <i class="fa-solid fa-pen"></i>
                    </button>
                    <button
                      class="delete-btn user-delete-btn"
                      data-user-id="<?php echo (int)$u['user_id']; ?>"
                      data-full-name="<?php echo htmlspecialchars($u['full_name'], ENT_QUOTES); ?>"
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

  <!-- Add User Modal -->
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
        <input
          type="text"
          name="full_name"
          placeholder="Enter full name"
          required
        >

        <label>Email</label>
        <input
          type="email"
          name="email"
          placeholder="Enter email"
          required
          autocomplete="off"
        >

        <label>Username</label>
        <input
          type="text"
          name="username"
          placeholder="Enter username"
          required
          autocomplete="off"
        >

        <label>Role</label>
        <select name="role" required>
          <option value="student">Student</option>
          <option value="teacher">Professor</option>
          <option value="admin">Admin</option>
        </select>

        <label>Password</label>
        <input
          type="password"
          name="password"
          placeholder="Temporary password"
          required
          autocomplete="new-password"
        >

        <div class="button-group">
          <button type="submit" class="save-btn">Save</button>
          <button type="button" class="cancel-btn" id="addUserCancel">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit User Modal -->
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
        <input
          type="text"
          name="edit_full_name"
          id="edit_full_name"
          required
        >

        <label>Email</label>
        <input
          type="email"
          name="edit_email"
          id="edit_email"
          required
          autocomplete="off"
        >

        <label>Username</label>
        <input
          type="text"
          name="edit_username"
          id="edit_username"
          required
          autocomplete="off"
        >

        <label>Role</label>
        <select name="edit_role" id="edit_role" required>
          <option value="student">Student</option>
          <option value="teacher">Professor</option>
          <option value="admin">Admin</option>
        </select>

        <label>New Password (optional)</label>
        <input
          type="password"
          name="edit_password"
          id="edit_password"
          placeholder="Leave blank to keep current password"
          autocomplete="new-password"
        >

        <div class="button-group">
          <button type="submit" class="save-btn">Save Changes</button>
          <button type="button" class="cancel-btn" id="editUserCancel">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
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
    // ---------- Helpers ----------
    function openModal(id) {
      const m = document.getElementById(id);
      if (m) m.style.display = "flex";
    }
    function closeModal(id) {
      const m = document.getElementById(id);
      if (m) m.style.display = "none";
    }

    // ---------- ADD USER ----------
    const addBtn    = document.getElementById("openAddUserModal");
    const addClose  = document.getElementById("addUserClose");
    const addCancel = document.getElementById("addUserCancel");

    if (addBtn)    addBtn.onclick    = () => openModal("addUserModal");
    if (addClose)  addClose.onclick  = () => closeModal("addUserModal");
    if (addCancel) addCancel.onclick = () => closeModal("addUserModal");

    // ---------- EDIT USER ----------
    const editClose  = document.getElementById("editUserClose");
    const editCancel = document.getElementById("editUserCancel");

    document.querySelectorAll(".user-edit-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        const uid   = btn.dataset.userId;
        const name  = btn.dataset.fullName;
        const email = btn.dataset.email;
        const usern = btn.dataset.username;
        const role  = btn.dataset.role;

        document.getElementById("edit_user_id").value   = uid;
        document.getElementById("edit_full_name").value = name;
        document.getElementById("edit_email").value     = email;
        document.getElementById("edit_username").value  = usern;
        document.getElementById("edit_role").value      = role;
        document.getElementById("edit_password").value  = "";

        openModal("editUserModal");
      });
    });

    if (editClose)  editClose.onclick  = () => closeModal("editUserModal");
    if (editCancel) editCancel.onclick = () => closeModal("editUserModal");

    // ---------- DELETE USER ----------
    const deleteCancel = document.getElementById("deleteUserCancel");

    document.querySelectorAll(".user-delete-btn").forEach(btn => {
      btn.addEventListener("click", () => {
        const uid  = btn.dataset.userId;
        const name = btn.dataset.fullName;

        document.getElementById("delete_user_id").value = uid;
        document.getElementById("deleteUserText").textContent =
          'This will remove the user "' + name +
          '" and any linked academic info. Continue?';

        openModal("deleteUserModal");
      });
    });

    if (deleteCancel) deleteCancel.onclick = () => closeModal("deleteUserModal");

    // ---------- CLOSE MODALS ON BACKDROP CLICK ----------
    window.onclick = function (event) {
      const addM  = document.getElementById("addUserModal");
      const editM = document.getElementById("editUserModal");
      const delM  = document.getElementById("deleteUserModal");

      [addM, editM, delM].forEach(m => {
        if (event.target === m) {
          m.style.display = "none";
        }
      });
    };
  </script>
</body>
</html>
