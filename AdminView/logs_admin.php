<?php
include("../includes/auth_session.php");
include("../includes/auth_admin.php");
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

// ================== FILTERS ==================
$searchUser = trim($_GET['user'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');
$dateFilter = trim($_GET['date'] ?? ''); // format: YYYY-MM-DD

// ================== FETCH LOGS ==================
$sql = "
    SELECT 
        l.log_id,
        l.timestamp,
        l.action,
        l.details,
        l.status,
        l.ip_address,
        u.user_id,
        u.full_name,
        u.role,
        COALESCE(s.student_id, t.teacher_id, u.user_id) AS display_id
    FROM activity_logs l
    LEFT JOIN users u       ON l.user_id = u.user_id
    LEFT JOIN student_info s ON u.user_id = s.user_id
    LEFT JOIN teacher_info t ON u.user_id = t.user_id
    WHERE 1=1
";

$types  = '';
$params = [];

// search by name or any ID
if ($searchUser !== '') {
    $sql .= " AND (
        u.full_name LIKE ?
        OR s.student_id LIKE ?
        OR t.teacher_id LIKE ?
        OR CAST(u.user_id AS CHAR) LIKE ?
    )";
    $like = '%' . $searchUser . '%';
    $types  .= 'ssss';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

// filter by role
if ($roleFilter !== '') {
    $sql .= " AND u.role = ?";
    $types  .= 's';
    $params[] = $roleFilter;
}

// filter by date (only date part of timestamp)
if ($dateFilter !== '') {
    $sql .= " AND DATE(l.timestamp) = ?";
    $types  .= 's';
    $params[] = $dateFilter;
}

$sql .= " ORDER BY l.timestamp DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Query error: " . htmlspecialchars($conn->error));
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Escolink Centra | Activity Logs</title>
  <link rel="stylesheet" href="CSS/format.css">
  <link rel="stylesheet" href="CSS/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
  <div class="portal-layout">
    <!-- Sidebar -->
    <?php include('sidebar_admin.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <div class="search-container">
          <!-- Top search is just visual; real filters are below -->
          <input type="text" placeholder="Search logs..." class="search-bar"
                 onkeypress="if(event.key==='Enter'){document.getElementById('filterUser').value=this.value; document.getElementById('logsFilterForm').submit();}">
          <i class="fa-solid fa-magnifying-glass search-icon"
             onclick="document.getElementById('filterUser').value=document.querySelector('.search-bar').value; document.getElementById('logsFilterForm').submit();"></i>
        </div>

        <div class="profile-section">
          <img src="<?php echo htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8'); ?>" alt="User Avatar" class="avatar">
          <span class="profile-name"><?php echo htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
      </header>

      <!-- Body -->
      <section class="dashboard-body logs-wrapper">
        <h1>Activity Logs</h1>
        <p class="semester-text">Monitor system-wide actions and events across Escolink Centra</p>

        <!-- Filters -->
        <form method="get" id="logsFilterForm" class="logs-filters">
          <div class="filter-group">
            <label for="filterUser">User / ID</label>
            <input type="text" id="filterUser" name="user"
                   placeholder="Search by user name or ID"
                   value="<?php echo htmlspecialchars($searchUser, ENT_QUOTES, 'UTF-8'); ?>">
          </div>

          <div class="filter-group">
            <label for="filterRole">Role</label>
            <select id="filterRole" name="role">
              <option value="">All Roles</option>
              <option value="student" <?php echo ($roleFilter === 'student') ? 'selected' : ''; ?>>Student</option>
              <option value="teacher" <?php echo ($roleFilter === 'teacher') ? 'selected' : ''; ?>>Professor</option>
              <option value="admin"   <?php echo ($roleFilter === 'admin')   ? 'selected' : ''; ?>>Admin</option>
            </select>
          </div>

          <div class="filter-group">
            <label for="filterDate">Date</label>
            <input type="date" id="filterDate" name="date"
                   value="<?php echo htmlspecialchars($dateFilter, ENT_QUOTES, 'UTF-8'); ?>">
          </div>

          <button type="submit" class="apply-filter-btn">
            <i class="fa-solid fa-filter"></i> Apply Filters
          </button>

          <button type="button" class="clear-filter-btn" onclick="clearFilters();">
            <i class="fa-solid fa-rotate-left"></i>
          </button>
        </form>

        <!-- Logs Table -->
        <div class="table-wrapper logs-table-wrapper">
          <table class="logs-table">
            <thead>
              <tr>
                <th>Date &amp; Time</th>
                <th>ID</th>
                <th>User</th>
                <th>Role</th>
                <th>Action</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                  $dt       = $row['timestamp'] ?? '';
                  $id_disp  = $row['display_id'] ?? '';
                  $name     = $row['full_name'] ?? 'Unknown';
                  $role     = $row['role'] ?? 'N/A';
                  $action   = $row['action'] ?? '';
                  $details  = $row['details'] ?? '';
                  $status   = $row['status'] ?? '';
                  $ip       = $row['ip_address'] ?? '';
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($dt, ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($id_disp, ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?></td>
                  <td>
                    <button class="view-log-btn"
                        type="button"
                        data-log-title="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>"
                        data-log-datetime="<?php echo htmlspecialchars($dt, ENT_QUOTES, 'UTF-8'); ?>"
                        data-log-user="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                        data-log-id="<?php echo htmlspecialchars($id_disp, ENT_QUOTES, 'UTF-8'); ?>"
                        data-log-role="<?php echo htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8'); ?>"
                        data-log-details="<?php echo htmlspecialchars($details, ENT_QUOTES, 'UTF-8'); ?>"
                        data-log-status="<?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>"
                        data-log-ip="<?php echo htmlspecialchars($ip, ENT_QUOTES, 'UTF-8'); ?>"
                        onclick="openLogDetails(this)">
                      View
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="empty-state">No activity logs found.</td>
              </tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Export Button (still UI-only, backend export can be added later) -->
        <div class="logs-actions">
          <button class="logs-export-btn" type="button" onclick="openModal('exportLogsModal')">
            <i class="fa-solid fa-file-export"></i> Export Logs
          </button>
        </div>
      </section>
    </main>
  </div>

  <!-- Log Details Modal -->
  <div id="logDetailsModal" class="modal">
    <div class="modal-content user-modal">
      <span class="close" onclick="closeModal('logDetailsModal')">&times;</span>
      <div class="modal-header">
        <i class="fa-solid fa-clipboard-list"></i>
        <h2>Log Details</h2>
      </div>
      <div id="logDetailsBody" class="log-details-body"></div>
      <div class="button-group">
        <button class="cancel-btn" onclick="closeModal('logDetailsModal')">Close</button>
      </div>
    </div>
  </div>

  <!-- Export Logs Modal (UI only for now) -->
  <div id="exportLogsModal" class="logs-modal">
    <div class="logs-modal-content">
      <span class="logs-modal-close" onclick="closeModal('exportLogsModal')">&times;</span>
      <div class="logs-modal-header">
        <i class="fa-solid fa-file-export"></i>
        <h2>Export Logs</h2>
      </div>
      <p class="logs-modal-desc">
        Choose the format and data range for exporting your system logs.
      </p>
      <form class="logs-export-form" onsubmit="event.preventDefault(); showPopup('Export backend not implemented yet.'); closeModal('exportLogsModal');">
        <label for="format">Select Format</label>
        <select id="format">
          <option value="pdf">PDF</option>
          <option value="csv">CSV</option>
          <option value="xlsx">Excel (.xlsx)</option>
        </select>

        <label for="range">Date Range</label>
        <input type="date" id="range-start"> — <input type="date" id="range-end">

        <div class="logs-button-group">
          <button type="submit" class="logs-export-confirm-btn">
            <i class="fa-solid fa-download"></i> Export
          </button>
          <button type="button" class="logs-export-cancel-btn" onclick="closeModal('exportLogsModal')">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Success Popup -->
  <div id="successPopup" class="success-popup">
    <i class="fa-solid fa-circle-check"></i>
    <span id="popupText">Action successful.</span>
  </div>

  <!-- JS Section -->
  <script>
    function openModal(id) {
      const m = document.getElementById(id);
      if (m) m.style.display = "flex";
    }

    function closeModal(id) {
      const m = document.getElementById(id);
      if (m) m.style.display = "none";
    }

    function showPopup(msg) {
      const popup = document.getElementById("successPopup");
      const text = document.getElementById("popupText");
      if (!popup || !text) return;
      text.innerText = msg;
      popup.style.display = "flex";
      popup.style.opacity = "1";
      setTimeout(() => {
        popup.style.opacity = "0";
        setTimeout(() => popup.style.display = "none", 400);
      }, 2000);
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    function openLogDetails(button) {
      const title    = button.getAttribute('data-log-title')   || 'Log Details';
      const datetime = button.getAttribute('data-log-datetime')|| '';
      const user     = button.getAttribute('data-log-user')    || '';
      const id       = button.getAttribute('data-log-id')      || '';
      const role     = button.getAttribute('data-log-role')    || '';
      const details  = button.getAttribute('data-log-details') || '';
      const status   = button.getAttribute('data-log-status')  || '';
      const ip       = button.getAttribute('data-log-ip')      || '';

      const body = document.getElementById("logDetailsBody");
      if (!body) return;

      body.innerHTML = `
        <p><strong>Title:</strong> ${escapeHtml(title)}</p>
        <p><strong>Date &amp; Time:</strong> ${escapeHtml(datetime)}</p>
        <p><strong>User:</strong> ${escapeHtml(user)}</p>
        <p><strong>ID:</strong> ${escapeHtml(id)}</p>
        <p><strong>Role:</strong> ${escapeHtml(role)}</p>
        <p><strong>Status:</strong> ${escapeHtml(status)}</p>
        <p><strong>IP Address:</strong> ${escapeHtml(ip)}</p>
        <p><strong>Details:</strong> ${escapeHtml(details)}</p>
      `;
      openModal('logDetailsModal');
    }

    function clearFilters() {
      // reset filters by reloading page without query string
      window.location.href = "logs_admin.php";
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
      document.querySelectorAll(".modal, .logs-modal").forEach(m => {
        if (event.target === m) m.style.display = "none";
      });
    };
  </script>
</body>
</html>
<?php
// free result
if ($result instanceof mysqli_result) {
    $result->free();
}
?>
