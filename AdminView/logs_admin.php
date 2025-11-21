<?php
include("../includes/auth_session.php");
include("../includes/auth_admin.php");
include("../config/db_connect.php");

// ================== HEADER USER INFO ==================
$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'System Administrator';

// default avatar
$avatar = "images/ProfileImg.png";

$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE user_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($profile_pic);
    if ($stmt->fetch() && !empty($profile_pic)) {
        $avatar = "../uploads/" . $profile_pic;
    }
    $stmt->close();
}

// ================== FILTERS ==================
$searchUser = trim($_GET['user'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');
$dateFilter = trim($_GET['date'] ?? '');

// ================== PAGINATION ==================
$perPage = 12;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

// ================== BASE QUERY ==================
$base_sql = "
    FROM activity_logs l
    LEFT JOIN users u       ON l.user_id = u.user_id
    LEFT JOIN student_info s ON u.user_id = s.user_id
    LEFT JOIN teacher_info t ON u.user_id = t.user_id
    WHERE 1=1
";

$types  = "";
$params = [];

// search
if ($searchUser !== "") {
    $base_sql .= " AND (
        u.full_name LIKE ?
        OR s.student_id LIKE ?
        OR t.teacher_id LIKE ?
        OR CAST(u.user_id AS CHAR) LIKE ?
    )";
    $like = "%$searchUser%";
    $types .= "ssss";
    array_push($params, $like, $like, $like, $like);
}

// role
if ($roleFilter !== "") {
    $base_sql .= " AND u.role = ?";
    $types .= "s";
    $params[] = $roleFilter;
}

// date filter
if ($dateFilter !== "") {
    $base_sql .= " AND DATE(l.timestamp) = ?";
    $types .= "s";
    $params[] = $dateFilter;
}

// ================== COUNT TOTAL ==================
$sql_count = "SELECT COUNT(*) AS total " . $base_sql;
$stmt = $conn->prepare($sql_count);

if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = max(1, ceil($total / $perPage));

// ================== FETCH LOGS (PAGINATED) ==================
$sql_logs = "
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
    " . $base_sql . "
    ORDER BY l.timestamp DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql_logs);

if ($types !== "") {
    $bindTypes = $types . "ii";
    $bindParams = array_merge($params, [$perPage, $offset]);
    $stmt->bind_param($bindTypes, ...$bindParams);
} else {
    $stmt->bind_param("ii", $perPage, $offset);
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

    <?php include('sidebar_admin.php'); ?>

    <main class="main-content">

      <!-- Topbar (REMOVED SEARCH BAR) -->
      <header class="topbar">
        <div></div>
        <div class="profile-section">
          <img src="<?= htmlspecialchars($avatar) ?>" class="avatar">
          <span class="profile-name"><?= htmlspecialchars($full_name) ?></span>
        </div>
      </header>

      <section class="dashboard-body logs-wrapper">
        <h1>Activity Logs</h1>
        <p class="semester-text">Monitor system-wide actions and events across Escolink Centra</p>

        <!-- Filters -->
        <form method="get" id="logsFilterForm" class="logs-filters">
          <div class="filter-group">
            <label>User / ID</label>
            <input type="text" name="user" value="<?= htmlspecialchars($searchUser) ?>" placeholder="Search">
          </div>

          <div class="filter-group">
            <label>Role</label>
            <select name="role">
              <option value="">All</option>
              <option value="student" <?= $roleFilter==="student"?"selected":"" ?>>Student</option>
              <option value="teacher" <?= $roleFilter==="teacher"?"selected":"" ?>>Professor</option>
              <option value="admin"   <?= $roleFilter==="admin"  ?"selected":"" ?>>Admin</option>
            </select>
          </div>

          <div class="filter-group">
            <label>Date</label>
            <input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>">
          </div>

          <button type="submit" class="apply-filter-btn">
            <i class="fa-solid fa-filter"></i> Apply Filters
          </button>

          <button type="button" class="clear-filter-btn"
                  onclick="window.location='logs_admin.php'">
            <i class="fa-solid fa-rotate-left"></i>
          </button>
        </form>

        <!-- Logs Table -->
        <div class="table-wrapper logs-table-wrapper">
          <table class="logs-table">
            <thead>
              <tr>
                <th>Date & Time</th>
                <th>ID</th>
                <th>User</th>
                <th>Role</th>
                <th>Action</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['timestamp']) ?></td>
                  <td><?= htmlspecialchars($row['display_id']) ?></td>
                  <td><?= htmlspecialchars($row['full_name']) ?></td>
                  <td><?= htmlspecialchars(ucfirst($row['role'])) ?></td>
                  <td><?= htmlspecialchars($row['action']) ?></td>
                  <td>
                    <button class="view-log-btn"
                        type="button"
                        data-log-title="<?= htmlspecialchars($row['action']) ?>"
                        data-log-datetime="<?= htmlspecialchars($row['timestamp']) ?>"
                        data-log-user="<?= htmlspecialchars($row['full_name']) ?>"
                        data-log-id="<?= htmlspecialchars($row['display_id']) ?>"
                        data-log-role="<?= htmlspecialchars(ucfirst($row['role'])) ?>"
                        data-log-details="<?= htmlspecialchars($row['details']) ?>"
                        data-log-status="<?= htmlspecialchars($row['status']) ?>"
                        data-log-ip="<?= htmlspecialchars($row['ip_address']) ?>"
                        onclick="openLogDetails(this)">
                      View
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6" class="empty-state">No logs found.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

      </section>

    </main>

  </div>

  <!-- Log Details Modal (unchanged) -->
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


  <!-- ====================== PAGINATION (STICKY) ====================== -->
  <div class="pagination-bar">
    <div class="pagination-inner">

      <?php $prev = $page - 1; $next = $page + 1; ?>

      <!-- Previous -->
      <span class="<?= ($page <= 1) ? 'disabled' : '' ?>">
        <?php if ($page > 1): ?>
          <a href="<?= build_query(['page'=>$prev]) ?>">&laquo;</a>
        <?php else: ?>&laquo;<?php endif; ?>
      </span>

      <!-- Pages -->
      <?php for ($i=1; $i<=$totalPages; $i++): ?>
        <?php if ($i == $page): ?>
          <span class="current-page"><?= $i ?></span>
        <?php else: ?>
          <a href="<?= build_query(['page'=>$i]) ?>"><?= $i ?></a>
        <?php endif; ?>
      <?php endfor; ?>

      <!-- Next -->
      <span class="<?= ($page >= $totalPages) ? 'disabled' : '' ?>">
        <?php if ($page < $totalPages): ?>
          <a href="<?= build_query(['page'=>$next]) ?>">&raquo;</a>
        <?php else: ?>&raquo;<?php endif; ?>
      </span>

    </div>
  </div>


  <script>
    function openModal(id){document.getElementById(id).style.display="flex";}
    function closeModal(id){document.getElementById(id).style.display="none";}
    function escapeHtml(s){return s.replace(/[&<>"']/g,m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m]));}

    function openLogDetails(btn){
      const b = document.getElementById("logDetailsBody");
      b.innerHTML = `
        <p><strong>Action:</strong> ${escapeHtml(btn.dataset.logTitle)}</p>
        <p><strong>Date & Time:</strong> ${escapeHtml(btn.dataset.logDatetime)}</p>
        <p><strong>User:</strong> ${escapeHtml(btn.dataset.logUser)}</p>
        <p><strong>ID:</strong> ${escapeHtml(btn.dataset.logId)}</p>
        <p><strong>Role:</strong> ${escapeHtml(btn.dataset.logRole)}</p>
        <p><strong>Status:</strong> ${escapeHtml(btn.dataset.logStatus)}</p>
        <p><strong>IP:</strong> ${escapeHtml(btn.dataset.logIp)}</p>
        <p><strong>Details:</strong> ${escapeHtml(btn.dataset.logDetails)}</p>
      `;
      openModal('logDetailsModal');
    }
  </script>

</body>
</html>

<?php
if ($result instanceof mysqli_result) $result->free();

function build_query(array $overrides=[]){
    $params=$_GET;
    foreach($overrides as $k=>$v){
        if($v===null) unset($params[$k]);
        else $params[$k]=$v;
    }
    $q=http_build_query($params);
    return $q ? ("?$q") : "";
}
?>
